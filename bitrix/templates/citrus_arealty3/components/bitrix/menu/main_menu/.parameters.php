<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main\Localization\Loc;

$arTemplateParameters = array(
	"TRUNCATE_TEXT_LENGTH" => Array(
		"NAME" => Loc::getMessage("MENU_TRUNCATE_LENGTH"),
		"TYPE" => "STRING",
		"DEFAULT" => "50",
	),
);
?>