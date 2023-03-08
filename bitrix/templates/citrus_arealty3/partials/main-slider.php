<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Citrus\Arealty\Helper;

?>
<div class="main-slider" data-settings="BLOCKS" data-settings-rel="slider"
	<?=Citrus\Arealty\Template\hiddenCss('slider')?>>
	<? $APPLICATION->IncludeComponent(
		"bitrix:news.list",
		"main-slider",
		array(
			"DISPLAY_DATE" => "Y",
			"DISPLAY_NAME" => "Y",
			"DISPLAY_PICTURE" => "N",
			"DISPLAY_PREVIEW_TEXT" => "Y",
			"RESIZE_IMAGE_WIDTH" => "150",
			"RESIZE_IMAGE_HEIGHT" => "150",
			"AJAX_MODE" => "N",
			"IBLOCK_TYPE" => "info",
			"IBLOCK_ID" => Helper::getIblock("mp_slider"),
			"NEWS_COUNT" => "6",
			"SORT_BY1" => "ACTIVE_FROM",
			"SORT_ORDER1" => "DESC",
			"SORT_BY2" => "SORT",
			"SORT_ORDER2" => "ASC",
			"FILTER_NAME" => "",
			"FIELD_CODE" => array(
				0 => "PREVIEW_TEXT",
				1 => "PREVIEW_PICTURE",
				2 => "",
			),
			"PROPERTY_CODE" => array(
				0 => "",
				1 => "btn_link",
				2 => "btn_text",
				3 => "btn_link_attr",
			),
			"CHECK_DATES" => "Y",
			"DETAIL_URL" => "",
			"PREVIEW_TRUNCATE_LEN" => "",
			"ACTIVE_DATE_FORMAT" => "j F Y",
			"SET_TITLE" => "N",
			"SET_STATUS_404" => "N",
			"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
			"ADD_SECTIONS_CHAIN" => "N",
			"HIDE_LINK_WHEN_NO_DETAIL" => "N",
			"PARENT_SECTION" => "",
			"PARENT_SECTION_CODE" => "",
			"CACHE_TYPE" => "A",
			"CACHE_TIME" => "36000000",
			"CACHE_FILTER" => "N",
			"CACHE_GROUPS" => "Y",
			"DISPLAY_TOP_PAGER" => "N",
			"DISPLAY_BOTTOM_PAGER" => "N",
			"PAGER_TITLE" => '',
			"PAGER_SHOW_ALWAYS" => "N",
			"PAGER_TEMPLATE" => "",
			"PAGER_DESC_NUMBERING" => "N",
			"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
			"PAGER_SHOW_ALL" => "N",
			"AJAX_OPTION_JUMP" => "N",
			"AJAX_OPTION_STYLE" => "Y",
			"AJAX_OPTION_HISTORY" => "N",
			"COMPONENT_TEMPLATE" => "mp_top_slider",
			"AJAX_OPTION_ADDITIONAL" => "",
			"SET_BROWSER_TITLE" => "Y",
			"SET_META_KEYWORDS" => "Y",
			"SET_META_DESCRIPTION" => "Y",
			"SET_LAST_MODIFIED" => "N",
			"INCLUDE_SUBSECTIONS" => "Y",
			"GROUP_BY_MONTH" => "N",
			"PAGER_BASE_LINK_ENABLE" => "N",
			"SHOW_404" => "N",
			"MESSAGE_404" => ""
		),
		false
	); ?>
</div>