<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arTemplateParameters = array(
	"SHOW_TITLE" => Array(
		"NAME" => GetMessage("TPL_PAR_F_SHOW_TITLE"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y",
	),
	"FORM_TITLE" => Array(
		"NAME" => GetMessage("TPL_PAR_F_FORM_TITLE"),
		"TYPE" => "STRING",
		"DEFAULT" => GetMessage('TPL_PAR_FORM_TITLE'),
	),
	"FORM_TOOLTIP" => Array(
		"NAME" => GetMessage("TPL_PAR_F_FORM_TOOLTIP"),
		"TYPE" => "STRING",
		"DEFAULT" => GetMessage('TPL_PAR_FORM_TOOLTIP'),
	),
	"OK_MESSAGE" => Array(
		"NAME" => GetMessage("TPL_PAR_F_OK_MESSAGE"),
		"TYPE" => "STRING",
		"DEFAULT" => GetMessage('TPL_PAR_OK_MESSAGE_DEFAULT'),
	)
);
?>