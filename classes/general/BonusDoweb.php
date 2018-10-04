<?php

IncludeModuleLangFile(__FILE__);

include_once __DIR__.'/../../parametrs.php';

Class BonusDoweb
{
	const MODULE_ID = DIGITAL_MODULE_ID;
	const PARTNER_NAME = 'doweb';
	const SOLUTION_NAME = 'bonusdoweb';
	const devMode = false;

	static $arParametrsList = array();
	private static $arMetaParams = array();

   public function sendBonusBIAction($action = 'unknown') {
		if(CModule::IncludeModule('main')){

		}
	}

	public function correctInstall(){
		if(CModule::IncludeModule('main')){
			if(COption::GetOptionString(self::MODULE_ID, 'WIZARD_DEMO_INSTALLED') == 'Y'){
				require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/classes/general/wizard.php');
				@set_time_limit(0);
				if(!CWizardUtil::DeleteWizard(self::PARTNER_NAME.':'.self::SOLUTION_NAME)){
					if(!DeleteDirFilesEx($_SERVER['DOCUMENT_ROOT'].'/bitrix/wizards/'.self::PARTNER_NAME.'/'.self::SOLUTION_NAME.'/')){
						self::removeDirectory($_SERVER['DOCUMENT_ROOT'].'/bitrix/wizards/'.self::PARTNER_NAME.'/'.self::SOLUTION_NAME.'/');
					}
				}

				UnRegisterModuleDependences('main', 'OnBeforeProlog', self::MODULE_ID, __CLASS__, 'correctInstall');
				COption::SetOptionString(self::MODULE_ID, 'WIZARD_DEMO_INSTALLED', 'N');
			}
		}
	}
	static function GetBackParametrsValues($SITE_ID, $bStatic = true){
		if($bStatic)
			static $arValues;

		if($bStatic && $arValues === NULL || !$bStatic){
			$arDefaultValues = $arValues = $arNestedValues = array();
			$bNestedParams = false;
			if(self::$arParametrsList && is_array(self::$arParametrsList))
			{
				foreach(self::$arParametrsList as $blockCode => $arBlock)
				{
					if($arBlock['OPTIONS'] && is_array($arBlock['OPTIONS']))
					{
						foreach($arBlock['OPTIONS'] as $optionCode => $arOption)
						{
							if($arOption['TYPE'] !== 'note' && $arOption['TYPE'] !== 'includefile'){
								if($arOption['TYPE'] === 'array'){
									$itemsKeysCount = COption::GetOptionString(self::MODULE_ID, $optionCode, '0', $SITE_ID);
									if($arOption['OPTIONS'] && is_array($arOption['OPTIONS'])){
										for($itemKey = 0, $cnt = $itemsKeysCount + 1; $itemKey < $cnt; ++$itemKey){
											$_arParameters = array();
											$arOptionsKeys = array_keys($arOption['OPTIONS']);
											foreach($arOptionsKeys as $_optionKey){
												$arrayOptionItemCode = $optionCode.'_array_'.$_optionKey.'_'.$itemKey;
												$arValues[$arrayOptionItemCode] = COption::GetOptionString(self::MODULE_ID, $arrayOptionItemCode, '', $SITE_ID);
												$arDefaultValues[$arrayOptionItemCode] = $arOption['OPTIONS'][$_optionKey]['DEFAULT'];
											}
										}
									}
									$arValues[$optionCode] = $itemsKeysCount;
									$arDefaultValues[$optionCode] = 0;
								}
								else{
									$arDefaultValues[$optionCode] = $arOption['DEFAULT'];
									$arValues[$optionCode] = COption::GetOptionString(self::MODULE_ID, $optionCode, $arOption['DEFAULT'], $SITE_ID);

									if(isset($arOption['SUB_PARAMS']) && $arOption['SUB_PARAMS']) //get nested params default value
									{
										if($arOption['TYPE'] == 'selectbox' && (isset($arOption['LIST'])) && $arOption['LIST'])
										{
											$bNestedParams = true;
											$arNestedValues[$optionCode] = $arOption['LIST'];
											foreach($arOption['LIST'] as $key => $value)
											{
												if($arOption['SUB_PARAMS'][$key])
												{
													foreach($arOption['SUB_PARAMS'][$key] as $key2 => $arSubOptions)
														$arDefaultValues[$key.'_'.$key2] = $arSubOptions['DEFAULT'];
												}
											}
										}
									}

									if(isset($arOption['DEPENDENT_PARAMS']) && $arOption['DEPENDENT_PARAMS']) //get dependent params default value
									{
										foreach($arOption['DEPENDENT_PARAMS'] as $key => $arSubOption)
										{
											$arDefaultValues[$key] = $arSubOption['DEFAULT'];
											$arValues[$key] = COption::GetOptionString(self::MODULE_ID, $key, $arSubOption['DEFAULT'], $SITE_ID);
										}
									}
								}
							}
						}
					}
				}
			}
			if($arNestedValues && $bNestedParams) //get nested params bd value
			{
				foreach($arNestedValues as $key => $arAllValues)
				{
					$arTmpValues = array();
					foreach($arAllValues as $key2 => $arOptionValue)
					{
						$arTmpValues = unserialize(COption::GetOptionString(self::MODULE_ID, 'NESTED_OPTIONS_'.$key.'_'.$key2, serialize(array()), $SITE_ID));
						if($arTmpValues)
						{
							foreach($arTmpValues as $key3 => $value)
							{
								$arValues[$key2.'_'.$key3] = $value;
							}
						}
					}

				}
			}

			// replace #SITE_DIR#
			if(!defined('ADMIN_SECTION'))
			{
				if($arValues && is_array($arValues))
				{
					foreach($arValues as $optionCode => $arOption)
					{
						if(!is_array($arOption))
							$arValues[$optionCode] = str_replace('#SITE_DIR#', SITE_DIR, $arOption);
					}
				}
			}
		}

		return $arValues;
	}
	static function ShowAdminRow($optionCode, $arOption, $arTab, $optionValNew){
		$optionName = $arOption["TITLE"];
		$optionType = $arOption["TYPE"];
		$optionDefault = $arOption["DEFAULT"];
		$optionVal = $arTab["OPTIONS"][$optionCode];
		$optionsSiteID = $arTab["SITE_ID"];
		
		?>
			<?if(!$isArrayItem):?>
				<td class="<?=(in_array($optionType, array("multiselectbox", "textarea", "statictext", "statichtml")) ? "adm-detail-valign-top" : "")?>" width="50%">
					<?=$optionName.($optionCode == "BASE_COLOR_CUSTOM" ? ' #' : '')?>

					<?if(strlen($optionSup_text)):?>
						<span class="required"><sup><?=$optionSup_text?></sup></span>
					<?endif;?>
				</td>
			<?endif;?>
			<td<?=(!$isArrayItem ? ' width="50%"' : '')?>>
				<?if($optionType == "text" || $optionType == "password"):?>
					<input type="<?=$optionType?>" <?=((isset($arOption['PARAMS']) && isset($arOption['PARAMS']['WIDTH'])) ? 'style="width:'.$arOption['PARAMS']['WIDTH'].'"' : '');?> <?=$optionController?> size="<?=$optionSize?>" maxlength="255" value="<?=$optionValNew ? htmlspecialcharsbx($optionValNew) : htmlspecialcharsbx($optionVal);?>

					" name="<?=htmlspecialcharsbx($optionCode)."_".$optionsSiteID?>" <?=$optionDisabled?> <?=($optionCode == "password" ? "autocomplete='off'" : "")?>>
			
				<?endif;?>
			</td>

		<?
	}
	
	
}
