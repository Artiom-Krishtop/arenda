<?php

use Bitrix\Main\IO\Path;
use Bitrix\Main\Localization\Loc;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

/** @var array $arCurrentValues */

/*
$directory = Path::normalize(__DIR__);
$documentRoot = Path::normalize($_SERVER['DOCUMENT_ROOT']);
$parametrsFilePath = str_replace($documentRoot,'',$directory); */

$parametrsFilePath = "/bitrix/components/citrus/iblock.element.form";


if(!CModule::IncludeModule("iblock"))
	return;

$arIBlockType = CIBlockParameters::GetIBlockTypes();

$arIBlock=array();
$rsIBlock = CIBlock::GetList(Array("sort" => "asc"), Array("TYPE" => $arCurrentValues["IBLOCK_TYPE"], "ACTIVE"=>"Y"));
while($arr=$rsIBlock->Fetch())
{
	$arIBlock[$arr["ID"]] = "[".$arr["ID"]."] ".$arr["NAME"];
}

$arIBlockProp = array();
if (0 < (int)$arCurrentValues["IBLOCK_ID"])
{
	$rsProp = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("IBLOCK_ID"=>$arCurrentValues["IBLOCK_ID"], "ACTIVE"=>"Y" ,"PROPERTY_TYPE" => "S"));
	while ($arr=$rsProp->Fetch())
	{
		$arIBlockProp[$arr["CODE"]] = "[".$arr["CODE"]."] ".$arr["NAME"];
	}
}

CBitrixComponent::includeComponentClass('citrus:iblock.element.form');

require_once __DIR__ . '/func.php';

$jsMessages = array('IBLOCK_ID' => $arCurrentValues['IBLOCK_ID']);
foreach (Loc::loadLanguageFile(__FILE__) as $key => $value)
{
	if (strpos($key, '_JS') !== false)
	{
		$jsMessages[ToLower($key)] = $value;
	}
}

$arComponentParameters = array(
	"GROUPS" => array(
		"FORM_STYLE" => array(
			"NAME" => GetMessage("CIEE_FORM_STYLE"),
			"SORT" => "1000",
		),
		"MAIL_SETTINGS" => array(
			"SORT" => 110,
			"NAME" => GetMessage("CIEE_MAIL_SETTINGS"),
		),
		"TITLES" => array(
			"NAME" => GetMessage("IBLOCK_TITLES"),
			"SORT" => "1000",
		),
		"ELEMENT_EDIT" => array(
			"NAME" => GetMessage("CIEE_ELEMENT_EDIT"),
			"SORT" => "1000",
		),
		"ACCESS_SETTINGS" => array(
			"SORT" => 110,
			"NAME" => GetMessage("CIEE_ACCESS_SETTINGS"),
		),
		"USED_AJAX" => array(
			"NAME" => GetMessage("CIEE_AJAX"),
			"SORT" => "1000",
		)
	),
	"PARAMETERS" => array(
		"IBLOCK_TYPE" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("CIEE_IBLOCK_TYPE"),
			"TYPE" => "LIST",
			"VALUES" => $arIBlockType,
			"REFRESH" => "Y",
		),
		"IBLOCK_ID" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("CIEE_IBLOCK"),
			"TYPE" => "LIST",
			"VALUES" => $arIBlock,
			"REFRESH" => "Y",
			"ADDITIONAL_VALUES" => "Y",
		),
		"PARENT_SECTION" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("CIEE_SECTION_ID"),
			"TYPE" => "INT",
			"DEFAULT" => '',
		),
		"PARENT_SECTION_CODE" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("CIEE_SECTION_CODE"),
			"TYPE" => "STRING",
			"DEFAULT" => '',
		),
		'FIELDS' => array(
			'NAME' => GetMessage('CIEE_FIELD_DATA'),
			'TYPE' => 'CUSTOM',
			'JS_FILE' => $parametrsFilePath . '/settings/settings.js',
			'JS_EVENT' => 'OnCitrusIBlockElementFormSettingsEdit',
			'JS_DATA' => $jsMessages,
			'DEFAULT' => serialize(CIEE_GetDefaultFields()),
			'PARENT' => 'BASE',
			'JS_COMPONENT_PATH' => $parametrsFilePath
		),
		"CREATE_USER" => array(
			"PARENT" => "ELEMENT_EDIT",
			"NAME" => GetMessage("CIEE_CREATE_USER"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
		),
		"PROPERTY_CODE" => array(
			"PARENT" => "ELEMENT_EDIT",
			"NAME" => GetMessage("CIEE_PROPERTY_CODE"),
			"TYPE" => "LIST",
			"MULTIPLE" => "N",
			"VALUES" => $arIBlockProp,
			"ADDITIONAL_VALUES" => "Y",
		),
		"NOT_CREATE_ELEMENT" => array(
			"PARENT" => "ELEMENT_EDIT",
			"NAME" => GetMessage("CIEE_CREATE_ELEMENT"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
		),
		"EDIT_ELEMENT" => array(
			"PARENT" => "ELEMENT_EDIT",
			"NAME" => GetMessage("CIEE_EDIT_ELEMENT"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
		),
		"ELEMENT_ID" => array(
			"PARENT" => "ELEMENT_EDIT",
			"NAME" => GetMessage("CIEE_ELEMENT_ID"),
			"TYPE" => "STRING",
			"DEFAULT" => '',
			"ADDITIONAL_VALUES" => "Y",
		),
		"SEND_MESSAGE" => array(
			"PARENT" => "MAIL_SETTINGS",
			"NAME" => GetMessage("CIEE_SEND_MESSAGE"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
			"REFRESH" => "Y"
		),

		"SUCCESS_ADD_MESSAGE" => array(
			"PARENT" => "FORM_STYLE",
			"NAME" => GetMessage("CIEE_SUCCESS_ADD_MESSAGE"),
			"TYPE" => "STRING",
			"DEFAULT" => "",
		),
		'ACCESS_DENIED_MESSAGE' => array(
			'PARENT' => 'FORM_STYLE',
			'NAME' => GetMessage("CIEE_ACCESS_DENIED_MESSAGE"),
			'TYPE' => 'STRING',
			'DEFAULT' => "",
		),
		"SUBMIT_TEXT" => array(
			"PARENT" => "FORM_STYLE",
			"NAME" => GetMessage("CIEE_SUBMIT_TEXT"),
			"TYPE" => "STRING",
			"DEFAULT" => GetMessage("CIEE_SUBMIT_TEXT_DEFAULT"),
		),
		"ERROR_LIST_MESSAGE" => array(
			"PARENT" => "FORM_STYLE",
			"NAME" => GetMessage("CIEE_ERROR_TITLE_MESSAGE"),
			"TYPE" => "STRING",
			"DEFAULT" => '',
		),
		"FORM_TITLE" => array(
			"PARENT" => "FORM_STYLE",
			"NAME" => GetMessage("CIEE_FORM_TITLE"),
			"TYPE" => "STRING",
			"DEFAULT" => '',
		),
		"AJAX" => array(
			"PARENT" => "USED_AJAX",
			"NAME" => GetMessage("CIEE_USED_AJAX_FIELD"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
		),
		"URL_SUCCESS_PAGE" => array(
			"PARENT" => "USED_AJAX",
			"NAME" => GetMessage("CIEE_URL_SUCCESS_PAGE"),
			"TYPE" => "STRING",
			"DEFAULT" => '',
		),
		"URL_CUR_PAGE" =>  array(
			"PARENT" => "USED_AJAX",
			"NAME" => GetMessage("CIEE_URL_CUR_PAGE"),
			"TYPE" => "STRING",
			"DEFAULT" => '',
		),
	),
);

if($arCurrentValues['SEND_MESSAGE'] == "Y") {
	$arMailEvent = array(
		GetMessage('CIEE_MAILE_EVENT_NOT_SET')
	);

	$arFilter = array(
		"LID" => LANGUAGE_ID
	);

	$rsEventType = CEventType::GetList($arFilter);
	while($arEvent = $rsEventType->Fetch())
		$arMailEvent[$arEvent['EVENT_NAME']] = "[".$arEvent["EVENT_NAME"]."] ".$arEvent["NAME"];

	$arMailTemplate = array(
		0 => GetMessage('CIEE_ALL_MAILE_EVENT_TEMPLATE')
	);

	if(isset($arCurrentValues['MAIL_EVENT']) && strlen($arCurrentValues['MAIL_EVENT']) > 0) {

		$rsMess = CEventMessage::GetList($by="site_id", $order="desc", Array("TYPE_ID" => $arCurrentValues['MAIL_EVENT'], "ACTIVE" => "Y"));
		while($arr = $rsMess->Fetch())
			$arMailTemplate[$arr['ID']] = "[" . $arr['ID'] ."]" . $arr['SUBJECT'];
	}

	$arComponentParameters['PARAMETERS']['MAIL_EVENT'] = array(
		"PARENT" => "MAIL_SETTINGS",
		"NAME" => GetMessage("CIEE_MAIL_EVENT"),
		"TYPE" => "LIST",
		"VALUES" => $arMailEvent,
		"REFRESH" => "Y"
	);

	$arComponentParameters['PARAMETERS']['MAILE_EVENT_TEMPLATE'] = array(
		"PARENT" => "MAIL_SETTINGS",
		"NAME" => GetMessage("CIEE_MAILE_EVENT_TEMPLATE_SET"),
		"TYPE" => "LIST",
		"MULTIPLE" => "Y",
		"VALUES" => $arMailTemplate,
	);
}
