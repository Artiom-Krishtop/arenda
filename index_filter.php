<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

$realtyModuleIncluded = \Bitrix\Main\Loader::includeModule('citrus.arealty');

?>

<?/* $APPLICATION->IncludeComponent(
	"citrus.arealty:catalog.section.list",
	"line-sections",
	Array(
		"ADD_SECTIONS_CHAIN" => "N",
		"CACHE_GROUPS" => "Y",
		"CACHE_TIME" => "36000000",
		"CACHE_TYPE" => "A",
		"COUNT_ELEMENTS" => "Y",
		"IBLOCK_ID" => $realtyModuleIncluded ? Citrus\Arealty\Helper::getIblock("offers") : false,
		"IBLOCK_TYPE" => "realty",
		"SECTION_CODE" => "",
		"SECTION_FIELDS" => array(
			0 => "PICTURE",
			1 => "",
		),
		"SECTION_ID" => "",
		"SECTION_URL" => "",
		"SECTION_USER_FIELDS" => array(
			0 => "UF_SECTION_COLOR",
			1 => "",
		),
		"SHOW_PARENT_NAME" => "Y",
		"TOP_DEPTH" => "1",
		"VIEW_MODE" => "LINE",
        "ACTIVE_SECTION" => $realtyModuleIncluded ? reset(\Citrus\Arealty\Iblock::getSectionCodes("offers", "Y")) : '',
        "ALIGN_LEFT" => 'Y',
	),
	$component
); */?>

<?$APPLICATION->IncludeComponent(
	//"citrus.arealty:smart.filter",
	"travelsoft:smart.filter",
	"",
	array(
		"CACHE_GROUPS" => "Y",
		"CACHE_TIME" => "36000000",
		"CACHE_TYPE" => "A",
		"COMPONENT_TEMPLATE" => ".default",
		"DISPLAY_ELEMENT_COUNT" => "N",
		"FILTER_NAME" => "arrFilter",
		"FILTER_VIEW_MODE" => "vertical",
		//"IBLOCK_ID" => $realtyModuleIncluded ? Citrus\Arealty\Helper::getIblock("offers") : false,
		"IBLOCK_ID" => 13,
		"IBLOCK_TYPE" => "realty",
		"INSTANT_RELOAD" => "N",
		"PAGER_PARAMS_NAME" => "arrPager",
		"POPUP_POSITION" => "left",
		"SAVE_IN_SESSION" => "N",
		//"SECTION_CODE" => $realtyModuleIncluded ? reset(\Citrus\Arealty\Iblock::getSectionCodes("offers", "Y")) : '',
		"SECTION_CODE" => "",
		"SECTION_CODE_PATH" => "",
		"SECTION_DESCRIPTION" => "-",
		"SECTION_ID" => "",
		"SECTION_TITLE" => "-",
		"SEF_MODE" => "N",
		"SEF_RULE" => "",
		"SMART_FILTER_PATH" => "#SECTION_CODE_PATH#/filter/#SMART_FILTER_PATH#/apply/",
		"XML_EXPORT" => "N",
		"FORM_ACTION_ON_SECTION_PAGE" => "Y",
		"FORM_ACTION" => "/predlozhenija/offers/"
	),
	$component
);?>

