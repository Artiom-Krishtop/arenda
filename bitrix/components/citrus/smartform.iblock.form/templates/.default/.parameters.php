<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$arTemplateParameters = array(
	"SUB_TEXT"=>array(
		"NAME" => GetMessage("TPL_PAR_FIELD_SUB_TEXT_TITLE"),
		"TYPE" => "STRING",
		"DEFAULT" => "",	
	),
	"JQUERY_VALID"=>array(
		"NAME" => GetMessage("TPL_PAR_FIELD_JQUERY_VALID_TITLE"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y",	
	),
	"FORM_STYLE"=>array(
		"NAME" => GetMessage("TPL_PAR_FORM_STYLE"),
		"TYPE" => "LIST",
		'PARENT' => 'FRONTEND',
		'VALUES' => array(
			'GRAY' => GetMessage('TPL_PAR_FORM_STYLE_GRAY'),
			'WHITE' => GetMessage('TPL_PAR_FORM_STYLE_WHITE'),
		),
		"DEFAULT" => "GRAY",
	),
    "BUTTON_POSITION"=>array(
        "NAME" => GetMessage("TPL_PAR_BUTTON_POSITION"),
        "TYPE" => "LIST",
        'PARENT' => 'FRONTEND',
        'VALUES' => array(
            'LEFT' => GetMessage('TPL_PAR_BUTTON_POSITION_LEFT'),
            'CENTER' => GetMessage('TPL_PAR_BUTTON_POSITION_CENTER'),
            'RIGHT' => GetMessage('TPL_PAR_BUTTON_POSITION_RIGHT'),
            'JUSTIFY' => GetMessage('TPL_PAR_BUTTON_POSITION_JUSTIFY'),
        ),
        "DEFAULT" => "RIGHT",
	),
	"HIDE_INPUTS_ON_SUCCESS" => array (
		"NAME" => GetMessage("TPL_PAR_HIDE_INPUTS_ON_SUCCESS"),
		'PARENT' => 'ADDITIONAL_SETTINGS',
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "N",
	),
	"HIDDEN_ANTI_SPAM" => array (
		"NAME" => GetMessage("TPL_PAR_HIDDEN_ANTI_SPAM"),
		'PARENT' => 'ADDITIONAL_SETTINGS',
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y",
	)
);
