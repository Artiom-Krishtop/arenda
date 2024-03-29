<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("SHOW_TITLE", "Y");
$APPLICATION->SetPageProperty("description", "Сайт агентства недвижимости. Предложения");
$APPLICATION->SetTitle("Предложения");

?><?$APPLICATION->IncludeComponent(
	"citrus:realty.catalog", 
	"custom", 
	array(
		"ACTION_VARIABLE" => "action",
		"ADD_ELEMENT_CHAIN" => "Y",
		"ADD_PICT_PROP" => "-",
		"ADD_PROPERTIES_TO_BASKET" => "Y",
		"ADD_SECTIONS_CHAIN" => "Y",
		"AJAX_MODE" => "N",
		"AJAX_OPTION_ADDITIONAL" => "",
		"AJAX_OPTION_HISTORY" => "Y",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "Y",
		"BASKET_URL" => "/personal/basket.php",
		"CACHE_FILTER" => "Y",
		"CACHE_GROUPS" => "Y",
		"CACHE_TIME" => "36000000",
		"CACHE_TYPE" => "A",
		"CURRENCY" => \Citrus\Arealty\Helper::getSelectedCurrency(),
		"DETAIL_ADD_DETAIL_TO_SLIDER" => "N",
		"DETAIL_BRAND_USE" => "N",
		"DETAIL_BROWSER_TITLE" => "-",
		"DETAIL_DETAIL_PICTURE_MODE" => "IMG",
		"DETAIL_DISPLAY_NAME" => "Y",
		"DETAIL_DISPLAY_NUMBER" => "N",
		"DETAIL_DISPLAY_PREVIEW_TEXT_MODE" => "E",
		"DETAIL_META_DESCRIPTION" => "-",
		"DETAIL_META_KEYWORDS" => "-",
		"DETAIL_PROPERTY_CODE" => array(
			0 => "CITY",
			1 => "NEW_BUILDING",
			2 => "COUNTRY",
			3 => "CITY_AREA",
			4 => "ADDRESS",
			5 => "NEW_ROOMS_AREA",
			6 => "NEW_COMMERCIAL_FEATURES",
			7 => "NEW_COMMERCIAL_TYPE",
			9 => "NEW_FLOOR",
			10 => "NEW_ROOMS_TYPE",
			11 => "DESCRIPTION",
			12 => "contact",
			13 => "deal_type",
			14 => "category",
			15 => "commercial_type",
			16 => "commercial_building_type",
			17 => "deal_status_new",
			18 => "deal_status",
			19 => "deal_status_commercial",
			20 => "rooms",
			21 => "rooms_offered",
			22 => "metro_stations",
			23 => "district",
			24 => "price_for_meter",
			25 => "cost_curr",
			26 => "cost",
			27 => "cost_curr_value",
			28 => "cost_unit",
			29 => "cost_period",
			30 => "agent_fee",
			31 => "prepayment",
			32 => "commercial_rent_conditions",
			33 => "security_payment",
			34 => "apartment",
			35 => "lot_number",
			36 => "cadastral_number",
			37 => "garage_type",
			38 => "taxation_form",
			39 => "common_area",
			40 => "deal_conditions",
			41 => "floors",
			42 => "living_area",
			43 => "rooms_area",
			44 => "kitchen_area",
			45 => "land_area",
			46 => "balcony",
			47 => "share_amount",
			48 => "wc",
			49 => "ownership_type",
			50 => "window_view",
			51 => "renovation",
			52 => "type_of_land",
			53 => "garage_name",
			54 => "condition",
			55 => "walls_type",
			56 => "building_type",
			57 => "building_series",
			58 => "building_section",
			59 => "built_year",
			60 => "ceiling_height",
			61 => "LAND_LORD",
			62 => "entrance_type",
			63 => "BUILDING",
			64 => "phone_lines",
			65 => "rooms_html",
			66 => "RENTAL_COMPANY",
			67 => "garage_features",
			68 => "distance_to_town",
			69 => "garage_building_type",
			70 => "window_type",
			71 => "parking_type",
			72 => "residential_features",
			73 => "building_features",
			74 => "house_features",
			75 => "floor_covering",
			76 => "commercial_purpose",
			77 => "purpose_warehouse",
			78 => "office_class",
			79 => "commercial_building_features",
			80 => "commercial_features",
			81 => "parking_places",
			82 => "parking_place_price",
			83 => "parking_guest_places",
			84 => "geodata",
			85 => "listing_fee",
			86 => "quick_sale",
			87 => "publishterms_sevices",
			88 => "publishterms_excludedsevices",
			89 => "publishterms_ignoreservicepackages",
			91 => "floor",
			92 => "rooms_type",
			93 => "complex",
			94 => "documents",
			95 => "house_type",
			96 => "lift",
			97 => "",
		),
		"DETAIL_SET_CANONICAL_URL" => "Y",
		"DETAIL_SHOW_SIMILAR_OFFERS" => "Y",
		"DETAIL_USE_COMMENTS" => "N",
		"DETAIL_USE_VOTE_RATING" => "N",
		"DISPLAY_BOTTOM_PAGER" => "Y",
		"DISPLAY_TOP_PAGER" => "N",
		"ELEMENT_SORT_FIELD" => "sort",
		"ELEMENT_SORT_FIELD2" => "shows",
		"ELEMENT_SORT_ORDER" => "asc",
		"ELEMENT_SORT_ORDER2" => "desc",
		"EMPTY_LIST_MESSAGE" => "",
		"FILE_404" => "/404.php",
		"FILTER_FIELD_CODE" => array(
			0 => "",
			1 => "",
		),
		"FILTER_NAME" => "arrFilter",
		"FILTER_PRICE_CODE" => array(
			0 => "cost",
		),
		"FILTER_PROPERTY_CODE" => array(
			0 => "rooms",
			1 => "district",
			2 => "common_area",
			3 => "balcony",
			4 => "wc",
			5 => "house_type",
			6 => "complex",
			7 => "commercial_features",
			8 => "rooms_area",
		),
		"FILTER_VIEW_MODE" => "VERTICAL",
		"IBLOCK_ID" => "13",
		"IBLOCK_TYPE" => "realty",
		"INCLUDE_SUBSECTIONS" => "Y",
		"LABEL_PROP" => "-",
		"LINE_ELEMENT_COUNT" => "1",
		"LINK_ELEMENTS_URL" => "link.php?PARENT_ELEMENT_ID=#ELEMENT_ID#",
		"LINK_IBLOCK_ID" => "3",
		"LINK_IBLOCK_TYPE" => "company",
		"LINK_PROPERTY_SID" => "",
		"LIST_BROWSER_TITLE" => "-",
		"LIST_META_DESCRIPTION" => "-",
		"LIST_META_KEYWORDS" => "-",
		"LIST_PROPERTY_CODE" => array(
			0 => "district",
			1 => "cost",
			2 => "rooms_area",
			3 => "commercial_features",
			4 => "",
		),
		"MESS_BTN_ADD_TO_BASKET" => "В корзину",
		"MESS_BTN_BUY" => "Купить",
		"MESS_BTN_COMPARE" => "Сравнение",
		"MESS_BTN_DETAIL" => "Подробнее",
		"MESS_NOT_AVAILABLE" => "Нет в наличии",
		"PAGER_DESC_NUMBERING" => "N",
		"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
		"PAGER_SHOW_ALL" => "N",
		"PAGER_SHOW_ALWAYS" => "N",
		"PAGER_TEMPLATE" => "",
		"PAGER_TITLE" => "Предложения",
		"PAGE_ELEMENT_COUNT" => "15",
		"PARTIAL_PRODUCT_PROPERTIES" => "N",
		"PRICE_CODE" => array(
			0 => "cost",
		),
		"PRICE_VAT_INCLUDE" => "Y",
		"PRICE_VAT_SHOW_VALUE" => "N",
		"PRODUCT_ID_VARIABLE" => "id",
		"PRODUCT_PROPERTIES" => "",
		"PRODUCT_PROPS_VARIABLE" => "prop",
		"PRODUCT_QUANTITY_VARIABLE" => "quantity",
		"PROP_LINK" => array(
			0 => "commercial_type",
			1 => "rooms_area",
			2 => "commercial_features",
			3 => "",
		),
		"REDIRECT_TO_SECTION_404" => "N",
		"SECTIONS_HIDE_SECTION_NAME" => "N",
		"SECTIONS_SHOW_PARENT_NAME" => "N",
		"SECTIONS_VIEW_MODE" => "LIST",
		"SECTION_COUNT_ELEMENTS" => "Y",
		"SECTION_ID_VARIABLE" => "SECTION_ID",
		"SECTION_TOP_DEPTH" => "2",
		"SEF_FOLDER" => "/predlozhenija/",
		"SEF_MODE" => "Y",
		"SET_STATUS_404" => "Y",
		"SET_TITLE" => "N",
		"SHARE_SERVICES" => array(
			0 => "delicious",
			1 => "vk",
			2 => "twitter",
			3 => "mailru",
			4 => "facebook",
			5 => "lj",
		),
		"SHOW_404" => "Y",
		"SHOW_PRICE_COUNT" => "1",
		"SHOW_TOP_ELEMENTS" => "N",
		"TEMPLATE_THEME" => CModule::IncludeModule("citrus.arealty")?\Citrus\Arealty\Helper::getTheme():"",
		"TOP_ELEMENT_COUNT" => "9",
		"TOP_ELEMENT_SORT_FIELD" => "shows",
		"TOP_ELEMENT_SORT_FIELD2" => "id",
		"TOP_ELEMENT_SORT_ORDER" => "desc",
		"TOP_ELEMENT_SORT_ORDER2" => "asc",
		"TOP_LINE_ELEMENT_COUNT" => "2",
		"TOP_PROPERTY_CODE" => array(
			0 => "rooms",
			1 => "",
		),
		"TOP_ROTATE_TIMER" => "30",
		"TOP_VIEW_MODE" => "BANNER",
		"USE_COMPARE" => "N",
		"USE_ELEMENT_COUNTER" => "Y",
		"USE_FILTER" => "Y",
		"USE_PRICE_COUNT" => "N",
		"USE_PRODUCT_QUANTITY" => "N",
		"USE_REVIEW" => "N",
		"USE_STORE" => "N",
		"COMPONENT_TEMPLATE" => ".default",
		"SEF_URL_TEMPLATES" => array(
			"sections" => "",
			"section" => "#SECTION_CODE_PATH#/",
			"element" => "#SECTION_CODE_PATH#/#ELEMENT_CODE#/",
			"favourites" => "izbrannoe/",
			"print" => "print/",
			"pdf" => "pdf/",
			"smart_filter" => "",
		)
	),
	false
);?><br><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>