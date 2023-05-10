<?php

use Bitrix\Main\Context;
use Bitrix\Main\Loader;
use Bitrix\Main\Web\Json;

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

Loader::includeModule('citrus.forms');
CJSCore::Init(array_merge(['citrus_form'], $templateData['cjscore'] ?: []));

$context = Context::getCurrent();

if($context->getRequest()->isAjaxRequest() && isset($_REQUEST['ASSETS']))
{
	$APPLICATION->RestartBuffer();
	while (ob_end_clean()) {}

	$context->getResponse()->addHeader('Content-Type', 'application/json; charset=' . SITE_CHARSET);

	$arScripts = array_map([CUtil::class, 'GetAdditionalFileURL'], $APPLICATION->arHeadScripts);
	$arCss = array_map([CUtil::class, 'GetAdditionalFileURL'], $APPLICATION->GetCSSArray());

	echo Json::encode(['css' => $arCss,'js' => $arScripts]);

	CMain::FinalActions();
	die();
}
