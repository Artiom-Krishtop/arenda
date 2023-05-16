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
Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . "/js/popcorn/popcorn.min.js");
Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . "/js/popcorn/popcorn.capture.js");

Loc::loadMessages(__DIR__ . '/template.php');

$APPLICATION->SetPageProperty('SHOW_TITLE', 'N');

$jsMessages = [
	'CITRUS_AREALTY_PDF_SEND_URL' => $arParams["PDF_DETAIL_URL"],
	'CITRUS_AREALTY_PDF_SEND_PROMPT' => GetMessage("CITRUS_AREALTY_PDF_SEND_PROMPT"),
	'CITRUS_AREALTY_PDF_SEND_RESULT' => GetMessage("CITRUS_AREALTY_PDF_SEND_RESULT"),
	'CITRUS_AREALTY_PDF_SEND_SITE_DIR' => CUtil::JSEscape($arParams['PRINT_DETAIL_URL']),
];

Asset::getInstance()->addString('<script>BX.message(' . CUtil::PhpToJSObject($jsMessages) . ');</script>');

if (empty($arParams['IS_JK']) || $arParams['IS_JK'] != 'Y')
{
	if ($USER->IsAuthorized() && \Bitrix\Main\Loader::includeModule('citrus.arealtypro') && class_exists(RightsFactory::class))
	{
		$rights = RightsFactory::getInstance($arResult['IBLOCK_ID']);
		if ($rights->canDoOperation(RightsProvider::OP_ELEMENT_EDIT, $arResult['ID'])){?>
            <a href="<?=SITE_DIR?>kabinet/<?=$arResult["ID"]?>/" data-bx-app-ex-href="<?=SITE_DIR?>kabinet/<?=$arResult["ID"]?>/"
                 class="image-actions__link print-hidden" id="object-edit-link" style="display: none;"> <span class="image-actions__link-icon"><i class="icon-edit"></i></span> <span class="image-actions__link-text"><?=Loc::getMessage("CITRUS_REALTY_CHANGE_OBJECT")?></span>
            </a>
        <?
		}
	}
}
?>