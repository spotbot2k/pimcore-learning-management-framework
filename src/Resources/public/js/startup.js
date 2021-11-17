pimcore.registerNS("pimcore.plugin.learning.management.framework");

pimcore.plugin.learning_management_framework = Class.create(pimcore.plugin.admin, {
    getClassName: function() {
        return "pimcore.plugin.learning.management.framework";
    },

    initialize: function() {
        pimcore.plugin.broker.registerPlugin(this);
    },

    pimcoreReady: function (params, broker) {
        var user = pimcore.globalmanager.get("user");
        if (user.admin || user.isAllowed("plugin_lmf_manage") || user.isAllowed("plugin_lmf_view")) {
            let navEl = Ext.get('pimcore_menu_search').insertSibling(`
                <li id="pimcore_menu_learning_management_framework" data-menu-tooltip="${t('plugin_pimcore_learning_management_framework_config_toolbar')}" class="pimcore_menu_item">
                    <img src="/bundles/pimcorelearningmanagementframework/img/school.svg">
                </li>`
            , 'before');

            navEl.on('mousedown', function () {
                try {
                    pimcore.globalmanager.get("plugin_pimcore_learning_management_framework_config").activate();
                } catch (e) {
                    pimcore.globalmanager.add("plugin_pimcore_learning_management_framework_config", new pimcore.plugin.learning.management.framework.config());
                }
            });

            pimcore.helpers.initMenuTooltips();
        }
    }
});

var LearningManagementFrameworkPlugin = new pimcore.plugin.learning_management_framework();
