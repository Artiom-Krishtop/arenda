<?

use Bitrix\Main\Localization\Loc;

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$arTemplateParameters = array(
	"SUB_TEXT" => array(
		"NAME" => GetMessage("TPL_PAR_FIELD_SUB_TEXT_TITLE"),
		"TYPE" => "STRING",
		"DEFAULT" => "",
	),
	"JQUERY_VALID" => array(
		"NAME" => GetMessage("TPL_PAR_FIELD_JQUERY_VALID_TITLE"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y",
	),
	"AJAX"=>array(
		"NAME" => GetMessage("TPL_PAR_FIELD_AJAX_TITLE"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y",
	),
	"FORM_CLASS" => array(
		"NAME" => Loc::getMessage("TPL_PAR_FIELD_FORM_WRAPPER_CLASS"),
		"TYPE" => "STRING",
		'PARENT' => 'FRONTEND',
		"DEFAULT" => "",
	),
	"FORM_PLACE_MODE" => array(
		"NAME" => Loc::getMessage("TPL_PAR_FIELD_FORM_PLACE_MODE"),
		"TYPE" => "LIST",
		'PARENT' => 'FRONTEND',
		'VALUES' => array(
			'POPUP' => Loc::getMessage("TPL_PAR_FIELD_FORM_PLACE_MODE_POPUP"),
			'PAGE' => Loc::getMessage("TPL_PAR_FIELD_FORM_PLACE_MODE_PAGE"),
		),
		"DEFAULT" => "PAGE",
	),
	"FORM_STYLE" => array(
		"NAME" => GetMessage("TPL_PAR_FORM_STYLE"),
		"TYPE" => "LIST",
		'PARENT' => 'FRONTEND',
		'VALUES' => array(
			'GRAY' => GetMessage('TPL_PAR_FORM_STYLE_GRAY'),
			'WHITE' => GetMessage('TPL_PAR_FORM_STYLE_WHITE'),
			'BORDERED' => GetMessage('TPL_PAR_FORM_STYLE_BORDERED'),
		),
		"DEFAULT" => "GRAY",
	),
	"AGREEMENT_LINK"=>array(
		"NAME" => GetMessage("TPL_PAR_AGREEMENT_LINK_NAME"),
		"TYPE" => "STRING",
		'PARENT' => 'FRONTEND',
		'DEFAULT' => ""
	),
	"BUTTON_POSITION" => array(
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
	"BUTTON_CLASS" => array(
		"NAME" => Loc::getMessage("TPL_PAR_BUTTON_POSITION"),
		"TYPE" => "STRING",
		'PARENT' => 'FRONTEND',
		"DEFAULT" => "",
	),
	"HIDDEN_ANTI_SPAM" => array(
		"NAME" => GetMessage("TPL_PAR_HIDDEN_ANTI_SPAM"),
		'PARENT' => 'ADDITIONAL_SETTINGS',
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y",
	)
);
