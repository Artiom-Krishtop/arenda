<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

/** @var array $arParams Параметры, чтение/изменение не затрагивает одноименный член компонента. */
/** @var array $arResult Результат, чтение/изменение не затрагивает одноименный член класса компонента. */
/** @var string $componentPath Путь к папке с компонентом от DOCUMENT_ROOT (например /bitrix/components/bitrix/iblock.list). */
/** @var CBitrixComponent $component Ссылка на $this. */
/** @var CBitrixComponent $this Ссылка на текущий вызванный компонент, можно использовать все методы класса. */
/** @var string $epilogFile Путь к файлу component_epilog.php относительно DOCUMENT_ROOT */
/** @var string $templateName Имя шаблона компонента (например: .dеfault) */
/** @var string $templateFile Путь к файлу шаблона от DOCUMENT_ROOT (напр. /bitrix/components/bitrix/iblock.list/templates/.default/template.php) */
/** @var string $templateFolder Путь к папке с шаблоном от DOCUMENT_ROOT (напр. /bitrix/components/bitrix/iblock.list/templates/.default) */
/** @var array $templateData Обратите внимание, таким образом можно передать данные из template.php в файл component_epilog.php, причем эти данные закешируются и будут доступны в component_epilog.php на каждом хите */

CJsCore::Init('magnificPopup');

Loc::loadMessages(__DIR__ . '/template.php');

$APPLICATION->SetPageProperty('show_title', 'Y');

if ($arResult['OFFERS'])
{
	ob_start();

	?><?$APPLICATION->IncludeComponent(
		"bitrix:catalog.section",
		"catalog_carousel",
		Array(
			"COMPONENT_TEMPLATE" => "switch_view",
			"IBLOCK_TYPE" => "realty",
			"IBLOCK_ID" => \Bitrix\Main\Loader::includeModule("citrus.arealty") ? \Citrus\Arealty\Helper::getIblock('offers') : '',
			"SECTION_ID" => $arResult['OFFERS'],
			"SECTION_CODE" => "",
			"SECTION_USER_FIELDS" => array(0 => "", 1 => "",),
			"ELEMENT_SORT_FIELD" => "ACTIVE_FROM",
			"ELEMENT_SORT_ORDER" => "DESC",
			"ELEMENT_SORT_FIELD2" => "id",
			"ELEMENT_SORT_ORDER2" => "desc",
			"FILTER_NAME" => "",
			"INCLUDE_SUBSECTIONS" => "Y",
			"SHOW_ALL_WO_SECTION" => "Y",
			"PAGE_ELEMENT_COUNT" => "8",
			"LINE_ELEMENT_COUNT" => "",
			"PROPERTY_CODE" => array(0 => "contact", 1 => "cost", 3 => "district", 4 => "rooms", 5 => "floor", 6 => "floors", 7 => "common_area", 8 => "living_area", 9 => "kitchen_area", 10 => "balcony", 11 => "wc", 12 => "condition", 13 => "documents", 14 => "house_type", 15 => "text_location", 16 => "text_prices", 17 => "text_mortage", 18 => "quick_sale", 19 => "",),
			"OFFERS_LIMIT" => "5",
			"TEMPLATE_THEME" => "blue",
			"MESS_BTN_BUY" => "",
			"MESS_BTN_ADD_TO_BASKET" => "",
			"MESS_BTN_SUBSCRIBE" => "",
			"MESS_BTN_DETAIL" => "",
			"MESS_NOT_AVAILABLE" => "",
			"SECTION_URL" => "",
			"DETAIL_URL" => "",
			"SECTION_ID_VARIABLE" => "SECTION_ID",
			"SEF_MODE" => "N",
			"AJAX_MODE" => "N",
			"AJAX_OPTION_JUMP" => "N",
			"AJAX_OPTION_STYLE" => "Y",
			"AJAX_OPTION_HISTORY" => "N",
			"AJAX_OPTION_ADDITIONAL" => "",
			"CACHE_TYPE" => "A",
			"CACHE_TIME" => "36000000",
			"CACHE_GROUPS" => "Y",
			"SET_TITLE" => "Y",
			"SET_BROWSER_TITLE" => "Y",
			"BROWSER_TITLE" => "-",
			"SET_META_KEYWORDS" => "Y",
			"META_KEYWORDS" => "-",
			"SET_META_DESCRIPTION" => "Y",
			"META_DESCRIPTION" => "-",
			"SET_LAST_MODIFIED" => "N",
			"USE_MAIN_ELEMENT_SECTION" => "N",
			"ADD_SECTIONS_CHAIN" => "N",
			"CACHE_FILTER" => "N",
			"ACTION_VARIABLE" => "action",
			"PRODUCT_ID_VARIABLE" => "id",
			"PRICE_CODE" => array(0 => "cost",),
			"USE_PRICE_COUNT" => "N",
			"SHOW_PRICE_COUNT" => "1",
			"PRICE_VAT_INCLUDE" => "Y",
			"BASKET_URL" => "/personal/basket.php",
			"USE_PRODUCT_QUANTITY" => "N",
			"PRODUCT_QUANTITY_VARIABLE" => "",
			"ADD_PROPERTIES_TO_BASKET" => "Y",
			"PRODUCT_PROPS_VARIABLE" => "prop",
			"PARTIAL_PRODUCT_PROPERTIES" => "N",
			"PRODUCT_PROPERTIES" => array(),
			"PAGER_TEMPLATE" => ".default",
			"DISPLAY_TOP_PAGER" => "N",
			"DISPLAY_BOTTOM_PAGER" => "N",
			"PAGER_TITLE" => "",
			"PAGER_SHOW_ALWAYS" => "N",
			"PAGER_DESC_NUMBERING" => "N",
			"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
			"PAGER_SHOW_ALL" => "N",
			"PAGER_BASE_LINK_ENABLE" => "N",
			"SET_STATUS_404" => "N",
			"SHOW_404" => "N",
			"MESSAGE_404" => "",
			"ADD_PICT_PROP" => "-",
			"LABEL_PROP" => "-"
		),
		$component
	);?><?php

	$APPLICATION->IncludeComponent(
		"citrus.core:include",
		".default",
		[
			"AREA_FILE_SHOW" => "html",
			"HTML" => ob_get_clean(),
			"h" => ".h2",
			"TITLE" => Loc::getMessage("CITRUS_TEMPLATE_SIMILAR_OFFERS"),
			"PAGE_SECTION" => "Y",
			"PADDING" => "Y",
		],
		$component,
		['HIDE_ICONS' => 'Y']
	);
}

// блок ВАШ ПЕРСОНАЛЬНЫЙ МЕНЕДЖЕР
if ($arResult["CONTACT"])
{
	?><a name="personal_manager" id="personal_manager"></a><?php

	$APPLICATION->IncludeComponent(
		"citrus.core:include",
		".default",
		[
			"AREA_FILE_SHOW" => "component",
			"_COMPONENT" => "citrus:template",
			"_COMPONENT_TEMPLATE" => "staff-block",
			"ITEM" => $arResult["CONTACT"],
			"h" => ".h2",
			"TITLE" => Loc::getMessage("CITRUS_AREATLY_PERSONAL_MANAGER_BLOCK"),
			"DESCRIPTION" => Loc::getMessage("CITRUS_AREATLY_PERSONAL_MANAGER_BLOCK_DESC"),
			"PAGE_SECTION" => "Y",
			"PADDING" => "Y",
		],
		$component,
		['HIDE_ICONS' => 'Y']
	);
}