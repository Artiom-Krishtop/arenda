<?php

namespace Citrus\Core;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Bitrix\Iblock;
use Bitrix\Main\Localization\Loc;

if (!Loader::includeModule('iblock'))
{
	$this->abortResultCache();
	ShowError(GetMessage("IBLOCK_MODULE_NOT_INSTALLED"));

	return;
}

$arParams['PROPERTY_CODE'] = empty($arParams['PROPERTY_CODE']) ? [
	'title',
	'subtitle',
] : $arParams['PROPERTY_CODE'];
$arSelect = [
	"ID",
	"NAME",
	"IBLOCK_ID",
	"IBLOCK_SECTION_ID",
	"DETAIL_TEXT",
	"DETAIL_TEXT_TYPE",
	"PREVIEW_TEXT",
	"PREVIEW_TEXT_TYPE",
	"PREVIEW_PICTURE",
	"DETAIL_PICTURE",
	"TIMESTAMP_X",
	"ACTIVE_FROM",
	"LIST_PAGE_URL",
	"DETAIL_PAGE_URL",
];
$arFilter = [
	'IBLOCK_LID' => SITE_ID,
	'IBLOCK_ACTIVE' => 'Y',
	'CHECK_PERMISSIONS' => 'Y',
	'IBLOCK_TYPE' => $arParams['IBLOCK_TYPE'],
];
if (!empty($arParams['IBLOCK_ID']))
{
	$arFilter['IBLOCK_ID'] = $arParams['IBLOCK_ID'];
}
if (!empty($arParams['ELEMENT_CODE']))
{
	$arFilter['=CODE'] = $arParams['ELEMENT_CODE'];
}
if (!empty($arParams['ELEMENT_XML_ID']))
{
	$arFilter['=XML_ID'] = $arParams['ELEMENT_XML_ID'];
}
if (!empty($arParams['ELEMENT_ID']))
{
	$arFilter['=ID'] = $arParams['ELEMENT_ID'];
}

if ($this->startResultCache(false))
{
	$rsElement = \CIBlockElement::GetList(['SORT' => 'asc', 'ID' => 'asc'], $arFilter, false, false, $arSelect);
	if ($obElement = $rsElement->GetNextElement())
	{
		$arResult = $obElement->GetFields();
		$arResult['FIELDS'] = [];
		foreach ($arSelect as $code)
		{
			if (array_key_exists($code, $arResult))
			{
				$arResult['FIELDS'][$code] = $arResult[$code];
			}
		}

		Iblock\Component\Tools::getFieldImageData(
			$arResult,
			['PREVIEW_PICTURE', 'DETAIL_PICTURE'],
			Iblock\Component\Tools::IPROPERTY_ENTITY_ELEMENT,
			'IPROPERTY_VALUES'
		);

		$arResult['PROPERTIES'] = $obElement->GetProperties();
		$arResult['DISPLAY_PROPERTIES'] = [];
		foreach ($arParams['PROPERTY_CODE'] as $pid)
		{
			$prop = &$arResult['PROPERTIES'][$pid];
			if (
				(is_array($prop['VALUE']) && count($prop['VALUE']) > 0)
				|| (!is_array($prop['VALUE']) && strlen($prop['VALUE']) > 0)
			)
			{
				$arResult['DISPLAY_PROPERTIES'][$pid] = \CIBlockFormatProperties::GetDisplayValue($arResult, $prop, '');
			}
		}

		$arButtons = \CIBlock::GetPanelButtons(
			$arResult['IBLOCK_ID'],
			$arResult['ID'],
			0,
			['SECTION_BUTTONS' => false, 'SESSID' => false]
		);
		$arResult['EDIT_LINK'] = $arButtons['edit']['edit_element']['ACTION_URL'];

		$resultCacheKeys = [
			'ID',
			'IBLOCK_ID',
			'NAME',
			'IBLOCK_SECTION_ID',
			'IBLOCK',
			'TIMESTAMP_X',
		];
		$this->setResultCacheKeys($resultCacheKeys);

		$this->includeComponentTemplate();
	}
	else
	{
		ShowError(Loc::getMessage("CITRUS_AREALTY_CONTENTBLOCK_NOT_FOUND"));

		$this->abortResultCache();
	}
}