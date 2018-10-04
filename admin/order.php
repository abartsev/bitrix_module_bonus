<?require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php');
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_after.php');

$moduleClass = "BonusDoweb";
$moduleClassUser = "SaleUser";
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
	$getAnswer = false;
	$db_res = CSite::GetList($by, $sort, array("ACTIVE"=>"Y"));
	while($res = $db_res->Fetch()){
		$arSites[] = $res;
	}

	if($REQUEST_METHOD == "POST" && $RIGHT >= "W" && check_bitrix_sessid()){
		if ($_REQUEST["bonus_summ"] != '' && $_REQUEST["user"] != '') {
			$getAnswer = $moduleClassUser::AccrualBonus($_REQUEST["bonus_summ"], $_REQUEST["user"]);
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
	$UserArray = $moduleClassUser::UserArray();
	CJSCore::Init(array("jquery"));
	$tabControl->Begin();
	?>

	<form method="post" enctype="multipart/form-data" action="<?=$APPLICATION->GetCurPage()?>?mid=<?=urlencode($mid)?>&amp;lang=<?=LANGUAGE_ID?>">
		<?=bitrix_sessid_post();?>
		<? $tabControl->BeginNextTab();?>
	
				<tr>
					<td width="20%"></td>
					<td width="50%">
						<select class="users_select" name="users">
							<option ><?=GetMessage('SELECT_A_USER'); ?></option>
								<? foreach ($UserArray as $UserKey => $UserVal): ?>
									<option value="<?=$UserVal["ID"]?>"><?=$UserVal['NAME']." ".$UserVal['LAST_NAME']?></option>
								<? endforeach ?>
						</select>
						<input type="text" name="user" class="user hide" value="">
					</td>
				</tr>
		   		<tr>
		   			<td width="20%">
		   				<label><?=getMessage('SUMM_ORDER')?></label>
		   			</td>
		   			<td width="50%">
		   				<input type="text" name="summ_order" class="summ_order">
		   			</td>
		   		</tr>
		   		<tr>
		   			<td width="20%">
		   				<label><?=getMessage('PERCENT_BONUS')?></label>
		   			</td>
		   			
		   			<td width="50%">
		   				<input type="text" name="percent_bonus" class="percent_bonus">
		   			</td>
		   		</tr>
		   		<tr>
		   			<td width="20%">
		   				<label><?=getMessage('BONUS_SUMM')?></label>
		   			</td>
		   			<td width="50%">
		   				<input type="text" name="bonus_summ" class="bonus_summ">
		   			</td>
		   		</tr>
		   		<tr>
		   			<td width="20%">
		   				<label><? if ($getAnswer) {
		   					echo getMessage('BONUS_ANSWER');
		   				}?></label>
		   			</td>
		   		</tr>

   		<?$tabControl->Buttons();?>
		<input <?if($RIGHT < "W") echo "disabled"?> type="submit" name="Apply" class="submit-btn" value="<?=GetMessage("MAIN_OPT_APPLY")?>" title="<?=GetMessage("MAIN_OPT_APPLY_TITLE")?>">

	</form>
	<script>
		$(document).ready(function () {
			$('.users_select').on('change', function() {
			  var userId = $('.users_select option:selected').val();
			  $('input.user').val(userId);
			});
			$('.percent_bonus').on('keyup', function(){
				var	numb = $(this).val();
				var summ_order = $('.summ_order').val();
				var summ_bonus = $('.bonus_summ');
				if (numb != '' && summ_order != '') {
					var summ = (numb*summ_order)/100;
					summ_bonus.val(Math.ceil(summ));
				}
			})
		});
	</script>
<?require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php');?>