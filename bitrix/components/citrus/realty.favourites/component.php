<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if (!Loader::includeModule("citrus.arealty"))
	return;

$arParams['PATH'] = strlen(trim($arParams["PATH"])) ? $arParams["PATH"] : null;

/**
 * Ќа старых установках парметр компонента не заполнен: попытаемс€ определить или выведем сообщение дл€ администратора
 */
if (is_null($arParams['PATH']))
{
	CAdminNotify::Add(array(
		"MESSAGE" => Loc::getMessage('CITRUS_AREALTY_FAV_WARNING', array('#PAGE_PATH#' => $APPLICATION->GetCurPage())),
		"TAG" => "CITRUS_AREALTY_FAV_WARNING",
		"MODULE_ID" => "CITRUS_AREALTY",
		"ENABLE_CLOSE" => "Y",
	));
	if ($APPLICATION->GetPublicShowMode() != 'view')
	{
		ShowError(strip_tags(Loc::getMessage('CITRUS_AREALTY_FAV_WARNING', array('#PAGE_PATH#' => $APPLICATION->GetCurPage()))));
	}
}

$arResult = array(
	'COUNT' => \Citrus\Arealty\Favourites::getCount(),
	'LIST' => \Citrus\Arealty\Favourites::getList(),
);

$this->IncludeComponentTemplate();

