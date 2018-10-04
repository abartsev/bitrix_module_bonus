<?php

IncludeModuleLangFile(__FILE__);

class bonusdoweb extends CModule
{

    const moduleClass = 'BonusDoweb';
    const moduleClassEvents = 'BonusDowebEvents';
    const solutionName  = 'bonusdoweb';

    var $MODULE_ID = "bonusdoweb";
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $MODULE_CSS;
    var $MODULE_GROUP_RIGHTS = 'R';

    function bonusdoweb()
    {
    $arModuleVersion = array();

    $path = str_replace("\\", "/", __FILE__);
    $path = substr($path, 0, strlen($path) - strlen("/index.php"));
    include($path."/version.php");

    if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion))
    {
    $this->MODULE_VERSION = $arModuleVersion["VERSION"];
    $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
    }

    $this->MODULE_NAME = "bonus_doweb – модуль с компонентом";
    $this->MODULE_DESCRIPTION = "После установки вы сможете пользоваться компонентом doweb:bonus.doweb";
    }
    function InstallDB($install_wizard = true){
        global $DB, $DBType, $APPLICATION;

        RegisterModule($this->MODULE_ID);
        COption::SetOptionString($this->MODULE_ID, 'GROUP_DEFAULT_RIGHT', $this->MODULE_GROUP_RIGHTS);

        if(preg_match('/.bitrixlabs.ru/', $_SERVER['HTTP_HOST'])){
            RegisterModuleDependences('main', 'OnBeforeProlog', $this->MODULE_ID, self::moduleClass, 'correctInstall');
        }

        if(CModule::IncludeModule($this->MODULE_ID)){
            $moduleClass = self::moduleClass;
            $instance = new $moduleClass();
            $instance::sendBonusBIAction('install');
        }

        return true;
    }

    function UnInstallDB($arParams = array()){
        global $DB, $DBType, $APPLICATION;

        if(CModule::IncludeModule($this->MODULE_ID)){
            $moduleClass = self::moduleClass;
            $instance = new $moduleClass();
            $instance::sendAsproBIAction('delete');
        }

        COption::RemoveOption($this->MODULE_ID, 'GROUP_DEFAULT_RIGHT');
        UnRegisterModule($this->MODULE_ID);

        return true;
    }
     function InstallEvents()
    {
        include_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/bonusdoweb/install/events/install.php");
        return true;
    }
    function UnInstallEvents()
    {
        include_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/bonusdoweb/install/events/uninstall.php");
        return true;
    }
    function InstallFiles()
    {
        CopyDirFiles(__DIR__.'/admin/', $_SERVER["DOCUMENT_ROOT"].'/bitrix/admin', true);
    CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/bonusdoweb/install/components",
                 $_SERVER["DOCUMENT_ROOT"]."/bitrix/components", true, true);
    CopyDirFiles(__DIR__.'/css/', $_SERVER['DOCUMENT_ROOT'].'/bitrix/css/'.self::solutionName, true, true);
    CopyDirFiles(__DIR__.'/images/', $_SERVER['DOCUMENT_ROOT'].'/bitrix/images/'.self::solutionName, true, true);
    return true;
    }

    function UnInstallFiles()
    {
    DeleteDirFiles(__DIR__.'/admin/', $_SERVER['DOCUMENT_ROOT'].'/bitrix/admin');
    DeleteDirFilesEx("/bitrix/components/doweb");
    DeleteDirFilesEx('/bitrix/css/'.self::solutionName.'/');
    DeleteDirFilesEx('/bitrix/images/'.self::solutionName.'/');
    return true;
    }

    function DoInstall()
    {
    global $DOCUMENT_ROOT, $APPLICATION;
    $this->InstallFiles();
    RegisterModuleDependences("main", "OnAfterUserRegister", "bonusdoweb", "SaleUser", "OnAfterUserRegisterHandler");
    RegisterModuleDependences("main", "OnAfterUserUpdate", "bonusdoweb", "SaleUser", "OnAfterUserUpdateHandler");
    RegisterModule("bonusdoweb");
    $APPLICATION->IncludeAdminFile("Установка модуля bonus.doweb", $DOCUMENT_ROOT."/bitrix/modules/bonusdoweb/install/step.php");
    }

    function DoUninstall()
    {
    global $DOCUMENT_ROOT, $APPLICATION;
    $this->UnInstallFiles();
    UnRegisterModuleDependences("main", "OnAfterUserRegister", "bonusdoweb", "SaleUser", "OnAfterUserRegisterHandler");
    UnRegisterModuleDependences("main", "OnAfterUserUpdate", "bonusdoweb", "SaleUser", "OnAfterUserUpdateHandler");
    UnRegisterModule("bonusdoweb");
    $APPLICATION->IncludeAdminFile("Деинсталляция модуля bonus.doweb", $DOCUMENT_ROOT."/bitrix/modules/bonusdoweb/install/unstep.php");
    }

}