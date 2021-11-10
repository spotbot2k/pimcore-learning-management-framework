pimcore.registerNS('pimcore.plugin.learning.management.framework.config');
pimcore.plugin.learning.management.framework.config = Class.create({
    initialize: function () {
        this.getTabPanel();
    },

    activate: function () {
        let tabPanel = Ext.getCmp('pimcore_panel_tabs');
        tabPanel.setActiveItem(this.getTabPanel());
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
                items: [ this.getTree(), this.getEditPanel() ]
            });

            var tabPanel = Ext.getCmp('pimcore_panel_tabs');
            tabPanel.add(this.panel);
            tabPanel.setActiveItem('plugin_pimcore_learning_management_framework_config_tab');

            this.panel.on('destroy', function () {
                pimcore.globalmanager.remove('plugin_pimcore_learning_management_framework_config');
            }.bind(this));

            pimcore.layout.refresh();
        }

        return this.panel;
    },

    getTree: function () {
        if (!this.tree) {
            let store = Ext.create('Ext.data.TreeStore', {
                autoLoad: false,
                autoSync: false,
                proxy: {
                    type: 'ajax',
                    url: '/admin/lmf/students',
                    reader: {
                        type: 'json'
                    }
                },
                fields: [
                    'id',
                    'text',
                    { name: 'icon', defaultValue: '/bundles/pimcorelearningmanagementframework/img/person_black_24dp.svg' }
                ]
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
                    render: function () {
                        this.getRootNode().expand()
                    }
                }
            });
        }

        return this.tree;
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
        let grid = new Ext.grid.Panel({
            frame: false,
            autoScroll: true,
            stripeRows: true,
            forceFit:  true,
            trackMouseOver: true,
            columnLines: true,
            store: store,
            columns: [
                { text: t('plugin_pimcore_learning_management_framework_column_examTitle'), dataIndex: 'title' },
                { text: t('plugin_pimcore_learning_management_framework_column_attempts'), dataIndex: 'attempts' },
                { text: t('plugin_pimcore_learning_management_framework_column_lastAttempt'), dataIndex: 'lastAttempt' },
                { text: t('plugin_pimcore_learning_management_framework_column_passed'), dataIndex: 'passed' },
                { text: t('plugin_pimcore_learning_management_framework_column_bestTime'), dataIndex: 'bestTime' },
                { text: t('plugin_pimcore_learning_management_framework_column_bestRatio'), dataIndex: 'bestRatio' },
                { text: t('plugin_pimcore_learning_management_framework_column_latestGrade'), dataIndex: 'latestGrade' },
                // {
                //     align: 'center',
                //     xtype: 'actioncolumn',
                //     items: [
                //         {
                //            xtype: 'button',
                //            tooltip: t('plugin_pimcore_learning_management_framework_action_revoke'),
                //            icon: '/bundles/pimcoreadmin/img/flat-color-icons/overlay-delete.svg',
                //            handler: function() {
                //            }
                //         }
                //     ]
                // }
            ]
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

    getEditPanel: function () {
        if (!this.editPanel) {
            this.editPanel = new Ext.TabPanel({
                region: 'center'
            });
        }

        return this.editPanel;
    },
});