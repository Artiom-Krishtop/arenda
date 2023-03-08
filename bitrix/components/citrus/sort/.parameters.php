<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
/** @var array $arCurrentValues */

use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

if(!CModule::IncludeModule("iblock"))
	return;

$arTypesEx = CIBlockParameters::GetIBlockTypes(array("-"=>" "));

$arIBlocks=array();
$db_iblock = CIBlock::GetList(array("SORT"=>"ASC"), array("SITE_ID"=>$_REQUEST["site"], "TYPE" => ($arCurrentValues["IBLOCK_TYPE"]!="-"?$arCurrentValues["IBLOCK_TYPE"]:"")));
while($arRes = $db_iblock->Fetch())
	$arIBlocks[$arRes["ID"]] = $arRes["NAME"];

// get fields
$sortFields = CIBlockParameters::GetFieldCode( Loc::getMessage("CITRUS_SORT_FIELD_CODE"), "SORT_FIELDS");

// get properties
$rsProp = CIBlockProperty::GetList(array("sort"=>"asc", "name"=>"asc"), array("ACTIVE"=>"Y", "IBLOCK_ID"=>(isset($arCurrentValues["IBLOCK_ID"])?$arCurrentValues["IBLOCK_ID"]:$arCurrentValues["ID"])));
while ($arr=$rsProp->Fetch())
{   
	$arProperty[$arr["CODE"]] = "[".$arr["CODE"]."] ".$arr["NAME"];

	if (in_array($arr["PROPERTY_TYPE"], array("L", "N", "S")))
	{
		$sortFields['VALUES']['PROPERTY_'.$arr["CODE"]] = "[".$arr["CODE"]."] ".$arr["NAME"];
	}
}

$arSorts = array("ASC"=> Loc::getMessage("T_IBLOCK_DESC_ASC"), "DESC"=> Loc::getMessage("T_IBLOCK_DESC_DESC"));

$arViewList = array(
	"CARDS" => Loc::getMessage("CITRUS_PARAMS_VIEW_CARDS"),
	"LIST" => Loc::getMessage("CITRUS_PARAMS_VIEW_LIST"),
	"TABLE" => Loc::getMessage("CITRUS_PARAMS_VIEW_TABLE"),
);

$arComponentParameters = array(
	"GROUPS" => array(
		"SORT_FIELDS" => array(
			"NAME" => Loc::getMessage("CITRUS_PARAMS_SORT_GROUP"),
			"SORT" => 600
		),
		"VIEW" => array(
			"NAME" => Loc::getMessage("CITRUS_PARAMS_VIEW_LIST"),
			"SORT" => 700
		),
	),
	"PARAMETERS" => array(
		"IBLOCK_TYPE" => array(
			"PARENT" => "BASE",
			"NAME" =>  Loc::getMessage("T_IBLOCK_DESC_LIST_TYPE"),
			"TYPE" => "LIST",
			"VALUES" => $arTypesEx,
			"DEFAULT" => "news",
			"REFRESH" => "Y",
		),
		"IBLOCK_ID" => array(
			"PARENT" => "BASE",
			"NAME" =>  Loc::getMessage("T_IBLOCK_DESC_LIST_ID"),
			"TYPE" => "LIST",
			"VALUES" => $arIBlocks,
			"DEFAULT" => '',
			"ADDITIONAL_VALUES" => "Y",
			"REFRESH" => "Y",
		),
		"SORT_FIELD_CODE" => $sortFields,
		"SORT_FIELD_NAMES" => array(
			"PARENT" => "SORT_FIELDS",
			"NAME" =>  Loc::getMessage("CITRUS_SORT_FIELD_NAMES"),
			"TYPE" => "LIST",
			"DEFAULT" => "",
			"VALUES" => array(

			),
			"MULTIPLE" => "Y",
			"ADDITIONAL_VALUES" => "Y",
		),
		"SORT_FIELDS" => array(
			"PARENT" => "SORT_FIELDS",
			"NAME" =>  Loc::getMessage("CITRUS_SORT_FIELD_ARRAY"),
			"TYPE" => "LIST",
			"DEFAULT" => "",
			"VALUES" => array(

			),
			"MULTIPLE" => "Y",
			"ADDITIONAL_VALUES" => "Y",
		),
		"DEFAULT_SORT_ORDER" => array(
			"PARENT" => "SORT_FIELDS",
			"NAME" =>  Loc::getMessage("CITRUS_DEFAULT_SORT_ORDER"),
			"TYPE" => "LIST",
			"DEFAULT" => "DESC",
			"VALUES" => $arSorts,
		),
		"VIEW_LIST" => array(
			"PARENT" => "VIEW",
			"NAME" =>  Loc::getMessage("CITRUS_PARAMS_VIEW_LIST"),
			"TYPE" => "LIST",
			"DEFAULT" => "CARDS",
			"VALUES" => $arViewList,
			"MULTIPLE" => "Y",
			"ADDITIONAL_VALUES" => "Y",
		),
		"VIEW_DEFAULT" => array(
			"PARENT" => "VIEW",
			"NAME" =>  Loc::getMessage("CITRUS_PARAMS_VIEW_LIST_DEFAULT"),
			"TYPE" => "LIST",
			"DEFAULT" => "CARDS",
			"VALUES" => $arViewList,
			"ADDITIONAL_VALUES" => "Y",
		),
	)
);