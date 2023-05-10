<?php

use Bitrix\Main\Localization\Loc;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

Loc::loadMessages(__FILE__);

$arComponentParameters = array(
	"PARAMETERS"  =>  array(
		"PATH" => array(
			"PARENT" => "BASE",
			"NAME" => Loc::getMessage("CITRUS_AREALTY_FAVOURITES_PATH"),
			"TYPE" => "STRING",
			"MULTIPLE" => "N",
			"DEFAULT" => '',
		),
	),
);
