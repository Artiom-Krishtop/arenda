<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use Citrus\Arealty\SortOrder;
use Citrus\Arealty\Cache;
use Citrus\Arealty\Helper;

Loc::loadMessages(__FILE__);

/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);

global $APPLICATION;
if (0 < (int)$arResult["VARIABLES"]["SECTION_ID"])
{
	$APPLICATION->SetPageProperty("activeCatalogSectionID", $arResult["VARIABLES"]["SECTION_ID"]);
	$arFilter["ID"] = $arResult["VARIABLES"]["SECTION_ID"];
} elseif ('' != $arResult["VARIABLES"]["SECTION_CODE"])
{
	$APPLICATION->SetPageProperty("activeCatalogSectionCode", $arResult["VARIABLES"]["SECTION_CODE"]);
	$arFilter["=CODE"] = $arResult["VARIABLES"]["SECTION_CODE"];
}
?>
<?$APPLICATION->IncludeComponent(
	"citrus:realty.favourites",
	"block",
	array(
        "PATH" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["favourites"],
	),
	$component,
	array("HIDE_ICONS" => "Y")
);?>

<?$elementId = $APPLICATION->IncludeComponent(
	"bitrix:catalog.element",
	"catalog_detail",
	array(
		"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
		"IBLOCK_ID" => $arParams["IBLOCK_ID"],
		"PROPERTY_CODE" => $arParams["DETAIL_PROPERTY_CODE"],
		"META_KEYWORDS" => $arParams["DETAIL_META_KEYWORDS"],
		"META_DESCRIPTION" => $arParams["DETAIL_META_DESCRIPTION"],
		"BROWSER_TITLE" => $arParams["DETAIL_BROWSER_TITLE"],
		"BASKET_URL" => $arParams["BASKET_URL"],
		"ACTION_VARIABLE" => $arParams["ACTION_VARIABLE"],
		"PRODUCT_ID_VARIABLE" => $arParams["PRODUCT_ID_VARIABLE"],
		"SECTION_ID_VARIABLE" => $arParams["SECTION_ID_VARIABLE"],
		"PRODUCT_QUANTITY_VARIABLE" => $arParams["PRODUCT_QUANTITY_VARIABLE"],
		"PRODUCT_PROPS_VARIABLE" => $arParams["PRODUCT_PROPS_VARIABLE"],
		"CACHE_TYPE" => $arParams["CACHE_TYPE"],
		"CACHE_TIME" => $arParams["CACHE_TIME"],
		"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
		"SET_TITLE" => $arParams["SET_TITLE"],
		"SET_LAST_MODIFIED" => $arParams["SET_LAST_MODIFIED"],
		"MESSAGE_404" => $arParams["MESSAGE_404"],
		"SET_STATUS_404" => $arParams["SET_STATUS_404"],
		"SHOW_404" => $arParams["SHOW_404"],
		"FILE_404" => $arParams["FILE_404"],
		"PRICE_CODE" => $arParams["PRICE_CODE"],
		"USE_PRICE_COUNT" => $arParams["USE_PRICE_COUNT"],
		"SHOW_PRICE_COUNT" => $arParams["SHOW_PRICE_COUNT"],
		"PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],
		"PRICE_VAT_SHOW_VALUE" => $arParams["PRICE_VAT_SHOW_VALUE"],
		"USE_PRODUCT_QUANTITY" => $arParams['USE_PRODUCT_QUANTITY'],
		"PRODUCT_PROPERTIES" => $arParams["PRODUCT_PROPERTIES"],
		"ADD_PROPERTIES_TO_BASKET" => (isset($arParams["ADD_PROPERTIES_TO_BASKET"]) ? $arParams["ADD_PROPERTIES_TO_BASKET"] : ''),
		"PARTIAL_PRODUCT_PROPERTIES" => (isset($arParams["PARTIAL_PRODUCT_PROPERTIES"]) ? $arParams["PARTIAL_PRODUCT_PROPERTIES"] : ''),
		"LINK_IBLOCK_TYPE" => $arParams["LINK_IBLOCK_TYPE"],
		"LINK_IBLOCK_ID" => $arParams["LINK_IBLOCK_ID"],
		"LINK_PROPERTY_SID" => $arParams["LINK_PROPERTY_SID"],
		"LINK_ELEMENTS_URL" => $arParams["LINK_ELEMENTS_URL"],

		"OFFERS_CART_PROPERTIES" => $arParams["OFFERS_CART_PROPERTIES"],
		"OFFERS_FIELD_CODE" => $arParams["DETAIL_OFFERS_FIELD_CODE"],
		"OFFERS_PROPERTY_CODE" => $arParams["DETAIL_OFFERS_PROPERTY_CODE"],
		"OFFERS_SORT_FIELD" => $arParams["OFFERS_SORT_FIELD"],
		"OFFERS_SORT_ORDER" => $arParams["OFFERS_SORT_ORDER"],
		"OFFERS_SORT_FIELD2" => $arParams["OFFERS_SORT_FIELD2"],
		"OFFERS_SORT_ORDER2" => $arParams["OFFERS_SORT_ORDER2"],

		"ELEMENT_ID" => $arResult["VARIABLES"]["ELEMENT_ID"],
		"ELEMENT_CODE" => $arResult["VARIABLES"]["ELEMENT_CODE"],
		"SECTION_ID" => $arResult["VARIABLES"]["SECTION_ID"],
		"SECTION_CODE" => $arResult["VARIABLES"]["SECTION_CODE"],
		"SECTION_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["section"],
		"DETAIL_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["element"],
		"PDF_DETAIL_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["pdf"],
		'CONVERT_CURRENCY' => $arParams['CONVERT_CURRENCY'],
		'CURRENCY_ID' => $arParams['CURRENCY_ID'],
		'HIDE_NOT_AVAILABLE' => $arParams["HIDE_NOT_AVAILABLE"],
		'USE_ELEMENT_COUNTER' => $arParams['USE_ELEMENT_COUNTER'],

		'ADD_PICT_PROP' => $arParams['ADD_PICT_PROP'],
		'LABEL_PROP' => $arParams['LABEL_PROP'],
		'OFFER_ADD_PICT_PROP' => $arParams['OFFER_ADD_PICT_PROP'],
		'OFFER_TREE_PROPS' => $arParams['OFFER_TREE_PROPS'],
		'PRODUCT_SUBSCRIPTION' => $arParams['PRODUCT_SUBSCRIPTION'],
		'SHOW_DISCOUNT_PERCENT' => $arParams['SHOW_DISCOUNT_PERCENT'],
		'SHOW_OLD_PRICE' => $arParams['SHOW_OLD_PRICE'],
		'SHOW_MAX_QUANTITY' => $arParams['DETAIL_SHOW_MAX_QUANTITY'],
		'MESS_BTN_BUY' => $arParams['MESS_BTN_BUY'],
		'MESS_BTN_ADD_TO_BASKET' => $arParams['MESS_BTN_ADD_TO_BASKET'],
		'MESS_BTN_SUBSCRIBE' => $arParams['MESS_BTN_SUBSCRIBE'],
		'MESS_BTN_COMPARE' => $arParams['MESS_BTN_COMPARE'],
		'MESS_NOT_AVAILABLE' => $arParams['MESS_NOT_AVAILABLE'],
		'USE_VOTE_RATING' => $arParams['DETAIL_USE_VOTE_RATING'],
		'VOTE_DISPLAY_AS_RATING' => (isset($arParams['DETAIL_VOTE_DISPLAY_AS_RATING']) ? $arParams['DETAIL_VOTE_DISPLAY_AS_RATING'] : ''),
		'USE_COMMENTS' => $arParams['DETAIL_USE_COMMENTS'],
		'BLOG_USE' => (isset($arParams['DETAIL_BLOG_USE']) ? $arParams['DETAIL_BLOG_USE'] : ''),
		'BLOG_URL' => (isset($arParams['DETAIL_BLOG_URL']) ? $arParams['DETAIL_BLOG_URL'] : ''),
		'VK_USE' => (isset($arParams['DETAIL_VK_USE']) ? $arParams['DETAIL_VK_USE'] : ''),
		'VK_API_ID' => (isset($arParams['DETAIL_VK_API_ID']) ? $arParams['DETAIL_VK_API_ID'] : 'API_ID'),
		'FB_USE' => (isset($arParams['DETAIL_FB_USE']) ? $arParams['DETAIL_FB_USE'] : ''),
		'FB_APP_ID' => (isset($arParams['DETAIL_FB_APP_ID']) ? $arParams['DETAIL_FB_APP_ID'] : ''),
		'BRAND_USE' => (isset($arParams['DETAIL_BRAND_USE']) ? $arParams['DETAIL_BRAND_USE'] : 'N'),
		'BRAND_PROP_CODE' => (isset($arParams['DETAIL_BRAND_PROP_CODE']) ? $arParams['DETAIL_BRAND_PROP_CODE'] : ''),
		'DISPLAY_NAME' => (isset($arParams['DETAIL_DISPLAY_NAME']) ? $arParams['DETAIL_DISPLAY_NAME'] : ''),
		'ADD_DETAIL_TO_SLIDER' => (isset($arParams['DETAIL_ADD_DETAIL_TO_SLIDER']) ? $arParams['DETAIL_ADD_DETAIL_TO_SLIDER'] : ''),
		'TEMPLATE_THEME' => (isset($arParams['TEMPLATE_THEME']) ? $arParams['TEMPLATE_THEME'] : ''),
		"ADD_SECTIONS_CHAIN" => (isset($arParams["ADD_SECTIONS_CHAIN"]) ? $arParams["ADD_SECTIONS_CHAIN"] : ''),
		"ADD_ELEMENT_CHAIN" => (isset($arParams["ADD_ELEMENT_CHAIN"]) ? $arParams["ADD_ELEMENT_CHAIN"] : ''),
		"DISPLAY_PREVIEW_TEXT_MODE" => (isset($arParams['DETAIL_DISPLAY_PREVIEW_TEXT_MODE']) ? $arParams['DETAIL_DISPLAY_PREVIEW_TEXT_MODE'] : ''),
		"DETAIL_PICTURE_MODE" => (isset($arParams['DETAIL_DETAIL_PICTURE_MODE']) ? $arParams['DETAIL_DETAIL_PICTURE_MODE'] : ''),
		'SET_CANONICAL_URL' => $arParams['DETAIL_SET_CANONICAL_URL'],

		'IS_JK' => 'Y',
	),
	$component
);?><?

if (0 < $elementId)
{
	if ($arParams['DETAIL_DISPLAY_NUMBER'] == 'Y')
	{
		$APPLICATION->AddViewContent('addTitle', Loc::getMessage('CITRUS_AREALTY_DETAIL_TITLE_NUMBER', array('#NUM#' => $elementId)));
	}
}

if (0 < $elementId && 'Y' == $arParams['DETAIL_SHOW_SIMILAR_OFFERS'])
{
	global $seeAlsoFilter;

	$seeAlsoFilter = array(
		"!ID" => $elementId,
		"SECTION_ID" => \Citrus\Arealty\Helper::getLastSection(),
	);

	// get similar props list
	$resSection = \CIBlockSection::GetList(
		array(),
		array(
			'ID' => \Citrus\Arealty\Helper::getLastSection(),
			'IBLOCK_ID' => $arParams['IBLOCK_ID'],
		),
		false,
		array(
			"UF_SIMILAR_PROPS"
		)
	);
	$rowSection = $resSection->GetNext();
	$sectionSimilarProps = $rowSection["~UF_SIMILAR_PROPS"];
	if (trim($sectionSimilarProps) != "") {
		$sectionSimilarProps = json_decode($sectionSimilarProps, true);
	}

	/**
     * ѕровер€ет, есть ли записи при выборке с указанным фильтром
     *
	 * @param array $filter
	 * @return bool
	 */
	$hasResults = function($filter) use ($arParams) {
		$filter['IBLOCK_ID'] = $arParams['IBLOCK_ID'];
		$rsElement = CIBlockElement::GetList(
			array(),
			$filter,
			$arGroupBy = false,
			$arNavStartParams = array('nTopCount' => 1),
			$arSelectFields = Array("ID")
		);
		return (bool)$rsElement->SelectedRowsCount();
	};

	if (empty($sectionSimilarProps))
	{
		$filter = \Citrus\Arealty\Helper::getLastFilter();
	}
	else
	{
		// init filter for similar props
		$filter = array();
		foreach ($sectionSimilarProps["CODE"] as $i => $code)
		{
			$resItemProp = \CIBlockElement::GetProperty(
				$arParams['IBLOCK_ID'],
				$elementId,
				array("sort" => "asc"),
				array("CODE" => $code)
			);
			$itemProp = $resItemProp->Fetch();
			if (trim($itemProp["VALUE"]) == "")
			{
				continue;
			}
			if (empty($sectionSimilarProps["RANGE"][$i]))
			{
				$filter["PROPERTY_" . $code] = $itemProp["VALUE"];
			}
			else
			{
				$filter[] = array(
					"LOGIC" => "AND",
					array(">=PROPERTY_" . $code => $itemProp["VALUE"] - $sectionSimilarProps["RANGE"][$i]),
					array("<PROPERTY_" . $code => $itemProp["VALUE"] + $sectionSimilarProps["RANGE"][$i]),
				);
			}
		}
	}

	// есть ли есть другие предложени€ с тем же типом сделки, что у текущего
	if (is_array($filter) && $hasResults(array_merge($seeAlsoFilter, $filter)))
	{
		$seeAlsoFilter = array_merge($seeAlsoFilter, $filter);
	}
	// если записей нет, даже с другими типами сделки, скроем блок похожих предложений
	elseif (!$hasResults($seeAlsoFilter))
	{
		$seeAlsoFilter = array();
	}

	if ($seeAlsoFilter)
	{
		?>
		<? $APPLICATION->IncludeComponent(
		"citrus.core:include",
		".default",
		array(
			"AREA_FILE_SHOW" => "file",
			"AREA_FILE_SUFFIX" => "inc",
			"AREA_FILE_RECURSIVE" => "Y",
			"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
			"IBLOCK_ID" => $arParams["IBLOCK_ID"],
			"PATH" => SITE_DIR . "include/offers.php",
			"EDIT_TEMPLATE" => "page_inc.php",
			"EDIT_MODE" => "php",
			"TITLE" => Loc::getMessage("CITRUS_AREALTY_SIMILAR_OFFERS_TITLE"),
			"DESCRIPTION" => Loc::getMessage("CITRUS_AREALTY_SIMILAR_OFFERS_DESC"),
			"PAGE_SECTION" => "Y",
			"WIDGET_REL" => "who-we",
			"COMPONENT_TEMPLATE" => ".default",
			"h" => "h2.h1",
			"PADDING" => "Y",
			"BG_COLOR" => "WHITE",
			"CUT_CONTENT_HEIGHT" => "",
		),
		false
	);
	}
}
?>

<?php if (0 < $elementId) { // ќбъ€влени€ по текущему ∆ 

$complexId = $elementId;
$complexXmlId = Cache::remember(
	'complex_xml_id_' . $complexId, 24*60, function () use ($complexId) {
	Cache::registerIblockCacheTag(Helper::getIblock('complexes'));

	$element = CIBlockElement::GetList(
		Array(),
		Array("ID" => $complexId),
		$arGroupBy = false,
		$arNavStartParams = array('nTopCount' => 1),
		$arSelectFields = array('ID', 'XML_ID')
	)->Fetch();
	return $element['XML_ID'];
});

global $offersFilter;
$offersFilter = array("PROPERTY_complex" => $complexXmlId);

?>
<?$APPLICATION->IncludeComponent(
	"citrus.core:include",
	".default",
	array(
		"AREA_FILE_SHOW" => "component",
		"_COMPONENT" => "bitrix:catalog.section",
		"_COMPONENT_TEMPLATE" => "catalog_carousel",
		"PAGE_SECTION" => "Y",
		"WIDGET_REL" => "who-we",
		"h" => "h2.h1",
		"PADDING" => "Y",
		"BG_COLOR" => "WHITE",
		"COMPOSITE_FRAME_MODE" => "A",
		"COMPOSITE_FRAME_TYPE" => "AUTO",
		"TITLE" => Loc::getMessage("CITRUS_AREALTY_SIMILAR_OFFERS_TITLE_JK"),
		"DESCRIPTION" => Loc::getMessage("CITRUS_AREALTY_SIMILAR_OFFERS_DESC_JK"),

		"IBLOCK_TYPE" => "realty",
		"IBLOCK_ID" => \CModule::IncludeModule("citrus.arealty")?\Citrus\Arealty\Helper::getIblock("offers"):"",
		"SECTION_ID" => 0,
		"SECTION_CODE" => '',
		"ELEMENT_SORT_FIELD" => SortOrder::getField(),
		"ELEMENT_SORT_ORDER" => SortOrder::getOrder(),
		"ELEMENT_SORT_FIELD2" => SortOrder::getField(1),
		"ELEMENT_SORT_ORDER2" => SortOrder::getOrder(1),
		"FILTER_NAME" => "offersFilter",
		"INCLUDE_SUBSECTIONS" => "Y",
		"SHOW_ALL_WO_SECTION" => "Y",
		"PAGE_ELEMENT_COUNT" => "5",
		"LINE_ELEMENT_COUNT" => "1",
		"PROPERTY_CODE" => array("cost", "address", ""),
		"OFFERS_LIMIT" => "5",
		"TEMPLATE_THEME" => "",
		"SECTION_URL" => $arParams["SEF_FOLDER_LINKED"] . "#SECTION_CODE_PATH#/",
		"DETAIL_URL" => $arParams["SEF_FOLDER_LINKED"] . "#SECTION_CODE_PATH#/#ELEMENT_CODE#/",
		"SECTION_ID_VARIABLE" => "SECTION_ID",
		"AJAX_MODE" => "N",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "Y",
		"AJAX_OPTION_HISTORY" => "N",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "36000000",
		"CACHE_GROUPS" => "Y",
		"SET_META_KEYWORDS" => "N",
		"META_KEYWORDS" => "",
		"SET_META_DESCRIPTION" => "N",
		"META_DESCRIPTION" => "",
		"BROWSER_TITLE" => "-",
		"ADD_SECTIONS_CHAIN" => "N",
		"DISPLAY_COMPARE" => "N",
		"SET_TITLE" => "N",
		"SET_STATUS_404" => "N",
		"SHOW_404" => "N",
		"MESSAGE_404" => "",
		"CACHE_FILTER" => "Y",
		"PRICE_CODE" => array(),
		"USE_PRICE_COUNT" => "N",
		"SHOW_PRICE_COUNT" => "1",
		"PRICE_VAT_INCLUDE" => "Y",
		"BASKET_URL" => SITE_DIR . "personal/basket.php",
		"ACTION_VARIABLE" => "action",
		"PRODUCT_ID_VARIABLE" => "id",
		"USE_PRODUCT_QUANTITY" => "N",
		"ADD_PROPERTIES_TO_BASKET" => "Y",
		"PRODUCT_PROPS_VARIABLE" => "prop",
		"PARTIAL_PRODUCT_PROPERTIES" => "N",
		"PRODUCT_PROPERTIES" => array(),
		"PAGER_TEMPLATE" => "",
		"DISPLAY_TOP_PAGER" => "N",
		"DISPLAY_BOTTOM_PAGER" => "N",
		"PAGER_SHOW_ALWAYS" => "N",
		"PAGER_DESC_NUMBERING" => "N",
		"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
		"PAGER_SHOW_ALL" => "N",
		"CURRENCY" => \Citrus\Arealty\Helper::getSelectedCurrency(),
	),
	$component
);?>
<?php }

// заполн€етс€ в шаблоне bitrix:catalog.element
$APPLICATION->ShowViewContent('element-page-bottom');
