<?php

use Bitrix\Main\Localization\Loc;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

Loc::loadMessages(__FILE__);

$arComponentParameters = array(
	"PARAMETERS"  =>  array(
		"TITLE" => array(
			"PARENT" => "BASE",
			"NAME" => Loc::getMessage("CITRUS_FZ152_BROWSER_TITLE"),
			"TYPE" => "STRING",
			"DEFAULT" => '',
		),
	),
);
