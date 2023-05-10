<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();?>
<? $APPLICATION->IncludeComponent(
	"citrus.custom:bigdata.map",
	"",
	array(
		"CACHE_TYPE" => "Y",
		"CACHE_TIME" => 36000000,
		"COMPONENT_TEMPLATE" => "",
		"IBLOCK_TYPE" => "realty",
		"IBLOCK_ID" => $arParams['MAP_IBLOCK_ID'],
		"SECTION_ID" => $APPLICATION->GetPageProperty('mapCatalogSectionId'),
		"PAGE_ELEMENT_COUNT" => "",
		"FILTER_NAME" => $APPLICATION->GetPageProperty('mapCatalogFilterName'),
	),
	false
); ?>