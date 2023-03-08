<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use \Citrus\Arealty\SortOrder,
    Bitrix\Main\Localization\Loc;

$this->setFrameMode(true);

Loc::loadMessages(__DIR__."/section.php");?>

<?
// nook добавить возможность поделиться ссылкой со списком избранного

$APPLICATION->SetTitle(GetMessage("CITRUS_REALTY_FAVOURITES"));

global $arrFavouritesFilter;
$arrFavouritesFilter = array(
	"ID" => array_keys(\Citrus\Arealty\Favourites::getList()),
);
?>

<?if(\Citrus\Arealty\Favourites::getCount() > 0):?>
    <?$citrusSort = $APPLICATION->IncludeComponent(
        "citrus:sort",
        ".default",
        array(
            "COMPONENT_TEMPLATE" => ".default",
            "IBLOCK_TYPE" => "realty",
            "IBLOCK_ID" => \Citrus\Arealty\Helper::getIblock('offers'),
            "SORT_FIELDS" => array(
	            SortOrder::getOfferDateField() => Loc::getMessage("SORT_FIELD_DATE_CREATE"),
                "PROPERTY_cost" => Loc::getMessage("SORT_FIELD_COST"),
	            "PROPERTY_common_area" => Loc::getMessage("SORT_FIELD_COMMON_AREA")
            ),
            "DEFAULT_SORT_ORDER" => "DESC",
            "VIEW_LIST" => array(
                0 => "CARDS",
                1 => "LIST",
                2 => "",
            ),
            "VIEW_DEFAULT" => "CARDS",

        ),
		$component
    );?>
<?endif;?>

<?
echo GetMessage("CITRUS_REALTY_FAV_NOTE");
?>

<button type="button" class="btn btn-secondary js-citrus-pdf-send"
		data-ids="<?= implode(",", $arrFavouritesFilter["ID"]) ?>">
	<span class="btn-label"><?=GetMessage("CITRUS_REALTY_PDF_SEND")?></span>
</button>
<script>
BX.message({
	CITRUS_AREALTY_PDF_SEND_URL: '<?= CUtil::JSEscape(
		$arParams["SEF_FOLDER"] . $arParams["SEF_URL_TEMPLATES"]["pdf"]) ?>',
	CITRUS_AREALTY_PDF_SEND_PROMPT: '<?=CUtil::JSEscape(GetMessage("CITRUS_AREALTY_PDF_SEND_PROMPT"))?>',
	CITRUS_AREALTY_PDF_SEND_RESULT: '<?=CUtil::JSEscape(GetMessage("CITRUS_AREALTY_PDF_SEND_RESULT"))?>'
});
</script>
<script>
BX.ready(function () {
  BX.bind(document.querySelector(".js-citrus-pdf-send"), "click", function () {
    var id = this.getAttribute("data-ids");
    if (id == "") {
      console.error("ID not defined");
      return;
	}
	var id = id.split(","), paramId = [];
	for (var i = 0, l = id.length; i < l; i++) {
		paramId.push("id[]=" + id[i]);
	}
    var email = prompt(BX.message("CITRUS_AREALTY_PDF_SEND_PROMPT"), "");
    if (email == "" || email == null) {
      console.error("E-email not defined");
      return;
    }
    BX.ajax({
      url: BX.message("CITRUS_AREALTY_PDF_SEND_URL")
        + "?" + paramId.join("&") + "&to=" + email,
      method: "GET",
      dataType: "json",
      cache: false,
      onsuccess: function (data) {
        if (data.result > 0) {
          alert(BX.message("CITRUS_AREALTY_PDF_SEND_RESULT"));
        } else if (data.error) {
          alert(data.error);
        }
      },
    });
  });
});
</script>
<?php

if (\Citrus\Arealty\Favourites::getCount() <= 0)
{
	echo GetMessage("CITRUS_REALTY_FAV_EMPTY");
	return;
}
?>

<script>
BX.message({
	CITRUS_AREALTY_PDF_SEND_SITE_DIR: '<?= CUtil::JSEscape(
		$arParams["SEF_FOLDER"] . $arParams["SEF_URL_TEMPLATES"]["print"]
	) ?>',
});
</script>
<?$APPLICATION->IncludeComponent(
	"citrus:realty.favourites",
	"block",
	array(
        "PATH" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["favourites"],
	),
	$component,
	array("HIDE_ICONS" => "Y")
);?>
<div class="favorites_page">
<?$intSectionID = $APPLICATION->IncludeComponent(
	"bitrix:catalog.section",
	"catalog_" . strtolower($citrusSort["VIEW"]),
	array(
		"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
		"IBLOCK_ID" => $arParams["IBLOCK_ID"],
		"ELEMENT_SORT_FIELD" => $citrusSort["SORT"]["CODE"],
		"ELEMENT_SORT_ORDER" => $citrusSort["SORT"]["ORDER"],
		"ELEMENT_SORT_FIELD2" => $arParams["ELEMENT_SORT_FIELD2"],
		"ELEMENT_SORT_ORDER2" => $arParams["ELEMENT_SORT_ORDER2"],
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
		"FILTER_NAME" => "arrFavouritesFilter",
		"CACHE_TYPE" => $arParams["CACHE_TYPE"],
		"CACHE_TIME" => $arParams["CACHE_TIME"],
		"CACHE_FILTER" => $arParams["CACHE_FILTER"],
		"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
		"SET_TITLE" => "N",
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

		"SECTION_ID" => 0,
		"SECTION_CODE" => '',
		"INCLUDE_SUBSECTIONS" => "Y",
		"SHOW_ALL_WO_SECTION" => "Y",
		"SECTION_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["section"],
		"DETAIL_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["element"],
		'CONVERT_CURRENCY' => $arParams['CONVERT_CURRENCY'],
		'CURRENCY_ID' => $arParams['CURRENCY_ID'],
		'HIDE_NOT_AVAILABLE' => $arParams["HIDE_NOT_AVAILABLE"],

		'LABEL_PROP' => $arParams['LABEL_PROP'],
		'ADD_PICT_PROP' => $arParams['ADD_PICT_PROP'],
		'PRODUCT_DISPLAY_MODE' => $arParams['PRODUCT_DISPLAY_MODE'],

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

		"SHOW_MAP" => "Y",
		"SECTION_USER_FIELDS" => array("UF_TYPE"),
		"CURRENCY" => \Citrus\Arealty\Helper::getSelectedCurrency(),
	),
	$component
);?>

<div>
	<button type="button" class="btn btn-secondary js-citrus-fav-print">
		<span class="btn-icon fa fa-fw fa-print"></span>
		<span class="btn-label"><?=GetMessage("CITRUS_REALTY_FAV_PRINT")?></span>
	</button>
</div>

</div>
