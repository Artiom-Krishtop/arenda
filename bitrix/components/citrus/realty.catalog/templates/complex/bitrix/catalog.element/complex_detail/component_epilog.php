<?

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Page\Asset;

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $templateData */
/** @var @global CMain $APPLICATION */

CUtil::InitJSCore(Array("realtyAddress", "photoSwipe", "swiper"));
\Citrus\Arealty\Helper::setLastSection($arResult["IBLOCK_SECTION_ID"], $arResult['DEAL_TYPE'] ? array('PROPERTY_deal_type' => $arResult['DEAL_TYPE']) : array());

Loc::loadMessages(__DIR__ . '/template.php');

$APPLICATION->ShowViewContent('complex_offers');

?>
<script>
BX.message({
	CITRUS_AREALTY_PDF_SEND_URL: '<?= CUtil::JSEscape($arParams["PDF_DETAIL_URL"]) ?>',
	CITRUS_AREALTY_PDF_SEND_PROMPT: '<?=CUtil::JSEscape(GetMessage("CITRUS_AREALTY_PDF_SEND_PROMPT"))?>',
	CITRUS_AREALTY_PDF_SEND_RESULT: '<?=CUtil::JSEscape(GetMessage("CITRUS_AREALTY_PDF_SEND_RESULT"))?>'
});
</script>
<hr>
<section class="row-ib print-hidden">
    <div class="col-dt-6">
	    <?if (is_array($arResult['CONTACT']))
	    {
            global $arrContactPersonFilter;
            $arrContactPersonFilter = array("ID" => $arResult['CONTACT']['ID']);
            $APPLICATION->IncludeComponent(
                "bitrix:news.list",
                "staff_slider",
                Array(
                    "AJAX_MODE" => "N",
                    "IBLOCK_TYPE" => "news",
	                "IBLOCK_ID" => \Citrus\Arealty\Helper::getIblock('staff'),
                    "NEWS_COUNT" => "1",
                    "FILTER_NAME" => "arrContactPersonFilter",
                    "FIELD_CODE" => array("CODE"),
                    "PROPERTY_CODE" => array("contacts"),
                    "CHECK_DATES" => "Y",
                    "SET_TITLE" => "N",
                    "SET_STATUS_404" => "N",
                    "INCLUDE_IBLOCK_INTO_CHAIN" => "N",
                    "ADD_SECTIONS_CHAIN" => "N",
                    "HIDE_LINK_WHEN_NO_DETAIL" => "Y",
                    "CACHE_TYPE" => "N",
                    "DISPLAY_TOP_PAGER" => "N",
                    "DISPLAY_BOTTOM_PAGER" => "N",
                    "PAGER_SHOW_ALWAYS" => "N",
	                "BLOCK_TITLE" => Loc::getMessage(isset($arResult['CONTACT']['ID']) ? "CITRUS_TEMPLATE_CONTACT_PERSON" : 'CITRUS_TEMPLATE_CONTACT_INFO'),
	                "CONTACT" => $arResult['CONTACT'],
                ),
                $component,
                array("HIDE_ICONS"=>"Y")
            );
	    }
	    else
        {
	        ?><?$APPLICATION->IncludeComponent(
                "citrus:realty.contacts",
                "",
                array(
                ),
	            $component,
	            array("HIDE_ICONS"=>"Y")
            );?><?
        }
	    ?>
    </div>
    <div class="col-dt-6" >
		<? $APPLICATION->IncludeComponent(
			"bitrix:main.include",
			"",
			Array(
				"AREA_FILE_SHOW" => "file", // page | sect - area to include
				"AREA_FILE_SUFFIX" => "inc", // suffix of file to seek
				"AREA_FILE_RECURSIVE" => "Y",
				"PATH" => SITE_DIR . "include/service_feedback_form.php",
				"EDIT_TEMPLATE" => "page_inc.php",
				"EDIT_MODE" => "php",
                "SERVICE_ID" => $arResult["ID"]
			),
			$component
		); ?>
    </div>
</section>
