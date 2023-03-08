<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$arTemplateParameters = array(
	"SUB_TEXT"=>array(
		"NAME" => GetMessage("TPL_PAR_FIELD_SUB_TEXT_TITLE"),
		"TYPE" => "STRING",
		"DEFAULT" => "",	
	),	
	"ANCHOR_ID"=>array(
		"NAME" => GetMessage("TPL_PAR_FIELD_ANCHOR_ID_TITLE"),
		"TYPE" => "STRING",
		"DEFAULT" => "",	
	),
	"JQUERY_VALID"=>array(
		"NAME" => GetMessage("TPL_PAR_FIELD_JQUERY_VALID_TITLE"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y",	
	)
);
