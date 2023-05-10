<?php

CJSCore::Init(['equalHeight']);

$arParams['SET_PAGE_SUBHEADER'] = !empty($arParams['SET_PAGE_SUBHEADER']) && $arParams['SET_PAGE_SUBHEADER'] == 'Y';

if ($arParams['SET_PAGE_SUBHEADER'] && $sectionPath = \Citrus\Core\array_get($arResult, 'SECTION.PATH'))
{
	$APPLICATION->SetPageProperty('PAGE_SUBHEADER', end($sectionPath)["DESCRIPTION"]);
}