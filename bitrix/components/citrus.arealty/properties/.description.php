<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("CITRUS_AREALTY_PROPERTIES_NAME"),
	"DESCRIPTION" => GetMessage("CITRUS_AREALTY_PROPERTIES_DESC"),
	"PATH" => array(
		"ID" => "utility",
		"CHILD" => array(
			"ID" => "include_area",
			"NAME" => GetMessage("CITRUS_AREALTY_PROPERTIES_DESC"),
		),
	),
);
?>