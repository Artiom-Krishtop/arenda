<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

$this->setFrameMode(true);

?><?$APPLICATION->IncludeComponent(
	"bitrix:news.detail",
	"callout",
	array(
		"IBLOCK_TYPE" => $arResult['IBLOCK_TYPE'],
		"IBLOCK_ID" => $arResult['IBLOCK_ID'],
		"ELEMENT_CODE" => is_numeric($arParams['ID']) ? '' : $arParams['ID'],
		"ELEMENT_ID" => is_numeric($arParams['ID']) ? $arParams['ID'] : '',
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "36000000",
		"CACHE_FILTER" => "N",
		"CACHE_GROUPS" => "N",
		"SET_STATUS_404" => "N",
		"SET_TITLE" => "N",
		"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
		"ADD_SECTIONS_CHAIN" => "N",
		"HIDE_LINK_WHEN_NO_DETAIL" => "N",
		"CITRUS_THEME" => \Citrus\Arealty\Helper::getTheme(),
		"PROPERTY_CODE" => array(
			'btn_text',
			'btn_link',
			'btn_form',
		),
	),
	$component,
	array('HIDE_ICONS' => 'Y')
);?>
