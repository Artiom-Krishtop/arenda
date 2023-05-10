<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

$arComponentDescription = array(
	"NAME" => Loc::getMessage("DESC_UREGFORM_NAME"),
	"DESCRIPTION" => Loc::getMessage("DESC_UREGFORM_DESCRIPTION"),
	"ICON" => "/images/cat_detail.gif",
	"CACHE_PATH" => "Y",
	"SORT" => 40,
	"PATH" => array(
		"ID" => "citrus",
		"NAME" => Loc::getMessage("DESC_HBFORM_COMPONY_NAME"),
		"CHILD" => array(
			"ID" => "smartform",
			"NAME" => GetMessage('DESC_HBFORM_SMART_FORM')
		)
	)
);

?>