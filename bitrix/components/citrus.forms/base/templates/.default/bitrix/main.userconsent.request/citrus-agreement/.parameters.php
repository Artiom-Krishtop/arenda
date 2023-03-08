<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arTemplateParameters = array(
	"FORM_ID" => Array(
		"NAME" => GetMessage("CONSENT_FORM_ID"),
		"TYPE" => "STRING",
		"DEFAULT" => "",
		"PARENT" => 'USER_CONSENT',
	),
);

?>