pimcore.registerNS('pimcore.plugin.learning.management.framework.config');
pimcore.plugin.learning.management.framework.config = Class.create({
    initialize: function () {
        this.getTabPanel();
    },

    activate: function () {
        let tabPanel = Ext.getCmp('pimcore_panel_tabs');
        tabPanel.setActiveItem(this.getTabPanel());
    },

    getTree: function () {
        if (!this.tree) {
            let store = Ext.create('Ext.data.TreeStore', {
                autoLoad: false,
                autoSync: false,
                proxy: {
                    type: 'ajax',
                    url: '/admin/lmf/students',
                    reader: {  type: 'json' }
                },
                fields: [
                    'id',
                    'text',
                    { name: 'icon', defaultValue: '/bundles/pimcorelearningmanagementframework/img/person_black_24dp.svg' }
                ]
            });

            let refreshTreeButton = new Ext.Button({
                text: t('plugin_pimcore_learning_management_framework_action_reload'),
                iconCls: "pimcore_icon_reload",
                handler: function() {
                    this.tree.store.getRootNode().removeAll();
                    this.tree.store.load();
                }.bind(this)
            });

            this.tree = new Ext.tree.TreePanel({
                store: store,
                region: 'west',
                autoScroll: true,
                animate: true,
                containerScroll: true,
                width: 300,
                title: t('plugin_pimcore_learning_management_framework_config_students'),
                root: {
                    id: '0',
                    expanded: true
                },
                rootVisible: false,
                listeners: {
                    itemclick: this.onTreeNodeClick.bind(this),
                    itemcontextmenu: this.onStudentContextmenu.bind(this),
                    render: function () {
                        this.getRootNode().expand()
                    }
                },
                bbar: [ refreshTreeButton ]
            });
        }

        window.tree = this.tree;

        return this.tree;
    },

    getEditPanel: function () {
        if (!this.editPanel) {
            this.editPanel = new Ext.TabPanel({
                region: 'center'
            });
        }

        return this.editPanel;
    },

    getTabPanel: function () {
        if (!this.panel) {
            this.panel = new Ext.Panel({
                id: 'plugin_pimcore_learning_management_framework_config_tab',
                title: t('plugin_pimcore_learning_management_framework_config_toolbar'),
                icon: '/bundles/pimcorelearningmanagementframework/img/school_white_24dp.svg',
                border: false,
                layout: 'border',
                closable: true,
                items: [
                    this.getTree(),
                    this.getEditPanel()
                ]
            });

            let tabPanel = Ext.getCmp('pimcore_panel_tabs');
            tabPanel.add(this.panel);
            tabPanel.setActiveItem('plugin_pimcore_learning_management_framework_config_tab');

            this.panel.on('destroy', function () {
                pimcore.globalmanager.remove('plugin_pimcore_learning_management_framework_config');
            }.bind(this));

            pimcore.layout.refresh();
        }

        return this.panel;
    },

    onTreeNodeClick: function (tree, record, item, index, e, eOpts) {
        let store = Ext.create('Ext.data.Store', {
            autoLoad: true,
            autoSync: false,
            proxy: {
                type: 'ajax',
                url: '/admin/lmf/student/' + record.data.id,
                reader: {
                    type: 'json'
                }
            }
        });
        let openExamButton = new Ext.Button({
            iconCls: "pimcore_icon_open",
            handler: function(record) {
                pimcore.helpers.openObject(record.data.id, 'object');
            }.bind(this, record)
        });
        let refreshTabButton = new Ext.Button({
            iconCls: "pimcore_icon_reload",
            handler: function(store) {
                store.reload();
            }.bind(this, store)
        });
        let grid = new Ext.grid.Panel({
            frame: false,
            autoScroll: true,
            stripeRows: true,
            forceFit:  true,
            trackMouseOver: true,
            columnLines: true,
            store: store,
            listeners: {
                itemcontextmenu: this.onExamRowContextmenu.bind(this),
            },
            columns: [
                { text: t('plugin_pimcore_learning_management_framework_column_examTitle'), dataIndex: 'title' },
                { text: t('plugin_pimcore_learning_management_framework_column_attempts_active'), dataIndex: 'attemptsActive' },
                { text: t('plugin_pimcore_learning_management_framework_column_attempts_total'), dataIndex: 'attemptsTotal' },
                { text: t('plugin_pimcore_learning_management_framework_column_lastAttempt'), dataIndex: 'lastAttempt' },
                { text: t('plugin_pimcore_learning_management_framework_column_passed'), dataIndex: 'passed' },
                { text: t('plugin_pimcore_learning_management_framework_column_bestTime'), dataIndex: 'bestTime' },
                { text: t('plugin_pimcore_learning_management_framework_column_bestRatio'), dataIndex: 'bestRatio' },
                { text: t('plugin_pimcore_learning_management_framework_column_latestGrade'), dataIndex: 'latestGrade' }
            ],
            tbar: [ openExamButton, refreshTabButton ]
        });
        let tab = new Ext.Panel({
            title: record.data.text,
            icon: '/bundles/pimcorelearningmanagementframework/img/person_black_24dp.svg',
            closable: true,
            layout: 'fit',
            items: [ grid ],
            buttons: {
                componentCls: 'plugin_pimcore_datahub_statusbar',
                itemId: 'footer'
            },
        });

        this.editPanel.add(tab);
        this.editPanel.setActiveTab(tab);
        this.editPanel.updateLayout();
    },

    onExamRowContextmenu: function (grid, record, item, index, e, eOpts) {
        e.stopEvent();
        grid.select();

        let menu = new Ext.menu.Menu();

        menu.add(new Ext.menu.Item({
            text: t('plugin_pimcore_learning_management_framework_action_open_exam'),
            iconCls: 'pimcore_icon_open',
            handler: function (grid, record) {
                pimcore.helpers.openObject(record.data.examId, 'object');
            }.bind(this, grid, record)
        }));
        menu.add(new Ext.menu.Item({
            text: t('plugin_pimcore_learning_management_framework_action_reset_attempts'),
            iconCls: 'pimcore_icon_unpublish',
            handler: function (grid, record) {
                Ext.Ajax.request({
                    url: "/admin/lmf/exam/reset-attempts",
                    method: 'POST',
                    params: {
                        examId: record.data.examId,
                        studentId: record.data.studentId
                    },
                    success: function (response) {
                        grid.store.reload();
                    }.bind(this)
                });
            }.bind(this, grid, record)
        }));

        menu.showAt(e.pageX, e.pageY);
    },

    onStudentContextmenu: function (tree, record, item, index, e, eOpts) {
        e.stopEvent();
        tree.select();

        let menu = new Ext.menu.Menu();
        menu.add(new Ext.menu.Item({
            text: t('plugin_pimcore_learning_management_framework_action_open_student'),
            iconCls: 'pimcore_icon_open',
            handler: function (tree, record) {
                pimcore.helpers.openObject(record.data.id, 'object');
            }.bind(this, tree, record)
        }));

        menu.showAt(e.pageX, e.pageY);
    }
});