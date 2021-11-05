pimcore.registerNS("pimcore.plugin.learning.management.framework");

pimcore.plugin.learning_management_framework = Class.create(pimcore.plugin.admin, {
    getClassName: function() {
        return "pimcore.plugin.learning.management.framework";
    },

    initialize: function() {
        pimcore.plugin.broker.registerPlugin(this);
        this.navEl = Ext.get('pimcore_menu_search').insertSibling(`
            <li id="pimcore_menu_learning_management_framework" data-menu-tooltip="Learning Management" class="pimcore_menu_item">
                <a href="`+window.location.origin+`" target="_blank" style="position: absolute;width: 24px;height: 24px;top: 13px;left: 18px;">
                    <img src="/bundles/pimcorelearningmanagementframework/img/school_white_24dp.svg" alt="Learning Management">
                </a>
            </li>`
        , 'before');
    }
});

var LearningManagementFrameworkPlugin = new pimcore.plugin.learning_management_framework();
