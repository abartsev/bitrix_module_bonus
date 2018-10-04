
<?
AddEventHandler('main', 'OnBuildGlobalMenu', 'OnBuildGlobalMenuHandlerDigital');
function OnBuildGlobalMenuHandlerDigital(&$arGlobalMenu, &$arModuleMenu){
        if(!defined('BONUSDOWEB_MENU_INCLUDED')){
                define('BONUSDOWEB_MENU_INCLUDED', true);

                IncludeModuleLangFile(__FILE__);
                $moduleID = 'bonusdoweb';

                $GLOBALS['APPLICATION']->SetAdditionalCss("/bitrix/css/".$moduleID."/menu.css");

                if($GLOBALS['APPLICATION']->GetGroupRight($moduleID) >= 'R'){
                        $arMenu = array(
                                'menu_id' => 'global_menu_bonusdoweb',
                                'text' => GetMessage('BONUSDOWEB_GLOBAL_MENU_TEXT'),
                                'title' => GetMessage('BONUSDOWEB_GLOBAL_MENU_TITLE'),
                                'sort' => 1000,
                                'items_id' => 'global_menu_bonusdoweb_items',
                                'items' => array(
                                        array(
                                                'text' => GetMessage('BONUSDOWEB_MENU_TYPOGRAPHY_TEXT'),
                                                'title' => GetMessage('BONUSDOWEB_MENU_TYPOGRAPHY_TITLE'),
                                                'sort' => 30,
                                                'url' => '/bitrix/admin/'.$moduleID.'_options.php?mid=main',
                                                'icon' => 'imi_typography',
                                                'page_icon' => 'pi_typography',
                                                'items_id' => 'main',
                                        ),
                                         array(
                                                'text' => GetMessage('BONUSDOWEB_MENU_ORDER_TEXT'),
                                                'title' => GetMessage('BONUSDOWEB_MENU_ORDER_TITLE'),
                                                'sort' => 10,
                                                'url' => '/bitrix/admin/'.$moduleID.'_order.php?mid=main',
                                                'icon' => 'imi_order',
                                                'page_icon' => 'pi_torder',
                                                'items_id' => 'main',
                                        ),
                                        array(
                                                'text' => GetMessage('BONUSDOWEB_MENU_ORDERMIN_TEXT'),
                                                'title' => GetMessage('BONUSDOWEB_MENU_ORDERMIN_TITLE'),
                                                'sort' => 20,
                                                'url' => '/bitrix/admin/'.$moduleID.'_ordermin.php?mid=main',
                                                'icon' => 'imi_order',
                                                'page_icon' => 'pi_torder',
                                                'items_id' => 'main',
                                        ),                                      
                                ),
                        );

                        $arGlobalMenu[] = $arMenu;
                }
        }
}
?>