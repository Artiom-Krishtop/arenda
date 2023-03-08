<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main\Localization\Loc;

$arTemplateParameters = array(
	"SPLIT_SECTION" => Array(
		"NAME" => Loc::getMessage("T_IBLOCK_PARAMS_STAFF_SPLIT_SECTION"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y",
	),
);
?>