<?php

/** @var CBitrixComponent $this */

use Bitrix\Main\Loader;
use Citrus\Core\Components\ParamsSerializer;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$arResult["MAP_ID"] = uniqid('citrus-objects-map-');

$this->setResultCacheKeys(array());

if (!Loader::includeModule('citrus.arealty'))
{
	return;
}

if ($arParams['FILTER_NAME']
	&& is_array($GLOBALS[$arParams['FILTER_NAME']])
	&& count($GLOBALS[$arParams['FILTER_NAME']])
) {
	$arParams['FILTER'] = $GLOBALS[$arParams['FILTER_NAME']];
}

if (!isset($arParams['FILTER']) || !is_array($arParams['FILTER']))
{
	$arParams['FILTER'] = [];
}

$hasFilter = count($arParams['FILTER']);

if ($hasFilter || $this->startResultCache())
{
	$arFilter = array(
		"IBLOCK_ID" => $arParams["IBLOCK_ID"],
		"ACTIVE" => "Y",
		"INCLUDE_SUBSECTIONS" => "Y",
	);
	if ($arParams["SECTION_ID"]) $arFilter["SECTION_ID"] = $arParams["SECTION_ID"];
	if ($arParams["SECTION_ID"] || $arParams["SECTION_CODE"]) $arFilter['SECTION_GLOBAL_ACTIVE'] = 'Y';
	if (!$arParams['PAGE_ELEMENT_COUNT'] || $arParams['PAGE_ELEMENT_COUNT'] < 1) $arParams['PAGE_ELEMENT_COUNT'] = 500;

	$arSelect = array(
		"ID",
		"IBLOCK_ID",
		"SECTION_ID",
		"SECTION_CODE",
	);

	//ajax load data page
	if ($arParams['AJAX'])
	{
		$arSelect = array_merge(
			$arSelect,
			array(
				"NAME",
				"PREVIEW_PICTURE",
				"DETAIL_PICTURE",
				"DETAIL_PAGE_URL",
			)
		);
		$navParams = array(
			"iNumPage" => $arParams['PAGE'],
			"nPageSize" => $arParams['PAGE_ELEMENT_COUNT'],
		);
		if ($hasFilter)
		{
			$arFilter = array_merge($arFilter, $arParams['FILTER']);
		}

		$res = CIBlockElement::GetList(array(), $arFilter, false, $navParams, $arSelect);
		$arResult['ITEMS'] = array();
		while ($obItem = $res->GetNextElement())
		{
			$arItem = $obItem->GetFields();
			// todo запрашивать не все свойства
			$arItem['PROPERTIES'] = $obItem->GetProperties();
			if ($arItem['PREVIEW_PICTURE']) $arItem['PREVIEW_PICTURE'] = CFile::GetFileArray($arItem['PREVIEW_PICTURE']);
			if ($arItem['DETAIL_PICTURE']) $arItem['DETAIL_PICTURE'] = CFile::GetFileArray($arItem['DETAIL_PICTURE']);

			/**
			 * ƒобавл€ем на карту только объекты с координатами чтобы избежать массового геокодировани€
			 * #46502
			 */
			$geodata = $arItem['PROPERTIES']['geodata']['VALUE'];
			if ($geodata instanceof \Citrus\Yandex\Geo\GeoObject)
			{
				if ($geodata->getLatitude() && $geodata->getLongitude())
				{
					$arResult['ITEMS'][] = $arItem;
				}
			}
		}
	}
	else
	{
		if ($hasFilter) {
			$arFilter = array_merge($arFilter, $arParams['FILTER']);
		}
		$res = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
		$arResult['ITEMS_COUNT'] = $res->SelectedRowsCount();
		$arResult['PAGE_COUNT'] = ceil($arResult['ITEMS_COUNT'] / $arParams['PAGE_ELEMENT_COUNT']);

		$arParams['COMPONENT_TEMPLATE'] = $this->getTemplateName();
		$paramsSerializer = new ParamsSerializer();
		$arResult['AJAX_PARAMS'] = $paramsSerializer->serialize($arParams);
	}

	$this->includeComponentTemplate();
}