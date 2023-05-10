<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

/** @var mixed[] $arCurrentValues */

use Bitrix\Main;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

if(false === \Bitrix\Main\Loader::includeModule('iblock')) {
	ShowError(Loc::getMessage("CITRUS_SMARTFORM_MISSING_IBLOCK_MODULE"));
	return false;
}

$arAvalibleGroup = array("FIELDS","MAIL_SETTINGS","VISUAL");

/**
 * TODO эту функцию надо вынести
 * @param null $path
 * @return string
 */
$getComponentName = function($path = null) {
	if(null === $path)
		$path = str_replace('\\','/', __DIR__);;

	/**
	 * тут могут быть проблемы между unix и windows (надо проверить)
	 */
	$arPath = explode('/',$path);
	$arPath = array_reverse($arPath);

	return $arPath[1] . ":" . $arPath[0];
};

/**
 * При обновлении диалога с параметрами компонента (при смене инфоблока или установке галочки Использовать Использовать Google reCaptcha)
 * происходит перезагрузка формы с параметрами, при этом JS_DATA у CUSTOM-параметров прилетает в кодировке utf-8
 * настройки полей в неправильной кодировке затем используется на форме и сохраняется в параметрах компонента
 * #35002
 */
global $APPLICATION;
if (
	'/bitrix/admin/fileman_component_params.php' == $APPLICATION->GetCurPage(false)
	&& Main\Context::getCurrent()->getRequest()->isAjaxRequest()
)
{
	CUtil::decodeURIComponent($arCurrentValues);
}

$parenComponentName = $getComponentName();
CBitrixComponent::includeComponentClass($parenComponentName);
if(class_exists('CBCitrusIBAddFormComponent')) {
	$obj = new CBCitrusIBAddFormComponent;
	$obj->initComponent($parenComponentName,$arCurrentValues['COMPONENT_TEMPLATE']);
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
		$arCurrentValues['IBLOCK_ID'] = CBCitrusIBAddFormComponent::getIblock($arCurrentValues['IBLOCK_CODE']);
	}

	if (
		isset($arCurrentValues['IBLOCK_ID']) && (int)$arCurrentValues['IBLOCK_ID'] > 0
	) {
		$iblockId = (int)$arCurrentValues['IBLOCK_ID'];
		try {
			$defaultValue = $obj->GetFieldData($iblockId, $arCurrentValues, 'citrus:smartform');
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
		"FRONTEND" => array(
			"NAME" => Loc::getMessage("PAR_IBFROM_G_FRONTEND"),
			"SORT" => 30
		)
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
		'SAVE_SESSION' => array(
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
		),
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
		'USE_GOOGLE_RECAPTCHA' => array(
			"NAME" => GetMessage("PAR_IBFROM_USE_GOOGLE_RECAPTCHA"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
			"REFRESH" => "Y"
		),
		'AJAX' => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("PAR_IBFROM_AJAX"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		)
	)
);

if(
	isset($arCurrentValues['USE_GOOGLE_RECAPTCHA'])
	&& 'Y' == $arCurrentValues['USE_GOOGLE_RECAPTCHA']
) {
	$arComponentParameters['PARAMETERS']['GOOGLE_RECAPTCHA_PUBLIC_KEY'] = array(
		"NAME" => GetMessage("PAR_IBFROM_GOOGLE_RECAPTCHA_PUBLIC_KEY"),
		"TYPE" => "STRING",
		"DEFAULT" => ""
	);
	$arComponentParameters['PARAMETERS']['GOOGLE_RECAPTCHA_PRIVATE_KEY'] = array(
		"NAME" => GetMessage("PAR_IBFROM_GOOGLE_RECAPTCHA_PRIVATE_KEY"),
		"TYPE" => "STRING",
		"DEFAULT" => ""
	);
}

if(isset($arCompParam) && false !== $arCompParam) {
	$arComponentParameters['GROUPS'] = array_merge($arComponentParameters['GROUPS'],$arCompParam['GROUPS']);
	$arComponentParameters['PARAMETERS'] = array_merge($arComponentParameters['PARAMETERS'],$arCompParam['PARAMETERS']);
}
