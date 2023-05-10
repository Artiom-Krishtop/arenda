<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Main\Localization\Loc;

$arTemplateParameters = array(
	"CLASS"=>array(
		"NAME" => GetMessage("INCLUDE_ADDITIONAL_CLASS"),
		"TYPE" => "TEST",
		"DEFAULT" => "",
	),
	"BG_COLOR"=>array(
		"NAME" => GetMessage("INCLUDE_TPL_BG_COLOR"),
		"TYPE" => "LIST",
		"DEFAULT" => "",
		'VALUES' => array(
			'N' => GetMessage('INCLUDE_TPL_BG_OVERLAY__NONE'),
			'SITE' => GetMessage('INCLUDE_TPL_BG_OVERLAY__SITE'),
			'GRAY' => GetMessage('INCLUDE_TPL_BG_OVERLAY__GRAY'),
		),
	),
	"CUT_CONTENT_HEIGHT" => array(
		"NAME" => Loc::getMessage("INCLUDE_TPL_CUT_CONTENT_HEIGHT"),
		"TYPE" => "STRING",
	),
	"BOTTOM_SUBSTRATE" => array(
		"NAME" => Loc::getMessage("INCLUDE_TPL_BOTTOM_SUBSTRATE"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "N"
	),
	"VIEW_TARGET" => [
		"NAME" => Loc::getMessage("CITRUS_AREALTY_CORE_INCLUDE_VIEW_TARGET"),
		"TYPE" => "STRING",
	],
);