<?php

IncludeModuleLangFile(__FILE__);
$moduleClass = 'BonusDoweb';
$solution = 'bonusdoweb';

// initialize module parametrs list and default values
$moduleClass::$arParametrsList = array(
	'MAIN' => array(
		'TITLE' => GetMessage('MAIN_MENU'),
		'THEME' => 'Y',
		'OPTIONS' => array(
			'REG_USER' =>	array(
				'TITLE' => GetMessage('MAIN_REG_USER'),
				'TYPE' => 'text',
				'DEFAULT' => '1000',
				'THEME' => 'N',
			),
			'BIRTHDAY' =>	array(
				'TITLE' => GetMessage('MAIN_BIRTHDAY'),
				'TYPE' => 'text',
				'DEFAULT' => '500',
				'THEME' => 'N',
			),
			'MARCH' =>	array(
				'TITLE' => GetMessage('MAIN_MARCH'),
				'TYPE' => 'text',
				'DEFAULT' => '300',
				'THEME' => 'N',
			),
			'FEBRUARY' =>	array(
				'TITLE' => GetMessage('MAIN_FEBRUARY'),
				'TYPE' => 'text',
				'DEFAULT' => '300',
				'THEME' => 'N',
			),
			'NEWYEAR' =>	array(
				'TITLE' => GetMessage('MAIN_NEWYEAR'),
				'TYPE' => 'text',
				'DEFAULT' => '300',
				'THEME' => 'N',
			),
	
		)
	),
	
);
?>