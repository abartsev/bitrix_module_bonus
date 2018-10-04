<?php

use Bitrix\Main;
use Bitrix\Main\Type;

IncludeModuleLangFile(__FILE__);

class SaleUser
{
	 static function OnAfterUserRegisterHandler(&$arFields)
    {
		$summ = COption::GetOptionInt('bonusdoweb', "REG_USER");
    	$user = new CUser;
    	
    	if (!$arFields['UF_BONUS']) {
    		$bonus = array('UF_BONUS', 'UF_BONUS_POD');

		$oUserTypeEntity    = new CUserTypeEntity();
		for($i=0; $i <=1; $i++){
			$aUserFields    = array(
			    'ENTITY_ID'         => 'USER',
			    'FIELD_NAME'        => $bonus[$i],
			    'USER_TYPE_ID'      => 'string',
				'XML_ID'            => 'XML_ID_UF_BONUS',
				'SORT'              => 500,
				'MULTIPLE'          => 'N',
			    'MANDATORY'         => 'N',
			    'SHOW_FILTER'       => 'N',
			    'SHOW_IN_LIST'      => '',
			    'EDIT_IN_LIST'      => '',
			    'IS_SEARCHABLE'     => 'N',
			    'SETTINGS'          => array(
			        'DEFAULT_VALUE' => '',
			        'SIZE'          => '20',
			        'ROWS'          => '1',
			        'MIN_LENGTH'    => '0',
			        'MAX_LENGTH'    => '0',
			        'REGEXP'        => '',
				),
			    'EDIT_FORM_LABEL'   => array(
			        'ru'    => getMessage($bonus[$i]),
			        'en'    => getMessage('EN_'.$bonus[$i]),
			    ),
			    'LIST_COLUMN_LABEL' => array(
			        'ru'    => getMessage($bonus[$i]),
			        'en'    => getMessage('EN_'.$bonus[$i]),
			    ),
			    'LIST_FILTER_LABEL' => array(
			        'ru'    => getMessage($bonus[$i]),
					'en'    => getMessage('EN_'.$bonus[$i]),
				),
			    'ERROR_MESSAGE'     => array(
			        'ru'    => getMessage("ERROR_MESSAGE_FIELD"),
			        'en'    => getMessage("EN_ERROR_MESSAGE_FIELD"),
				),
			    'HELP_MESSAGE'      => array(
			        'ru'    => '',
				'en'    => '',
			    ),
			);
 
			$iUserFieldId = $oUserTypeEntity->Add( $aUserFields ); 
		}
    	}
    	
                if($arFields["USER_ID"]>0){
                	if(\CModule::IncludeModule("sale")){
	                    $arFields = Array("USER_ID" => $arFields["USER_ID"], "CURRENCY" => "RUB", "CURRENT_BUDGET" => $summ);
	                    $accountID = \CSaleUserAccount::Add($arFields);
                	}
                	

					$fields = Array("UF_BONUS" => $summ,); 
					$user->Update($arFields["USER_ID"], $fields);
                }
    }
    static function OnAfterUserUpdateHandler(&$arFields){
		// if(\CModule::IncludeModule("sale")){
		// 	$idsale = \CSaleUserAccount::GetByUserID($arFields["ID"], 'RUB');
		// 	if ($idsale["CURRENT_BUDGET"] != $arFields['UF_BONUS']) {
				
		// 			\CSaleUserAccount::Update($idsale["ID"], array('CURRENT_BUDGET' => $arFields['UF_BONUS']));
				
		// 	}
		// }
    }
    static function UserArray(){
       $UserArray = array();
		$filter = Array("GROUPS_ID" => Array(1,4,5));
			$arParams["SELECT"] = array("UF_BONUS");
			$rsUsers = CUser::GetList(($by = "NAME"), ($order = "desc"), $filter, $arParams);
			while ($arUser = $rsUsers->Fetch()) {
			  	$UserArray[] = $arUser;
			  	}
		return $UserArray;
	}
	static function AccrualBonus($summ, $idUser){
		CSaleUserAccount::UpdateAccount($idUser, +$summ,"RUB","ORDER_BONUS");
		$oUser = new CUser;
		$filter = Array("ID" => $idUser);
		$arParams["SELECT"] = array("UF_BONUS");
		$rsUser = CUser::GetList(($by="NAME"), ($order="desc"), $filter,$arParams);
		$userBonus = $rsUser->Fetch();
		$oUser->Update($idUser, array('UF_BONUS' => $summ + $userBonus['UF_BONUS']));
		return true;
	}
	static function AccrualBonusPod($summ, $idUser){
		CSaleUserAccount::UpdateAccount($idUser, +$summ,"RUB","ORDER_BONUS");
		$oUser = new CUser;
		$filter = Array("ID" => $idUser);
		$arParams["SELECT"] = array("UF_BONUS", 'UF_BONUS_POD');
		$rsUser = CUser::GetList(($by="NAME"), ($order="desc"), $filter,$arParams);
		$userBonus = $rsUser->Fetch();
		$oUser->Update($idUser, array('UF_BONUS_POD' => $summ + $userBonus['UF_BONUS_POD']));
		return true;
	}
	static function SaleGetByUser($idUser){
		$sale = CSaleUserAccount::GetByUserID($idUser,'RUB');
		echo $sale["CURRENT_BUDGET"];
		return $sale["CURRENT_BUDGET"];
	}
	static function SuccessPayment($idUser, $summ){
		$sum_payment = $summ;
		CSaleUserAccount::Pay($idUser, $summ, "RUB", false);
		$oUser = new CUser;
		$filter = Array("ID" => $idUser);
		$arParams["SELECT"] = array("UF_BONUS", 'UF_BONUS_POD');
		$rsUser = CUser::GetList(($by="NAME"), ($order="desc"), $filter,$arParams);
		$userBonus = $rsUser->Fetch();
		$bonus = $userBonus['UF_BONUS'];
		$bonus_pod = $userBonus['UF_BONUS_POD'];
		if ($bonus_pod != 0 && $sum_payment != 0) {
			
			$oUser->Update($idUser, array('UF_BONUS_POD' => $sum_payment > $bonus_pod ? 0 :  $bonus_pod - $sum_payment));
			$sum_payment = $sum_payment > $bonus_pod ? $sum_payment - $bonus_pod :  0;
			var_dump($sum_payment, $bonus_pod);
		}
		if ($bonus != 0 && $sum_payment != 0) {
			$oUser->Update($idUser, array('UF_BONUS' => $sum_payment > $bonus ? $sum_payment - $bonus :  $bonus - $sum_payment));
			var_dump($sum_payment, $bonus);

		}

		return true;
	}

}
