<?php

/** @global \CMain $APPLICATION */

use Bitrix\Main\Application;
use Bitrix\Main\Loader;
use Citrus\Core\Components\ParamsSerializer;

define('STOP_STATISTICS', true);

require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');

$request = Application::getInstance()->getContext()->getRequest();

if (!Loader::includeModule('iblock') || !Loader::includeModule('citrus.arealty'))
{
	return;
}

$arParams = array(
	'AJAX' => true,
	'PAGE' => (int) $request->get('PAGE')
);

$paramsSerializer = new ParamsSerializer();
$ajaxParams = $paramsSerializer->unserialize($request->get('arParams'));
if (!$ajaxParams)
{
	return;
}

$APPLICATION->IncludeComponent(
	'citrus.custom:bigdata.map',
	htmlspecialcharsEx($ajaxParams['COMPONENT_TEMPLATE']),
	array_merge($arParams, $ajaxParams),
	false,
	array('HIDE_ICONS' => 'Y')
);