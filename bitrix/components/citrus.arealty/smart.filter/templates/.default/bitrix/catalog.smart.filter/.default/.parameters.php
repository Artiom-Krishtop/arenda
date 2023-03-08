<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arTemplateParameters['DISPLAY_ELEMENT_COUNT'] = array(
	'PARENT' => 'VISUAL',
	'NAME' => GetMessage('TP_BCSF_DISPLAY_ELEMENT_COUNT'),
	'TYPE' => 'CHECKBOX',
	'DEFAULT' => 'Y',
);

$arTemplateParameters['FORM_ACTION_ON_SECTION_PAGE'] = array(
	'PARENT' => 'VISUAL',
	'NAME' => GetMessage('CITRUS_FILTER_FORM_ACTION_ON_SECTION_PAGE'),
	'TYPE' => 'CHECKBOX',
	'DEFAULT' => 'N',
);

$arTemplateParameters['MAX_ITEMS_COUNT'] = array(
	'PARENT' => 'VISUAL',
	'NAME' => GetMessage('CITRUS_FILTER_FORM_MAX_ITEMS_COUNT'),
	'TYPE' => 'TEXT',
	'DEFAULT' => '6',
);