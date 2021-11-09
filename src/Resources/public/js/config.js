pimcore.registerNS("pimcore.plugin.learning.management.framework.config");
pimcore.plugin.learning.management.framework.config = Class.create({
    initialize: function () {
        this.getTabPanel();
    },

    activate: function () {
        let tabPanel = Ext.getCmp("pimcore_panel_tabs");
        tabPanel.setActiveItem(this.getTabPanel());
    },

    getTabPanel: function () {
        if (!this.panel) {
            this.panel = new Ext.Panel({
                id: "plugin_pimcore_learning_management_framework_config_tab",
                title: t("plugin_pimcore_learning_management_framework_config_toolbar"),
                icon: "/bundles/pimcorelearningmanagementframework/img/school_white_24dp.svg",
                border: false,
                layout: "border",
                closable: true,
                items: [ this.getTree(), this.getEditPanel() ]
            });

            var tabPanel = Ext.getCmp("pimcore_panel_tabs");
            tabPanel.add(this.panel);
            tabPanel.setActiveItem("plugin_pimcore_learning_management_framework_config_tab");

            this.panel.on("destroy", function () {
                pimcore.globalmanager.remove("plugin_pimcore_learning_management_framework_config");
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
                region: "west",
                autoScroll: true,
                animate: true,
                containerScroll: true,
                width: 300,
                title: t("plugin_pimcore_learning_management_framework_config_students"),
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
        let store = Ext.create('Ext.data.TreeStore', {
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
        let tab = new Ext.TabPanel({
            store: store,
            activeTab: 0,
            title: record.data.text,
            icon: '/bundles/pimcorelearningmanagementframework/img/person_black_24dp.svg',
            closable: true,
            deferredRender: false,
            forceLayout: true,
            buttons: {
                componentCls: 'plugin_pimcore_datahub_statusbar',
                itemId: 'footer'
            },
        });
        tab.setActiveTab(0);

        this.editPanel.add(tab);
        this.editPanel.setActiveTab(tab);
        this.editPanel.updateLayout();
    },

    getEditPanel: function () {
        if (!this.editPanel) {
            this.editPanel = new Ext.TabPanel({
                region: "center"
            });
        }

        return this.editPanel;
    },
});