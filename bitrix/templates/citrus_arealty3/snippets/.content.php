<?
use Bitrix\Main\Localization\Loc;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

Loc::loadMessages(__FILE__);

$SNIPPETS = array(
	"service-detail.snp" => Array("title" => Loc::getMessage("CITRUS_TEMPLATE_3COLS"), "description" => Loc::getMessage("CITRUS_TEMPLATE_3COLS_DESC")),
	"button.snp" => Array("title" => Loc::getMessage("CITRUS_TEMPLATE_BUTTON"), "description" => Loc::getMessage("CITRUS_TEMPLATE_BUTTON_DESC")),
	"order-button.snp" => Array("title" => Loc::getMessage("CITRUS_TEMPLATE_ORDER_BUTTON"), "description" => Loc::getMessage("CITRUS_TEMPLATE_ORDER_BUTTON_DESC")),
	"table.snp" => Array("title" => Loc::getMessage("CITRUS_SNIPPET_TABLE_FORMATTED"), "description" => "",)
);
