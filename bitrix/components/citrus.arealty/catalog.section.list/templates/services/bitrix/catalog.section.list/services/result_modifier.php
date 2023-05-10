<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$arResult["SECTIONS"] = $arResult["SECTIONS"] ?: [];
$arSort = Array("SORT" => "ASC");
$arSelect = Array("NAME", "DETAIL_PAGE_URL");
$arFilter = Array(
	"IBLOCK_ID" => $arParams["IBLOCK_ID"],
	"ACTIVE" => "Y",
	"GLOBAL_ACTIVE" => "Y",
	"SECTION_ID" => array_column($arResult['SECTIONS'], 'ID'),
);

$res = CIBlockElement::GetList($arSort, $arFilter, false, false, $arSelect);
$elements = [];
while ($element = $res->GetNext())
{
	$elements[$element['IBLOCK_SECTION_ID']][] = $element;
}

foreach ($arResult["SECTIONS"] as &$arSection)
{
	if ((int)$arSection['DETAIL_PICTURE'] > 0)
	{
		if ($pic = CFile::ResizeImageGet(
			$arSection['DETAIL_PICTURE'],
			['width' => 397, 'height' => 212],
			BX_RESIZE_IMAGE_EXACT,
			$bInitSizes = true
		))
		{
			$arSection['PICTURE'] = array_change_key_case($pic, CASE_UPPER);
		}
	}

	$arSection['ITEMS'] = $elements[$arSection['ID']];
}

$arParams['SHOW_IBLOCK_DESRIPTION'] = !empty($arParams["SHOW_IBLOCK_DESRIPTION"]) && $arParams["SHOW_IBLOCK_DESRIPTION"] == 'Y';
if ($arParams['SHOW_IBLOCK_DESRIPTION'])
{
	$arResult["DESCRIPTION"] = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], 'DESCRIPTION');
}
