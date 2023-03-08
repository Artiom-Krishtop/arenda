<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("CITRUS_SUBSCRIBE_FORM_NAME"),
	"DESCRIPTION" => GetMessage("CITRUS_SUBSCRIBE_FORM_DESCRIPTION"),
	"ICON" => "/images/icon.gif",
	"CACHE_PATH" => "Y",
	"PATH" => array(
		"ID" => "citrus",
		"NAME" => GetMessage("CITRUS_SUBSCRIBE_FORM_CITRUS"),
	),
);

?>