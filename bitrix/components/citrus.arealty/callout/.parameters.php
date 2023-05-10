<?php

use Citrus\Arealty\Helper;
use Bitrix\Main\Localization\Loc;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

/** @var array $arCurrentValues */

if (!\Bitrix\Main\Loader::includeModule("iblock"))
{
	return;
}

if (!\Bitrix\Main\Loader::includeModule("citrus.arealty"))
{
	return;
}

$iblockTypes = CIBlockParameters::GetIBlockTypes();

$iblockId = $arCurrentValues["IBLOCK_ID"];
if (!is_numeric($iblockId))
{
	try
	{
		$iblockId = (int)Helper::getIblock($iblockId, isset($_GET['src_site']) ? $_GET['src_site'] : null);
	}
	catch (\Exception $e)
	{

	}
}
else
{
	$iblockId = (int)$iblockId;
}

$iblocks = array();
$iblocksIterator = CIBlock::GetList(
	array("sort" => "asc"),
	array("TYPE" => $arCurrentValues["IBLOCK_TYPE"], "ACTIVE" => "Y")
);
while ($arr = $iblocksIterator->Fetch())
{
	$iblocks[$arr["ID"]] = "[" . $arr["ID"] . "] " . $arr["NAME"];
}

$blocks = array();
if ($iblockId)
{
	$elementsIterator = CIBlockElement::GetList(
		Array("SORT" => "ASC"),
		Array("IBLOCK_ID" => $iblockId),
		$arGroupBy = false,
		$arNavStartParams = false,
		$arSelectFields = Array("ID", "CODE", "NAME")
	);
	while ($element = $elementsIterator->GetNext())
	{
		$blocks[$element['CODE']] = $element['NAME'];
	}
}

$arComponentParameters = array(
	"GROUPS" => array(
	),
	"PARAMETERS" => array(
		"VARIABLE_ALIASES" => array(),
		"IBLOCK_TYPE" => array(
			"PARENT" => "BASE",
			"NAME" => Loc::getMessage("CITRUS_REALTY_CALLOUT_IBLOCK_TYPE"),
			"TYPE" => "LIST",
			"VALUES" => $iblockTypes,
			"REFRESH" => "Y",
		),
		"IBLOCK_ID" => array(
			"PARENT" => "BASE",
			"NAME" => Loc::getMessage("CITRUS_REALTY_CALLOUT_IBLOCK"),
			"TYPE" => "LIST",
			"ADDITIONAL_VALUES" => "Y",
			"VALUES" => $iblocks,
			"REFRESH" => "Y",
		),
		"ID" => array(
			"PARENT" => "BASE",
			"NAME" => Loc::getMessage("CITRUS_REALTY_CALLOUT_BLOCK"),
			"TYPE" => "LIST",
			"ADDITIONAL_VALUES" => "Y",
			"VALUES" => $blocks,
		),
	),
);

