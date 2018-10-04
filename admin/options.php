<?require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php');
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_after.php');

$moduleClass = "BonusDoweb";
$moduleID = "bonusdoweb";
global  $APPLICATION;
IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/options.php");

CModule::IncludeModule($moduleID);
IncludeModuleLangFile(__FILE__);


$RIGHT = $APPLICATION->GetGroupRight($moduleID);



	$by = "id";
	$sort = "asc";
	$arParam = array();
	$arSites = array();
	$db_res = CSite::GetList($by, $sort, array("ACTIVE"=>"Y"));
	while($res = $db_res->Fetch()){
		$arSites[] = $res;
	}

	if($REQUEST_METHOD == "POST" && $RIGHT >= "W" && check_bitrix_sessid()){
	
		foreach ($_REQUEST as $key => $value) {
			if ($key == 'BIRTHDAY_s1' || $key == 'MARCH_s1' || $key == 'FEBRUARY_s1' || $key == 'REG_USER_s1' || $key == 'NEWYEAR_s1') {
				$arParam[preg_replace('(_s1)','',$key)] = $value;
			}
	
		}

		foreach ($arParam as $keyOption=> $newDependentVal) {
			COption::SetOptionString($moduleID, $keyOption, $newDependentVal, "", $arSites['LID']);
		}

	}
	foreach($arSites as $key => $arSite){
		$arBackParametrs = $moduleClass::GetBackParametrsValues($arSite["ID"], false);
		$arTabs[] = array(
			"DIV" => "edit".($key+1),
			"TAB" => GetMessage("MAIN_OPTIONS_SITE_TITLE", array("#SITE_NAME#" => $arSite["NAME"], "#SITE_ID#" => $arSite["ID"])),
			"ICON" => "settings",
			"TITLE" => GetMessage("MAIN_OPTIONS_TITLE"),
			"PAGE_TYPE" => "site_settings",
			"SITE_ID" => $arSite["ID"],
			"SITE_DIR" => $arSite["DIR"],
			"OPTIONS" => $arBackParametrs,
		);
	}
$tabControl = new CAdminTabControl("tabControl", $arTabs);
	CJSCore::Init(array("jquery"));
	CAjax::Init();
	$tabControl->Begin();
	?>
	<form method="post" enctype="multipart/form-data" action="<?=$APPLICATION->GetCurPage()?>?mid=<?=urlencode($mid)?>&amp;lang=<?=LANGUAGE_ID?>">
		<?=bitrix_sessid_post();?>
		<?
		foreach($arTabs as $key => $arTab){
			$tabControl->BeginNextTab();
			if($arTab["SITE_ID"]){
				$optionsSiteID = $arTab["SITE_ID"];
				foreach($moduleClass::$arParametrsList as $blockCode => $arBlock)
				{?>
					<tr class="heading"><td colspan="2"><?=$arBlock["TITLE"]?></td></tr>
					<?
					foreach($arBlock["OPTIONS"] as $optionCode => $arOption)
					{
						if(isset($arTab["OPTIONS"][$optionCode]) || $arOption["TYPE"] == 'note' || $arOption["TYPE"] == 'includefile')
						{
								$itemsKeysCount = COption::GetOptionString($moduleID, $optionCode, 0, $optionsSiteID);
								if ($itemsKeysCount != $arOption["DEFAULT"][$optionCode]) {
										$optionValNew = $itemsKeysCount;
									}	
									$optionName = $arOption["TITLE"];
									$optionType = $arOption["TYPE"];
									$optionList = $arOption["LIST"];
									$optionDefault = $arOption["DEFAULT"];
									?>
									<tr>
										<?=$moduleClass::ShowAdminRow($optionCode, $arOption, $arTab, $optionValNew);?>
									</tr>
		<?
						}
					}
				}
			}
		}
		?>
		<?
		$tabControl->Buttons();
		?>
		<input <?if($RIGHT < "W") echo "disabled"?> type="submit" name="Apply" class="submit-btn" value="<?=GetMessage("MAIN_OPT_APPLY")?>" title="<?=GetMessage("MAIN_OPT_APPLY_TITLE")?>">

	</form>
<?require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php');?>