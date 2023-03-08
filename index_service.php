<?if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
?>
<? $APPLICATION->IncludeComponent(
	"citrus.arealty:catalog.section.list",
	"services",
	array(
		"COMPONENT_TEMPLATE" => "services",
		"IBLOCK_TYPE" => "realty",
		"IBLOCK_ID" => CModule::IncludeModule("citrus.arealty")?\Citrus\Arealty\Helper::getIblock("services"):"",
		"SECTION_ID" => "",
		"SECTION_CODE" => "",
		"COUNT_ELEMENTS" => "N",
		"TOP_DEPTH" => "2",
		"SECTION_FIELDS" => array(
			0 => "PICTURE",
			1 => "DETAIL_PICTURE",
		),
		"SECTION_USER_FIELDS" => array(
			0 => "",
			1 => "",
		),
		"VIEW_MODE" => "LINE",
		"SHOW_PARENT_NAME" => "Y",
		"SECTION_URL" => "",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "36000000",
		"CACHE_GROUPS" => "Y",
		"ADD_SECTIONS_CHAIN" => "N",
	),
	false
); ?>