<?

use Bitrix\Main\Localization\Loc;

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$arTemplateParameters = array(
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
	/*"FORM_PLACE_MODE" => array(
		"NAME" => Loc::getMessage("TPL_PAR_FIELD_FORM_PLACE_MODE"),
		"TYPE" => "LIST",
		'PARENT' => 'VISUAL',
		'VALUES' => array(
			'POPUP' => Loc::getMessage("TPL_PAR_FIELD_FORM_PLACE_MODE_POPUP"),
			'PAGE' => Loc::getMessage("TPL_PAR_FIELD_FORM_PLACE_MODE_PAGE"),
		),
		"DEFAULT" => "PAGE",
	),*/
	"FORM_STYLE" => array(
		"NAME" => GetMessage("TPL_PAR_FORM_STYLE"),
		"TYPE" => "LIST",
		'PARENT' => 'VISUAL',
		'VALUES' => array(
			'GRAY' => GetMessage('TPL_PAR_FORM_STYLE_GRAY'),
			'WHITE' => GetMessage('TPL_PAR_FORM_STYLE_WHITE'),
			'BORDERED' => GetMessage('TPL_PAR_FORM_STYLE_BORDERED'),
		),
		"DEFAULT" => "GRAY",
	),
	"FORM_MOD" => array(
		"NAME" => Loc::getMessage("TPL_PAR_FORM_MOD_TITLE"),
		"TYPE" => "LIST",
		'PARENT' => 'VISUAL',
		'VALUES' => array(
			'DEFAULT' => Loc::getMessage("TPL_PAR_FORM_MOD_DEFAULT"),
			'COMPACT' => Loc::getMessage("TPL_PAR_FORM_MOD_COMPACT"),
			'MODAL' => Loc::getMessage("TPL_PAR_FORM_MOD_MODAL"),
			'INLINE' => Loc::getMessage("TPL_PAR_FORM_MOD_INLINE")
		),
		"DEFAULT" => "COMPACT",
	),
	"BUTTON_POSITION" => array(
		"NAME" => GetMessage("TPL_PAR_BUTTON_POSITION"),
		"TYPE" => "LIST",
		'PARENT' => 'VISUAL',
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
		'PARENT' => 'VISUAL',
		"DEFAULT" => "",
	),
	"HIDDEN_ANTI_SPAM" => array(
		"NAME" => GetMessage("TPL_PAR_HIDDEN_ANTI_SPAM"),
		'PARENT' => 'ADDITIONAL_SETTINGS',
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y",
	),
	"AGREEMENT_LINK"=>array(
		"NAME" => Loc::getMessage("TPL_PAR_AGREEMENT_LINK"),
		"TYPE" => "STRING",
		'PARENT' => 'USER_CONSENT',
		'DEFAULT' => ""
	),
);
