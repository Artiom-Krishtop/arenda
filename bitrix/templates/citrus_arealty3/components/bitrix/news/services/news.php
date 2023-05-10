<?php

use Bitrix\Main\Localization\Loc;

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$this->setFrameMode(true);

/** @var array $arParams */
/** @var array $arResult */
/** @var string $templateFolder */

$iblockId = $arParams['IBLOCK_ID'];
if (!is_numeric($iblockId))
{
	$iblockId = \Citrus\Arealty\Helper::getIblock($iblockId);
}

?>

<?$APPLICATION->IncludeComponent(
	"citrus.arealty:catalog.section.list",
	"services",
	array(
		"IBLOCK_TYPE" => $arParams['IBLOCK_TYPE'],
		"IBLOCK_ID" => $iblockId,
		"SECTION_ID" => "",
		"SECTION_CODE" => "",
		"COUNT_ELEMENTS" => "N",
		"TOP_DEPTH" => "1",
		"SECTION_FIELDS" => array(
			0 => "PICTURE",
			1 => "DETAIL_PICTURE",
		),
		"SECTION_USER_FIELDS" => array(
			0 => "",
			1 => "",
		),
		"SECTION_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["section"],
		"CACHE_TYPE" => $arParams["CACHE_TYPE"],
		"CACHE_TIME" => $arParams["CACHE_TIME"],
		"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
		"ADD_SECTIONS_CHAIN" => "N",
		"LINK_SHOW_ALL" => 'Y',
		"SHOW_IBLOCK_DESRIPTION" => "Y",
	),
	false
); ?>

<?php
ob_start();
?>
<a href="<?= SITE_DIR ?>kompanija/otzyvy/" class="btn btn-secondary btn-stretch"><?=
	Loc::getMessage('CITRUS_AREALTY3_BLOCK_REVIEWS_ALL') ?></a>
<?php
$reviewsAllLink = ob_get_clean();
?>

<? $APPLICATION->IncludeComponent(
	"citrus.core:include",
	".default",
	array(
		"AREA_FILE_SHOW" => "sect",
		"AREA_FILE_SUFFIX" => "block_reviews",
		"AREA_FILE_RECURSIVE" => "Y",
		"TITLE" => Loc::getMessage("CITRUS_AREALTY3_BLOCK_REVIEWS_TITLE"),
		"DESCRIPTION" => Loc::getMessage("CITRUS_AREALTY3_BLOCK_REVIEWS_DESC"),
		"h" => "h1",
		"PAGE_SECTION" => "Y",
		"PADDING" => "Y",
		"BG_COLOR" => "N",
		"BOTTOM_SUBSTRATE" => "N",
        "LINK_SHOW_ALL" => "Y",
		"FOOTER_CONTENT" => $reviewsAllLink,
	),
	false
); ?>

<?$this->SetViewTarget('footer-before');?>

<? $APPLICATION->IncludeComponent(
	"citrus.core:include",
	".default",
	array(
		"AREA_FILE_SHOW" => "sect",
		"AREA_FILE_SUFFIX" => "block_partners",
		"AREA_FILE_RECURSIVE" => "Y",
		"TITLE" => Loc::getMessage("CITRUS_AREALTY3_BLOCK_PARTNERS_TITLE"),
		"DESCRIPTION" => Loc::getMessage("CITRUS_AREALTY3_BLOCK_PARTNERS_DESC"),
		"h" => "h1",
		"PAGE_SECTION" => "Y",
		"COMPONENT_TEMPLATE" => ".default",
		"PADDING" => "Y",
		"BG_COLOR" => "GRAY",
		"BOTTOM_SUBSTRATE" => "N",
	),
	false
); ?>
