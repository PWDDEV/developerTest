<?

class br_base extends CModule {

    var $MODULE_ID = "br.base";
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;

    /**
     * Module installator constructor
     */
    function br_base() {
        $arModuleVersion = array();

        include(__DIR__ . "/version.php");

        $this->MODULE_VERSION = $arModuleVersion["VERSION"];
        $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];

        $this->MODULE_NAME = 'Br Base';
        $this->MODULE_DESCRIPTION = 'Br Base';

        $this->PARTNER_NAME = "Br";
        $this->PARTNER_URI = "https://vk.com/romani_nsk";
    }

    /**
     * Do install module
     * @return bool
     */
    function DoInstall() {
        if (IsModuleInstalled("br.base")) {
            return false;
        }

        if (!check_bitrix_sessid()) {
            return false;
        }

        RegisterModule("br.base");
        LocalRedirect("/bitrix/admin/partner_modules.php");
        return true;
    }

    /**
     * Uninstall module
     * @return bool
     */
    function DoUninstall() {
        UnRegisterModule("br.base");
        LocalRedirect("/bitrix/admin/partner_modules.php");
        return true;
    }
}
