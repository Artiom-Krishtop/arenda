<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main\Localization\Loc;

$arTemplateParameters = array(
	'SHOW_DETAIL_LINK' => array(
		'NAME' => Loc::getMessage("CITRUS_AREALTY_COMPANY_ABOUT_SHOW_DETAIL_LINK"),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'N',
	),
);
