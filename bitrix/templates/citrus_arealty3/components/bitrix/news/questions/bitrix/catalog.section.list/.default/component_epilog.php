<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if (array_key_exists('REDIRECT_TO', $arResult) && $arResult["REDIRECT_TO"])
	LocalRedirect($arResult["REDIRECT_TO"]);

CJSCore::Init(array("jquery"));
