<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

$appPath = SITE_TEMPLATE_PATH . '/application/';
$jsMessagesFilePath = SITE_TEMPLATE_PATH .'/lang/ru/js_messages.php';

if (!\Bitrix\Main\Loader::includeModule('citrus.core'))
{
	return;
}

$templateAssets = \Citrus\Core\ThemeBuilder::getFileArray($appPath . '/src/');

$arTemplateCoreConfig = array(

	'icon' => array(
		'css' => array(
			$appPath.'icon-fonts/icons.css',
		),
		'use' => CJSCore::USE_PUBLIC,
	),

	'app' => array(
		'js' => array_column($templateAssets['js'], 'output'),
		'css' => array_column($templateAssets['css'], 'output'),
		'rel' => ['citrusUI', 'jquery', 'inview', 'citrus.core.popup', 'icon'],
		'use' => CJSCore::USE_PUBLIC,
		'lang' => $jsMessagesFilePath,
	),
);

foreach ($arTemplateCoreConfig as $ext => $arExt)
{
	CJSCore::RegisterExt($ext, $arExt);
}
