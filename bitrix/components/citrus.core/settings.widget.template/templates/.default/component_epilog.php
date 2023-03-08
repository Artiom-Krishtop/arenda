<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

$assets = \Bitrix\Main\Page\Asset::getInstance();

CJSCore::Init(['vue', 'openSans']);

// add dev mode in .settings.php: 'citrus_dev' => ['value' => 'widget']
if (Bitrix\Main\Config\Configuration::getValue('citrus_dev') === 'widget')
{
	$assets->addJs('http://localhost:8082/dist/build.js', true);
	$assets->addCss('http://localhost:8082/dist/style.css', true);
}
else
{
	$assets->addJs($templateFolder.'/vueComponent/dist/build.js');
	$assets->addCss($templateFolder.'/vueComponent/dist/style.css');
}
