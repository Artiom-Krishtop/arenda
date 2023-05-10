<?php

use Citrus\Arealty;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

/** @var CBitrixComponentTemplate $this */
/** @var array $arParams */
/** @var array $arResult */

$this->__component->setResultCacheKeys(array("CONTACT", "OFFERS", "OFFERS_FIELDS"));

$arResult["CONTACT"] = false;
if ($contact = is_array($arResult["PROPERTIES"]["contact"]) ? $arResult["PROPERTIES"]["contact"]["VALUE"] : false)
{
	$arResult["CONTACT"] = Arealty\Helper::getContactInfo($contact);
}

// если контакт не указан или не найден, будем использовать данные по умолчанию
if (!$arResult["CONTACT"])
{
	$arResult["CONTACT"] = \Citrus\Arealty\Helper::getContactInfo();
}

$arResult['ADDRESS'] = Arealty\Object\Address::createFromFields($arResult);

try
{
	$complexService = new Arealty\ComplexService(Arealty\Helper::getIblock("offers", SITE_ID));
	$complexService->setDefaultPropLinks($arParams['PROP_LINK']);
	$arResult['OFFERS_FIELDS'] = reset($complexService->getOfferFields(array($arResult['XML_ID'])));
}
catch (\Exception $e)
{
	ShowError($e->getMessage());
	$arResult['OFFERS_FIELDS'] = null;
}