<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arTemplateParameters = array(
	"SHOW_MAP" => Array(
		"NAME" => GetMessage("CITRUS_TEMPLATE_SHOW_MAP"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "N",
	),
    "HEIGHT_OVERFLOW" => Array(
        "NAME" => GetMessage("CITRUS_TEMPLATE_HEIGHT_OVERFLOW"),
        "TYPE" => "CHECKBOX",
        "DEFAULT" => "N",
    ),
);
