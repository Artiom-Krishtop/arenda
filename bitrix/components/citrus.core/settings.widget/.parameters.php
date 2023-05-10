<?php

use Bitrix\Main\Localization\Loc;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$arComponentParameters = [
	'PARAMETERS' => [
		'DEMO_MODE' => [
			'NAME' => Loc::getMessage('CITRUS_CORE_SETTINGS_WIDGET_PARAM_DEMO_MODE'),
			'TYPE' => 'CHECKBOX',
			'DEFAULT' => 'N',
		],
	]
];

