<?php

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Page\Asset;
use \Citrus\Arealty\Helper;
use Citrus\Arealtypro\Manage\RightsFactory;
use Citrus\ArealtyPro\Manage\RightsProvider;

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

/** @var array $arParams ���������, ������/��������� �� ����������� ����������� ���� ����������. */
/** @var array $arResult ���������, ������/��������� �� ����������� ����������� ���� ������ ����������. */
/** @var string $componentPath ���� � ����� � ����������� �� DOCUMENT_ROOT (�������� /bitrix/components/bitrix/iblock.list). */
/** @var CBitrixComponent $component ������ �� $this. */
/** @var CBitrixComponent $this ������ �� ������� ��������� ���������, ����� ������������ ��� ������ ������. */
/** @var string $epilogFile ���� � ����� component_epilog.php ������������ DOCUMENT_ROOT */
/** @var string $templateName ��� ������� ���������� (��������: .d�fault) */
/** @var string $templateFile ���� � ����� ������� �� DOCUMENT_ROOT (����. /bitrix/components/bitrix/iblock.list/templates/.default/template.php) */
/** @var string $templateFolder ���� � ����� � �������� �� DOCUMENT_ROOT (����. /bitrix/components/bitrix/iblock.list/templates/.default) */
/** @var array $templateData �������� ��������, ����� ������� ����� �������� ������ �� template.php � ���� component_epilog.php, ������ ��� ������ ������������ � ����� �������� � component_epilog.php �� ������ ���� */
/** @var @global CMain $APPLICATION */
/** @var @global CUser $USER */

CUtil::InitJSCore(Array("realtyAddress", "photoSwipe", "swiper"));
Helper::setLastSection($arResult["IBLOCK_SECTION_ID"], $arResult['DEAL_TYPE'] ? array('PROPERTY_deal_type' => $arResult['DEAL_TYPE']) : array());

Asset::getInstance()->addCss($templateFolder . "/pannellum.css");
Asset::getInstance()->addJs($templateFolder . "/pannellum.js");

Loc::loadMessages(__DIR__ . '/template.php');

$APPLICATION->SetPageProperty('SHOW_TITLE', 'N');

$jsMessages = [
	'CITRUS_AREALTY_PDF_SEND_URL' => $arParams["PDF_DETAIL_URL"],
	'CITRUS_AREALTY_PDF_SEND_PROMPT' => GetMessage("CITRUS_AREALTY_PDF_SEND_PROMPT"),
	'CITRUS_AREALTY_PDF_SEND_RESULT' => GetMessage("CITRUS_AREALTY_PDF_SEND_RESULT"),
];
Asset::getInstance()
    ->addString('<script>BX.message(' . CUtil::PhpToJSObject($jsMessages) . ');</script>');

if (empty($arParams['IS_JK']) || $arParams['IS_JK'] != 'Y')
{
	if ($USER->IsAuthorized() && \Bitrix\Main\Loader::includeModule('citrus.arealtypro') && class_exists(RightsFactory::class))
	{
		$rights = RightsFactory::getInstance($arResult['IBLOCK_ID']);
		if ($rights->canDoOperation(RightsProvider::OP_ELEMENT_EDIT, $arResult['ID']))
		{
			?><!--<a href="&lt;?=SITE_DIR?&gt;kabinet/&lt;?=$arResult[">/" data-bx-app-ex-href="<?/*=SITE_DIR*/?>
kabinet/<?/*=$arResult["ID"]*/?>
/" class="image-actions__link print-hidden" id="object-edit-link" style="display: none;"&gt; <span class="image-actions__link-icon"><i class="icon-edit"></i></span> <span class="image-actions__link-text"><?/*=Loc::getMessage("CITRUS_REALTY_CHANGE_OBJECT")*/?></span> </a>-->
            <a href="<?=SITE_DIR?>kabinet/<?=$arResult["ID"]?>/" data-bx-app-ex-href="<?=SITE_DIR?>kabinet/<?=$arResult["ID"]?>/"
                 class="image-actions__link print-hidden" id="object-edit-link" style="display: none;"> <span class="image-actions__link-icon"><i class="icon-edit"></i></span> <span class="image-actions__link-text"><?=Loc::getMessage("CITRUS_REALTY_CHANGE_OBJECT")?></span>
            </a>
        <?
		}
	}
}
?>
<?php /*if (empty($arResult['COMPLEX'])) { */?><!--
	<?/*$APPLICATION->IncludeComponent(
	"citrus.core:include",
	".default",
	Array(
		"AREA_FILE_SHOW" => "component",
		"BG_COLOR" => "N",
		"BTN_TITLE" => Loc::getMessage('CITRUS_AREALTY_BLOCK_COMPLEX_BTN_TITLE'),
		"CACHE_TIME" => "36000000",
		"CACHE_TYPE" => "A",
		"CLASS" => "desc_complex",
		"COMPONENT_TEMPLATE" => ".default",
		"ELEMENT_XML_ID" => $arResult['COMPLEX'],
		"IBLOCK_ID" => \CModule::IncludeModule("citrus.arealty") ? Helper::getIblock("complexes") : "",
		"IBLOCK_TYPE" => "realty",
		"PADDING" => "Y",
		"PAGE_SECTION" => "Y",
		"TITLE" => Loc::getMessage('CITRUS_AREALTY_BLOCK_COMPLEX_TITLE'),
		"WIDGET_REL" => "",
		"_COMPONENT" => "citrus.arealty:contentblock",
		"_COMPONENT_TEMPLATE" => "contentwithimageandtitle",
		"h" => ".h1"
	)
);*/?>
--><?php /*} */?>