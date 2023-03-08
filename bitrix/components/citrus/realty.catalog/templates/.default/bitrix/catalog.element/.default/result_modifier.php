<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();
/** @var CBitrixComponentTemplate $this */
/** @var array $arParams */
/** @var array $arResult */

$arResult["CONTACT"] = false;
if ($contact = is_array($arResult["PROPERTIES"]["contact"]) ? $arResult["PROPERTIES"]["contact"]["VALUE"] : false)
	$arResult["CONTACT"] = \Citrus\Arealty\Helper::getContactInfo($contact);
