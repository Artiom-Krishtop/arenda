<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

if (!CModule::IncludeModule("iblock"))
{
	return;
}

$arPropertyFiles = array("" => '-');
$rsProp = CIBlockProperty::GetList(Array("sort" => "asc", "name" => "asc"),
	Array("ACTIVE" => "Y", "IBLOCK_ID" => $arCurrentValues["IBLOCK_ID"]));
while ($arr = $rsProp->Fetch())
{
	if (in_array($arr["PROPERTY_TYPE"], array('F')))
	{
		$arPropertyFiles[$arr["CODE"]] = "[" . $arr["CODE"] . "] " . $arr["NAME"];
	}
}

$arTemplateParameters = array(
	"DISPLAY_DATE" => Array(
		"NAME" => GetMessage("T_IBLOCK_DESC_NEWS_DATE"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y",
	),
	"DISPLAY_PICTURE" => Array(
		"NAME" => GetMessage("T_IBLOCK_DESC_NEWS_PICTURE"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y",
	),
	"DISPLAY_PREVIEW_TEXT" => Array(
		"NAME" => GetMessage("T_IBLOCK_DESC_NEWS_TEXT"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "Y",
	),
	"RESIZE_IMAGE_WIDTH" => Array(
		"NAME" => GetMessage("PARAM_RESIZE_IMAGE_WIDTH"),
		"TYPE" => "STRING",
		"DEFAULT" => "150",
	),
	"RESIZE_IMAGE_HEIGHT" => Array(
		"NAME" => GetMessage("PARAM_RESIZE_IMAGE_HEIGHT"),
		"TYPE" => "STRING",
		"DEFAULT" => "150",
	),
	"MORE_PHOTO" => array(
		"NAME" => GetMessage("PARAM_MORE_PHOTO_PROPERTY"),
		"TYPE" => "LIST",
		"VALUES" => $arPropertyFiles,
	),
);
