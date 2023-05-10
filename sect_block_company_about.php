<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

?>

<? $APPLICATION->IncludeComponent(
	"citrus.arealty:contentblock",
	".default",
	array(
		"IBLOCK_ID" => \CModule::IncludeModule("citrus.arealty") ? \Citrus\Arealty\Helper::getIblock("company_about"): "",
		"IBLOCK_TYPE" => "company",
		"ELEMENT_CODE" => "main",
		"SHOW_DETAIL_LINK" => "Y",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "36000000",
		"PARENT_CACHE_KEY" => get_class($this),
	),
	$this
); ?>
