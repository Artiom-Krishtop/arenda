<?php
use \Bitrix\Main\Page\Asset;
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

CJSCore::Init( array("citrus_validator"));
if ($arParams["AJAX_MODE"] == "Y") CJSCore::Init(array( "fx", "ajax" ));

//add scheme
Asset::getInstance()->addCss($templateFolder.'/themes/'.strtolower($arParams["FORM_STYLE"]).'.css');


if('Y' === $arParams['USE_GOOGLE_RECAPTCHA'])
	Asset::getInstance()->addJs('//www.google.com/recaptcha/api.js?hl=' . LANGUAGE_ID);
