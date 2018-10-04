<?php

$module_id = 'bonusdoweb';

global $APPLICATION, $IdUser;
IncludeModuleLangFile(__FILE__);
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

$arClassesList = array(
        "Tools" => "lib/tools.php",
        "SaleUser" => "classes/general/SaleUser.php",
        "BonusDoweb" => "classes/general/BonusDoweb.php"
        
);


CModule::AddAutoloadClasses(
        "bonusdoweb",
        $arClassesList
);


CJSCore::RegisterExt('jq_chosen', array(
        'js' => array(),
        'css' => array(),
        'rel' => array()
));
CJSCore::RegisterExt('add_js_css', array(
        'js' => array(),
        'css' => array(),
        'rel' => array()
));
