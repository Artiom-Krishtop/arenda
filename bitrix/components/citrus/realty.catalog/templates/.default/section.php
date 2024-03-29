<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

if (!\Bitrix\Main\Loader::includeModule("citrus.arealty"))
{
	ShowError(GetMessage("CITRUS_REALTY_MODULE_NOT_FOUND"));

	return;
}

use Citrus\Arealty\SortOrder;


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


if ($arParams['USE_FILTER'] == 'Y')
{
	$arFilter = array(
		"IBLOCK_ID" => $arParams["IBLOCK_ID"],
		"ACTIVE" => "Y",
		"GLOBAL_ACTIVE" => "Y",
	);
	global $APPLICATION;
	if (0 < intval($arResult["VARIABLES"]["SECTION_ID"]))
	{
		$APPLICATION->SetPageProperty("activeCatalogSectionID", $arResult["VARIABLES"]["SECTION_ID"]);
		$arFilter["ID"] = $arResult["VARIABLES"]["SECTION_ID"];
	}
	elseif ('' != $arResult["VARIABLES"]["SECTION_CODE"])
	{
		$APPLICATION->SetPageProperty("activeCatalogSectionCode", $arResult["VARIABLES"]["SECTION_CODE"]);
		$arFilter["=CODE"] = $arResult["VARIABLES"]["SECTION_CODE"];
	}

	$obCache = new CPHPCache();
	if ($obCache->InitCache(36000, serialize($arFilter), "/iblock/catalog"))
	{
		$currentSection = $obCache->GetVars();
	}
	elseif ($obCache->StartDataCache())
	{
		$currentSection = array();
		if (\Bitrix\Main\Loader::includeModule("iblock"))
		{
			$dbRes = CIBlockSection::GetList(array(), $arFilter, false, array("ID", "UF_TYPE"));

			if (defined("BX_COMP_MANAGED_CACHE"))
			{
				global $CACHE_MANAGER;
				$CACHE_MANAGER->StartTagCache("/iblock/catalog");

				if ($currentSection = $dbRes->Fetch())
				{
					$CACHE_MANAGER->RegisterTag("iblock_id_" . $arParams["IBLOCK_ID"]);
					$obEnum = new CUserFieldEnum();
					if ($currentSection["UF_TYPE"] && ($enum = $obEnum->GetList(array(), array("ID" => $currentSection["UF_TYPE"]))->Fetch()))
					{
						$currentSection["UF_TYPE_XML_ID"] = $enum["XML_ID"];
					}
					else
					{
						$currentSection["UF_TYPE_XML_ID"] = "cards";
					}
				}
				$CACHE_MANAGER->EndTagCache();
			}
			else
			{
				if ($currentSection = $dbRes->Fetch())
				{
					$obEnum = new CUserFieldEnum();
					if ($currentSection["UF_TYPE"] && ($enum = $obEnum->GetList(array(), array("ID" => $currentSection["UF_TYPE"]))->Fetch()))
					{
						$currentSection["UF_TYPE_XML_ID"] = $enum["XML_ID"];
					}
					else
					{
						$currentSection["UF_TYPE_XML_ID"] = "cards";
					}
				}
				else
				{
					$currentSection = null;
				}
			}
		}
		$obCache->EndDataCache($currentSection);
	}

	if (isset($currentSection))
	{
		$APPLICATION->IncludeComponent(
			"citrus.arealty:smart.filter",
			".default",
			array(
				"CACHE_TYPE" => $arParams["CACHE_TYPE"],
				"CACHE_TIME" => $arParams["CACHE_TIME"],
				"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
				"DISPLAY_ELEMENT_COUNT" => "N",
				"FILTER_NAME" => $arParams["FILTER_NAME"],
				"FILTER_VIEW_MODE" => "vertical",
				"IBLOCK_ID" => $arParams["IBLOCK_ID"],
				"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
				"INSTANT_RELOAD" => "N",
				"POPUP_POSITION" => "left",
				"SAVE_IN_SESSION" => "N",
				"SECTION_ID" => $currentSection["ID"],
				"TEMPLATE_THEME" => $arParams["TEMPLATE_THEME"],
				"XML_EXPORT" => "Y",
				"PRICE_CODE" => $arParams["PRICE_CODE"],
				//"SEF_MODE" => $arParams["SEF_MODE"],
				//"SEF_RULE" => $arResult["FOLDER"] . $arResult["URL_TEMPLATES"]["smart_filter"],
				//"SMART_FILTER_PATH" => $arResult["VARIABLES"]["SMART_FILTER_PATH"],
				"PAGER_PARAMS_NAME" => $arParams["PAGER_PARAMS_NAME"],
			),
			$component,
			array('HIDE_ICONS' => 'Y')
		);
	}
}
?>
<? $APPLICATION->IncludeComponent(
	"bitrix:catalog.section.list",
	"",
	array(
		"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
		"IBLOCK_ID" => $arParams["IBLOCK_ID"],
		"SECTION_ID" => $arResult["VARIABLES"]["SECTION_ID"],
		"SECTION_CODE" => $arResult["VARIABLES"]["SECTION_CODE"],
		"CACHE_TYPE" => $arParams["CACHE_TYPE"],
		"CACHE_TIME" => $arParams["CACHE_TIME"],
		"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
		"COUNT_ELEMENTS" => $arParams["SECTION_COUNT_ELEMENTS"],
		"TOP_DEPTH" => $arParams["SECTION_TOP_DEPTH"],
		"SECTION_URL" => $arResult["FOLDER"] . $arResult["URL_TEMPLATES"]["section"],
		"VIEW_MODE" => $arParams["SECTIONS_VIEW_MODE"],
		"SHOW_PARENT_NAME" => $arParams["SECTIONS_SHOW_PARENT_NAME"],
		"HIDE_SECTION_NAME" => (isset($arParams["SECTIONS_HIDE_SECTION_NAME"]) ? $arParams["SECTIONS_HIDE_SECTION_NAME"] : "N"),
		"ADD_SECTIONS_CHAIN" => (isset($arParams["ADD_SECTIONS_CHAIN"]) ? $arParams["ADD_SECTIONS_CHAIN"] : '')
	),
	$component
); ?><?
if ($arParams["USE_COMPARE"] == "Y")
{
	?><? $APPLICATION->IncludeComponent(
	"bitrix:catalog.compare.list",
	"",
	array(
		"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
		"IBLOCK_ID" => $arParams["IBLOCK_ID"],
		"NAME" => $arParams["COMPARE_NAME"],
		"DETAIL_URL" => $arResult["FOLDER"] . $arResult["URL_TEMPLATES"]["element"],
		"COMPARE_URL" => $arResult["FOLDER"] . $arResult["URL_TEMPLATES"]["compare"],
	),
	$component
); ?><?
}

$APPLICATION->ShowViewContent('sort-area');

$intSectionID = 0;

SortOrder::setContext($arParams["IBLOCK_ID"], array($arResult["VARIABLES"]["SECTION_ID"], $arResult["VARIABLES"]["SECTION_CODE"]));

?>
<? $APPLICATION->IncludeComponent(
	"citrus:realty.favourites",
	"block",
	array(
		"PATH" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["favourites"],
	),
	false,
	array('HIDE_ICONS' => 'Y')
); ?>

<? $intSectionID = $APPLICATION->IncludeComponent(
	"bitrix:catalog.section",
	"catalog_" . (isset($currentSection) ? $currentSection["UF_TYPE_XML_ID"] : 'cards'),
	array(
		"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
		"IBLOCK_ID" => $arParams["IBLOCK_ID"],
		"ELEMENT_SORT_FIELD" => SortOrder::getField(),
		"ELEMENT_SORT_ORDER" => SortOrder::getOrder(),
		"ELEMENT_SORT_FIELD2" => SortOrder::getField(1),
		"ELEMENT_SORT_ORDER2" => SortOrder::getOrder(1),
		"PROPERTY_CODE" => $arParams["LIST_PROPERTY_CODE"],
		"META_KEYWORDS" => $arParams["LIST_META_KEYWORDS"],
		"META_DESCRIPTION" => $arParams["LIST_META_DESCRIPTION"],
		"BROWSER_TITLE" => $arParams["LIST_BROWSER_TITLE"],
		"INCLUDE_SUBSECTIONS" => $arParams["INCLUDE_SUBSECTIONS"],
		"BASKET_URL" => $arParams["BASKET_URL"],
		"ACTION_VARIABLE" => $arParams["ACTION_VARIABLE"],
		"PRODUCT_ID_VARIABLE" => $arParams["PRODUCT_ID_VARIABLE"],
		"SECTION_ID_VARIABLE" => $arParams["SECTION_ID_VARIABLE"],
		"PRODUCT_QUANTITY_VARIABLE" => $arParams["PRODUCT_QUANTITY_VARIABLE"],
		"PRODUCT_PROPS_VARIABLE" => $arParams["PRODUCT_PROPS_VARIABLE"],
		"FILTER_NAME" => $arParams["FILTER_NAME"],
		"CACHE_TYPE" => $arParams["CACHE_TYPE"],
		"CACHE_TIME" => $arParams["CACHE_TIME"],
		"CACHE_FILTER" => $arParams["CACHE_FILTER"],
		"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
		"SET_TITLE" => $arParams["SET_TITLE"],
		"MESSAGE_404" => $arParams["MESSAGE_404"],
		"SET_STATUS_404" => $arParams["SET_STATUS_404"],
		"SHOW_404" => $arParams["SHOW_404"],
		"FILE_404" => $arParams["FILE_404"],
		"DISPLAY_COMPARE" => $arParams["USE_COMPARE"],
		"PAGE_ELEMENT_COUNT" => $arParams["PAGE_ELEMENT_COUNT"],
		"LINE_ELEMENT_COUNT" => $arParams["LINE_ELEMENT_COUNT"],
		"PRICE_CODE" => $arParams["PRICE_CODE"],
		"USE_PRICE_COUNT" => $arParams["USE_PRICE_COUNT"],
		"SHOW_PRICE_COUNT" => $arParams["SHOW_PRICE_COUNT"],

		"PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],
		"USE_PRODUCT_QUANTITY" => $arParams['USE_PRODUCT_QUANTITY'],
		"ADD_PROPERTIES_TO_BASKET" => (isset($arParams["ADD_PROPERTIES_TO_BASKET"]) ? $arParams["ADD_PROPERTIES_TO_BASKET"] : ''),
		"PARTIAL_PRODUCT_PROPERTIES" => (isset($arParams["PARTIAL_PRODUCT_PROPERTIES"]) ? $arParams["PARTIAL_PRODUCT_PROPERTIES"] : ''),
		"PRODUCT_PROPERTIES" => $arParams["PRODUCT_PROPERTIES"],

		"DISPLAY_TOP_PAGER" => $arParams["DISPLAY_TOP_PAGER"],
		"DISPLAY_BOTTOM_PAGER" => $arParams["DISPLAY_BOTTOM_PAGER"],
		"PAGER_TITLE" => $arParams["PAGER_TITLE"],
		"PAGER_SHOW_ALWAYS" => $arParams["PAGER_SHOW_ALWAYS"],
		"PAGER_TEMPLATE" => $arParams["PAGER_TEMPLATE"],
		"PAGER_DESC_NUMBERING" => $arParams["PAGER_DESC_NUMBERING"],
		"PAGER_DESC_NUMBERING_CACHE_TIME" => $arParams["PAGER_DESC_NUMBERING_CACHE_TIME"],
		"PAGER_SHOW_ALL" => $arParams["PAGER_SHOW_ALL"],

		"SECTION_ID" => $arResult["VARIABLES"]["SECTION_ID"],
		"SECTION_CODE" => $arResult["VARIABLES"]["SECTION_CODE"],
		"SECTION_URL" => $arResult["FOLDER"] . $arResult["URL_TEMPLATES"]["section"],
		"DETAIL_URL" => $arResult["FOLDER"] . $arResult["URL_TEMPLATES"]["element"],
		'CONVERT_CURRENCY' => $arParams['CONVERT_CURRENCY'],
		'CURRENCY_ID' => $arParams['CURRENCY_ID'],
		'HIDE_NOT_AVAILABLE' => $arParams["HIDE_NOT_AVAILABLE"],

		'LABEL_PROP' => $arParams['LABEL_PROP'],
		'ADD_PICT_PROP' => $arParams['ADD_PICT_PROP'],
		'PRODUCT_DISPLAY_MODE' => $arParams['PRODUCT_DISPLAY_MODE'],

		'OFFER_ADD_PICT_PROP' => $arParams['OFFER_ADD_PICT_PROP'],
		'OFFER_TREE_PROPS' => $arParams['OFFER_TREE_PROPS'],
		'PRODUCT_SUBSCRIPTION' => $arParams['PRODUCT_SUBSCRIPTION'],
		'SHOW_DISCOUNT_PERCENT' => $arParams['SHOW_DISCOUNT_PERCENT'],
		'SHOW_OLD_PRICE' => $arParams['SHOW_OLD_PRICE'],
		'MESS_BTN_BUY' => $arParams['MESS_BTN_BUY'],
		'MESS_BTN_ADD_TO_BASKET' => $arParams['MESS_BTN_ADD_TO_BASKET'],
		'MESS_BTN_SUBSCRIBE' => $arParams['MESS_BTN_SUBSCRIBE'],
		'MESS_BTN_DETAIL' => $arParams['MESS_BTN_DETAIL'],
		'MESS_NOT_AVAILABLE' => $arParams['MESS_NOT_AVAILABLE'],

		'TEMPLATE_THEME' => (isset($arParams['TEMPLATE_THEME']) ? $arParams['TEMPLATE_THEME'] : ''),
		"ADD_SECTIONS_CHAIN" => "N",

		"SECTION_USER_FIELDS" => array("UF_TYPE", "UF_PROP_LINK", "UF_SORT_FIELDS"),
		"SHOW_MAP" => "Y",
		"CITRUS_THEME" => \Citrus\Arealty\Helper::getTheme(),
		"EMPTY_LIST_MESSAGE" => $arParams['EMPTY_LIST_MESSAGE'],
	),
	$component
); ?>