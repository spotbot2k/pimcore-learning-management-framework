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
                items: [ this.getTree() ]
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
            var store = Ext.create('Ext.data.TreeStore', {
                autoLoad: false,
                autoSync: true,
                proxy: {
                    type: 'ajax',
                    url: '/admin/pimcoredatahub/config/list',
                    reader: {
                        type: 'json'
                    }
                }
            });

            let menuItems = [];

            let firstHandler;

            for (var key in pimcore.plugin.datahub.adapter) {
                if( pimcore.plugin.datahub.adapter.hasOwnProperty( key ) ) {

                    let adapter = new pimcore.plugin.datahub.adapter[key](this);

                    if (!firstHandler) {
                        firstHandler = adapter.addConfiguration.bind(adapter, key);
                    }
                    menuItems.push(
                    {
                        text: t('plugin_pimcore_datahub_type_' + key),
                        iconCls: "plugin_pimcore_datahub_icon_" + key,
                        handler: adapter.addConfiguration.bind(adapter, key)
                    });
                }
            }

            var addConfigButton = new Ext.SplitButton({
                text: t("plugin_pimcore_datahub_configpanel_add"),
                iconCls: "pimcore_icon_add",
                handler: firstHandler,
                menu: menuItems
            });


            this.tree = new Ext.tree.TreePanel({
                store: store,
                region: "west",
                useArrows: true,
                autoScroll: true,
                animate: true,
                containerScroll: true,
                border: true,
                width: 200,
                split: true,
                root: {
                    id: '0',
                    expanded: true,
                    iconCls: "pimcore_icon_thumbnails"
                },
                rootVisible: false,
                tbar: {
                    items: [
                        addConfigButton
                    ]
                },
                listeners: {
                    itemclick: this.onTreeNodeClick.bind(this),
                    itemcontextmenu: this.onTreeNodeContextmenu.bind(this),
                    render: function () {
                        this.getRootNode().expand()
                    }
                }
            });
        }

        return this.tree;
    },
});