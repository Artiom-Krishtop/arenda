<?php

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

/** @var $this CBitrixComponent */

if (!\Bitrix\Main\Loader::includeModule("citrus.arealty"))
{
	return false;
}

$iblockId = $arParams["IBLOCK_ID"];
if (!is_numeric($iblockId))
{
	try
	{
		$iblockId = \Citrus\Arealty\Helper::getIblock($iblockId, $_GET['src_site'] ?: null);
	}
	catch (\Exception $e)
	{

	}
}
else
{
	$iblockId = (int)$iblockId;
}

if(!isset($arParams["CACHE_TIME"]))
	$arParams["CACHE_TIME"] = 36000000;

if($this->startResultCache($arParams["CACHE_TIME"], array())){
	$arOrder = array("SORT" => "ASC", "date_active_from" => "desc");
	$arFilter = array(
		"IBLOCK_ID" => $iblockId,
	);
	if(is_numeric($arParams['ID'])) {
		$arFilter['ID'] = $arParams['ID'];
	} else {
		$arFilter['CODE'] = $arParams['ID'];
	}
	
	if (!is_array($arParams["FIELD_CODE"]))
	{
		$arParams["FIELD_CODE"] = [];
	}
	$arSelect = array_merge($arParams["FIELD_CODE"], array(
		"ID",
		"IBLOCK_ID",
		"IBLOCK_SECTION_ID",
		"NAME",
		"PREVIEW_TEXT",
		"PREVIEW_PICTURE",
		"PROPERTY_*"
	));
	$res = CIBlockElement::GetList($arOrder, $arFilter, false, false, $arSelect);
	if($obElement = $res->GetNextElement()){
		$arResult = $obElement->GetFields();
		$arResult["PROPERTIES"] = $obElement->GetProperties();
		
		$arButtons = CIBlock::GetPanelButtons(
			$arResult["IBLOCK_ID"],
			$arResult["ID"],
			0,
			array("SESSID"=>false, "CATALOG"=>true)
		);
		$arResult["EDIT_LINK"] = $arButtons["edit"]["edit_element"]["ACTION_URL"];
		$arResult["DELETE_LINK"] = $arButtons["edit"]["delete_element"]["ACTION_URL"];
		
		$this->IncludeComponentTemplate();
	}
	else
	{
		$this->endResultCache();
	}

	
}
