<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

/** @var array $TEMPLATE */

Bitrix\Main\Localization\Loc::loadMessages(__FILE__);

$TEMPLATE["standard.php"] = ["name" => GetMessage("citrus_core_page"), "sort" => 1];
$TEMPLATE["page_inc.php"] = ["name" => GetMessage("citrus_core_page_inc"), "sort" => 2];

