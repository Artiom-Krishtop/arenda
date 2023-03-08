<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

/** @var mixed[] $arCurrentValues */

use Citrus\Forms;
use Bitrix\Main;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

if (!Main\Loader::includeModule('iblock'))
{
	ShowError(Loc::getMessage("CITRUS_MISSING_MODULE", array('#ID#' => 'iblock')));
	return false;
}

if (!Main\Loader::includeModule('citrus.forms'))
{
	ShowError(Loc::getMessage("CITRUS_MISSING_MODULE", array('#ID#' => 'citrus.forms')));
	return false;
}

$arAvalibleGroup = array("RECAPTCHA","FIELDS","MAIL_SETTINGS","VISUAL", 'BASE', 'MESSAGES', 'ADDITIONAL', 'USER_CONSENT');

/**
 * При обновлении диалога с параметрами компонента (при смене инфоблока или установке галочки Использовать Использовать Google reCaptcha)
 * происходит перезагрузка формы с параметрами, при этом JS_DATA у CUSTOM-параметров прилетает в кодировке utf-8
 * настройки полей в неправильной кодировке затем используется на форме и сохраняется в параметрах компонента
 * #35002
 */
if (Main\Context::getCurrent()->getRequest()->isAjaxRequest())
{
	//TODO могут быть ошибки в кодировке
	if (isset($_REQUEST['current_values'])) CUtil::decodeURIComponent($arCurrentValues);
}

$componentName = Forms\getComponentName();
CBitrixComponent::includeComponentClass($componentName);
if(class_exists('\Citrus\Forms\IblockElementComponent')) {
	$obj = new Forms\IblockElementComponent();
	$obj->initComponent($componentName,$arCurrentValues['COMPONENT_TEMPLATE']);
	$arCompParam = $obj->GetComponentParametrs($arAvalibleGroup, $arCurrentValues);

	if(
		(
			isset($arCurrentValues['IBLOCK_CODE'])
			&& strlen($arCurrentValues['IBLOCK_CODE']) > 0
		)
		&& (
			!isset($arCurrentValues['IBLOCK_ID'])
			|| (int)$arCurrentValues['IBLOCK_ID'] <= 0
		)
	) {
		$arCurrentValues['IBLOCK_ID'] = Forms\IblockElementComponent::getIblock($arCurrentValues['IBLOCK_CODE']);
	}

	if (
		isset($arCurrentValues['IBLOCK_ID']) && (int)$arCurrentValues['IBLOCK_ID'] > 0
	) {
		$iblockId = (int)$arCurrentValues['IBLOCK_ID'];
		try {
			$defaultValue = $obj->GetParametrsFieldInputIblock($iblockId, $arCurrentValues);
			$arCompParam['PARAMETERS']['FIELDS'] = $defaultValue;
		}
		catch(Main\SystemException $ex) {}
	}
}

/**
 * Получим список доступных типов инфоблоков
 */

$arIBlockType = array (
	"" => Loc::getMessage('PAR_IBFROM_F_V_DEFAULT')
);

$arIBlockType = array_merge($arIBlockType,\CIBlockParameters::GetIBlockTypes());

/**
 * получим список доступных инфоблоков по выбранному типу
 */
$arIBlock = array(
	"" => Loc::getMessage('PAR_IBFROM_F_V_DEFAULT')
);

if(isset($arCurrentValues['IBLOCK_TYPE']) && strlen($arCurrentValues['IBLOCK_TYPE']) > 0) {
	$rsIBlock = Bitrix\Iblock\IblockTable::getList(array(
		'order' => array("ID" => "asc"),
		'filter' => array(
			"ACTIVE" => "Y",
			"IBLOCK_TYPE_ID" => $arCurrentValues['IBLOCK_TYPE']
		),
		'select' => array('ID','NAME')
	));
	while($arr = $rsIBlock->fetch())
		$arIBlock[$arr["ID"]] = "[".$arr["ID"]."] ".$arr["NAME"];

}

$arComponentParameters = array(
	"GROUPS" => array(
		"BASE" => array(
			"NAME" => Loc::getMessage("PAR_IBFROM_G_MAIN"),
			"SORT" => 10
		),
		"ELEMENT" => array(
			"NAME" => Loc::getMessage("PAR_IBFROM_G_ELEMENT"),
			"SORT" => 20
		),
	),
	"PARAMETERS" => array(
		'IBLOCK_TYPE' => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("PAR_IBFROM_IBLOCK_TYPE"),
			"TYPE" => "LIST",
			"VALUES" => $arIBlockType,
			"REFRESH" => "Y"
		),
		'IBLOCK_ID' => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("PAR_IBFROM_IBLOCK_ID"),
			"TYPE" => "LIST",
			"VALUES" => $arIBlock,
			"REFRESH" => "Y"
		),
		'IBLOCK_CODE' => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("PAR_IBFROM_IBLOCK_CODE"),
			"TYPE" => "STRING",
			"REFRESH" => "Y"
		),
		/*'SAVE_SESSION' => array(
			"PARENT" => "BASE",
			"NAME" => Loc::getMessage("PAR_IBFROM_SAVE_IN_SESSION"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N"
		),
		'FORM_ID' => array(
			"PARENT" => "BASE",
			"NAME" => Loc::getMessage("PAR_IBFROM_FORM_ID"),
			"TYPE" => "STRING",
			"DEFAULT" => md5(time())
		),*/
		'NOT_CREATE_ELEMENT' => array(
			"PARENT" => "ELEMENT",
			"NAME" => GetMessage("PAR_IBFROM_NOT_CREATE_ELEMENT"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N"
		),
		'REDIRECT_AFTER_SUCCESS' => array(
			"PARENT" => "ELEMENT",
			"NAME" => GetMessage("PAR_IBFROM_REDIRECT_AFTER_SUCCESS"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N"
		),
		'PARENT_SECTION' => array(
			"PARENT" => "ELEMENT",
			"NAME" => GetMessage("PAR_IBFROM_PARENT_SECTION"),
			"TYPE" => "INT",
			"DEFAULT" => ""
		),
		'PARENT_SECTION_CODE' => array(
			"PARENT" => "ELEMENT",
			"NAME" => GetMessage("PAR_IBFROM_PARENT_SECTION_CODE"),
			"TYPE" => "STRING",
			"DEFAULT" => ""
		),
		'EDIT_ELEMENT' => array(
			"PARENT" => "ELEMENT",
			"NAME" => GetMessage("PAR_IBFROM_EDIT_ELEMENT"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N"
		),
		'ELEMENT_ID' => array(
			"PARENT" => "ELEMENT",
			"NAME" => GetMessage("PAR_IBFROM_ELEMENT_ID"),
			"TYPE" => "STRING",
			"DEFAULT" => ""
		),
	)
);

if(isset($arCompParam) && false !== $arCompParam) {
	$arComponentParameters['GROUPS'] = array_merge($arComponentParameters['GROUPS'],$arCompParam['GROUPS']);
	$arComponentParameters['PARAMETERS'] = array_merge($arComponentParameters['PARAMETERS'],$arCompParam['PARAMETERS']);
}
