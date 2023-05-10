<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if(!empty($arResult['ITEMS'])){
	foreach ($arResult['ITEMS'] as $key => $item) {
		$db_props = CIBlockElement::GetProperty($item['IBLOCK_ID'], $item['ID']);

		while ($prop = $db_props->fetch()) {
			$arResult['ITEMS'][$key]['PROPERTIES'][$prop['CODE']] = $prop;
		}
	}
}


