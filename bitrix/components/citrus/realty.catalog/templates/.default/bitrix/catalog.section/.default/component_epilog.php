<?

CUtil::InitJSCore(Array("realtyAddress"));

if (!in_array($arResult["UF_TYPE_XML_ID"], array('only_text', 'cards')))
	$APPLICATION->AddViewContent('sort-area', \Citrus\Arealty\SortOrder::renderControl($arResult["SORT_FIELDS"]));
