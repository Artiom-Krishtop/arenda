<?php

use Bitrix\Main\Config\Option;
use Bitrix\Main\Localization\Loc,
    Bitrix\Main\ModuleManager;

Loc::loadMessages(__FILE__);

class travelsoft_bcimport extends CModule
{
    public $MODULE_ID = "travelsoft.bcimport";
    public $MODULE_VERSION;
    public $MODULE_VERSION_DATE;
    public $MODULE_NAME;
    public $MODULE_DESCRIPTION;

    function __construct()
    {
        $arModuleVersion = array();
        $path = dirname(__FILE__);

        include($path . "/version.php");

        if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion)) {
            $this->MODULE_VERSION = $arModuleVersion["VERSION"];
            $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        }
        $this->MODULE_NAME = Loc::getMessage("TRAVELSOFT_MODULE_NAME");
        $this->MODULE_DESCRIPTION = Loc::getMessage("TRAVELSOFT_MODULE_DESC");
        $this->PARTNER_NAME = Loc::getMessage("TRAVELSOFT_COMPANY");
        $this->PARTNER_URI = "http://travelsoft.by/";

        set_time_limit(0);
    }

    public function DoInstall()
    {
        global $DOCUMENT_ROOT, $APPLICATION;
        $this->addOptions();

        ModuleManager::registerModule($this->MODULE_ID);
        $APPLICATION->IncludeAdminFile("Установка модуля {$this->MODULE_ID}", $DOCUMENT_ROOT . "/local/modules/travelsoft.bcimport/install/step.php");
    }

    public function DoUninstall()
    {
        global $DOCUMENT_ROOT, $APPLICATION;
        $this->deleteOptions();

        ModuleManager::unRegisterModule($this->MODULE_ID);
        $APPLICATION->IncludeAdminFile("Деинсталляция модуля {$this->MODULE_ID}", $DOCUMENT_ROOT . "/local/modules/travelsoft.bcimport/install/unstep.php");
    }

    public function addOptions()
    {
        try {
            Option::set($this->MODULE_ID, "API_URL");
            Option::set($this->MODULE_ID, "CITY_STORE_ID");
        } catch (\Bitrix\Main\ArgumentOutOfRangeException $e) {
        }
    }

    public function deleteOptions()
    {
        try {
            Option::delete($this->MODULE_ID, array("name" => "API_URL"));
            Option::delete($this->MODULE_ID, array("name" => "CITY_STORE_ID"));
        } catch (\Bitrix\Main\ArgumentNullException $e) {
        }
    }
}
