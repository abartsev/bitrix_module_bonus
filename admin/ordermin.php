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


	$flag = $request->isAjaxRequest();
	$by = "id";
	$sort = "asc";
	$arParam = array();
	$arSites = array();
	$bonusAnswer = false;
	$db_res = CSite::GetList($by, $sort, array("ACTIVE"=>"Y"));
	while($res = $db_res->Fetch()){
		$arSites[] = $res;
	}
	if (isset($_POST['userId']) && !empty($_POST['userId'])) {
		$APPLICATION->RestartBuffer();
		$moduleClassUser::SaleGetByUser($_POST['userId']);
		exit();

	}

	if($REQUEST_METHOD == "POST" && $RIGHT >= "W" && check_bitrix_sessid()){
		if (!empty($_REQUEST["user"]) && !empty($_REQUEST["bonus_summ"])) {
		$bonusAnswer = $moduleClassUser::SuccessPayment($_REQUEST["user"],$_REQUEST["bonus_summ"]);
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
		   				<label><?=getMessage('BONUS_ONLY')?></label>
		   			</td>
		   			<td width="50%">
		   				<input type="text" name="bonus_only" class="bonus_only">
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
		   				<?php if ($bonusAnswer): ?>
		   					<label><?=getMessage('BONUS_ANSWER')?></label>
		   				<?php endif ?>
		   				
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
			  if (userId) {
			  	 $.ajax({
			  	 		type: 'POST',
				      	url: '<?=$APPLICATION->GetCurPage();?>?ajax_userid='+userId,
				        data: {userId: userId},
				        success: function (data) {
				            $('.bonus_only').val(+data).toFixed(1);
				      }
				   });
			  }
			});
			$('.percent_bonus').on('keyup', function(){
				var	numb = $(this).val();
				var bonus_only = $('.bonus_only').val();
				var summ_order = $('.summ_order').val();
				var summ_bonus = $('.bonus_summ');
				if (numb != '' && summ_order != '') {
					var summ = (numb*summ_order)/100;
					if (summ > bonus_only) {
						summ_bonus.val(Math.ceil(bonus_only));
					}else{
						summ_bonus.val(Math.ceil(summ));
					}
					
					
				}
			})
		});
	</script>
<?require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php');?>