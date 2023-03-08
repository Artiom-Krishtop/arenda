<?php
/**
 * @var $arParams
 * @var $arResult
 */
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Main\IO\File;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages('template.php');

function dm($var, $vd = false, $tofile = false, $onlyAdmin = true) {

    global $USER;

    if ($onlyAdmin && !$USER->IsAdmin()) {
        return;
    }

    if ($tofile) {
        ob_start();
    }

    echo "<pre>";

    if ($vd) {
        var_dump($var);
    } else {
        print_r($var);
    }

    echo "</pre>";

    if ($tofile) {
        file_put_contents(Bitrix\Main\Application::getDocumentRoot() . "/debug.txt", ob_get_clean());
    }

}

/*if(isset($_REQUEST["ajax"]) && $_REQUEST["ajax"] === "y")
    $_CHECK = &$_REQUEST;
elseif(isset($_REQUEST["del_filter"]))
    $_CHECK = array();
elseif(isset($_GET["set_filter"]))
    $_CHECK = &$_GET;
elseif($arParams["SMART_FILTER_PATH"])
    $_CHECK = $this->convertUrlToCheck($arParams["~SMART_FILTER_PATH"]);
elseif($arParams["SAVE_IN_SESSION"] && isset($_SESSION[$arParams["FILTER_NAME"]][$this->SECTION_ID]))
    $_CHECK = $_SESSION[$arParams["FILTER_NAME"]][$this->SECTION_ID];
else
    $_CHECK = array();*/

/*$my_props = array("commercial_type", "commercial_features");

foreach ($my_props as $my_prop){
    $property_enums = CIBlockPropertyEnum::GetList(Array("SORT"=>"ASC"), Array("IBLOCK_ID"=>$arParams["IBLOCK_ID"], "CODE"=>$my_prop));
    while($enum_fields = $property_enums->GetNext())
    {
        if(!isset($arResult["ITEMS"][$enum_fields["PROPERTY_ID"]])){
            $res = CIBlockProperty::GetByID($my_prop, $arParams["IBLOCK_ID"], false);
            if($ar_res = $res->GetNext()){

                $arResult["ITEMS"][$ar_res["ID"]] = array(
                    "ID" => $ar_res["ID"],
                    "IBLOCK_ID" => $ar_res["IBLOCK_ID"],
                    "CODE" => $ar_res["CODE"],
                    "~NAME" => $ar_res["~NAME"],
                    "NAME" => $ar_res["NAME"],
                    "PROPERTY_TYPE" => $ar_res["PROPERTY_TYPE"],
                    "USER_TYPE" => $ar_res["USER_TYPE"],
                    "USER_TYPE_SETTINGS" => $ar_res["USER_TYPE_SETTINGS"],
                    "DISPLAY_TYPE" => "P",
                    "DISPLAY_EXPANDED" => "Y",
                    "FILTER_HINT" => $ar_res["HINT"],
                    "VALUES" => array()
                );

            }
        }
        $key = abs(crc32($enum_fields["ID"]));
        $arResult["ITEMS"][$ar_res["ID"]]["VALUES"][$enum_fields["ID"]] = array(
            "CONTROL_ID" => $arParams["FILTER_NAME"].'_'.$enum_fields["PROPERTY_ID"].'_'.$key,
            "CONTROL_NAME" => $arParams["FILTER_NAME"].'_'.$enum_fields["PROPERTY_ID"].'_'.$key,
            "CONTROL_NAME_ALT" => $arParams["FILTER_NAME"].'_'.$enum_fields["PROPERTY_ID"],
            "HTML_VALUE_ALT" => $key,
            "HTML_VALUE" => "Y",
            "VALUE" => $enum_fields["VALUE"],
            "SORT" => $enum_fields["SORT"],
            "UPPER" => mb_strtoupper($enum_fields["VALUE"]),
            "FLAG" => '',
            "URL_ID" => $enum_fields["EXTERNAL_ID"],
        );
    }
}*/

//dm(CIBlockSectionPropertyLink::GetArray($arParams["IBLOCK_ID"]));


//$rooms_area = array();

/*$cache = \Bitrix\Main\Data\Cache::createInstance();
$cache_id = "main_rooms_area_" . md5(serialize("rooms_area"));
$cache_dir = "/travelsoft/main_rooms_area";

if ($cache->initCache(360000000, $cache_id, $cache_dir)) {
    $rooms_area = $cache->getVars();
} elseif ($cache->startDataCache()) {

    $rooms_ = CIBlockElement::GetList(false, Array("IBLOCK_ID"=>$arParams["IBLOCK_ID"], "ACTIVE"=>"Y", "!PROPERTY_rooms_area_VALUE"=>false), false, false, Array("IBLOCK_ID", "ID", "PROPERTY_rooms_area"));
    foreach ($rooms_ as $room) {
        $rooms_area[] = $room["PROPERTY_rooms_area_VALUE"];
    }

    if (!empty($rooms_area)) {
        $cache->endDataCache($rooms_area);
    } else {
        $cache->abortDataCache();
    }

    global $CACHE_MANAGER;
    $CACHE_MANAGER->StartTagCache($cache_dir);
    $CACHE_MANAGER->RegisterTag("iblock_id_" . $arParams["IBLOCK_ID"]);
    $CACHE_MANAGER->EndTagCache();

}*/

/*$res = CIBlockElement::GetList(false, Array("IBLOCK_ID"=>$arParams["IBLOCK_ID"], "ACTIVE"=>"Y", "!PROPERTY_rooms_area_VALUE"=>false), false, false, Array("IBLOCK_ID", "ID", "PROPERTY_rooms_area"));
while($ar_fields = $res->GetNext())
{
    $rooms_area[] = current($ar_fields["PROPERTY_ROOMS_AREA_VALUE"]);
}

if(!empty($rooms_area)) {
    $rooms_area_min = min($rooms_area);
    $rooms_area_max = max($rooms_area);
}
if(isset($rooms_area_min) && isset($rooms_area_max)){
    $res = CIBlockProperty::GetByID("rooms_area", $arParams["IBLOCK_ID"], false);
    if($ar_res = $res->GetNext()){

        $arResult["ITEMS"][$ar_res["ID"]] = array(
            "ID" => $ar_res["ID"],
            "IBLOCK_ID" => $ar_res["IBLOCK_ID"],
            "CODE" => $ar_res["CODE"],
            "~NAME" => $ar_res["~NAME"],
            "NAME" => $ar_res["NAME"],
            "PROPERTY_TYPE" => $ar_res["PROPERTY_TYPE"],
            "USER_TYPE" => $ar_res["USER_TYPE"],
            "USER_TYPE_SETTINGS" => $ar_res["USER_TYPE_SETTINGS"],
            "DISPLAY_TYPE" => "A",
            "DISPLAY_EXPANDED" => "Y",
            "FILTER_HINT" => $ar_res["HINT"],
            "VALUES" => array()
        );
        $minID = $arParams["FILTER_NAME"].'_'.$ar_res["ID"].'_MIN';
        $maxID = $arParams["FILTER_NAME"].'_'.$ar_res["ID"].'_MAX';
        $arResult["ITEMS"][$ar_res["ID"]]["VALUES"] = array(
            "MIN" => array(
                "CONTROL_ID" => $minID,
                "CONTROL_NAME" => $minID,
                "VALUE" => $rooms_area_min,
            ),
            "MAX" => array(
                "CONTROL_ID" => $maxID,
                "CONTROL_NAME" => $maxID,
                "VALUE" => $rooms_area_max,
            ),
        );

    }
}

foreach($arResult["ITEMS"] as $PID => $arItem) {
    foreach ($arItem["VALUES"] as $key => $ar) {
        if(
            isset($_CHECK[$ar["CONTROL_NAME"]])
            || (
                isset($_CHECK[$ar["CONTROL_NAME_ALT"]])
                && $_CHECK[$ar["CONTROL_NAME_ALT"]] == $ar["HTML_VALUE_ALT"]
            )
        ) {
            if ($arItem["PROPERTY_TYPE"] == "N") {
                $arResult["ITEMS"][$PID]["VALUES"][$key]["HTML_VALUE"] = htmlspecialcharsbx($_CHECK[$ar["CONTROL_NAME"]]);
                $arResult["ITEMS"][$PID]["DISPLAY_EXPANDED"] = "Y";
            }
            elseif($_CHECK[$ar["CONTROL_NAME"]] == $ar["HTML_VALUE"])
            {
                $arResult["ITEMS"][$PID]["VALUES"][$key]["CHECKED"] = true;
                $arResult["ITEMS"][$PID]["DISPLAY_EXPANDED"] = "Y";
            }
            elseif($_CHECK[$ar["CONTROL_NAME_ALT"]] == $ar["HTML_VALUE_ALT"])
            {
                $arResult["ITEMS"][$PID]["VALUES"][$key]["CHECKED"] = true;
                $arResult["ITEMS"][$PID]["DISPLAY_EXPANDED"] = "Y";
            }
        }
    }
}*/

$arResult["DEFAULT_ITEMS"] = $arResult["ITEMS"];

//dm($arResult["ITEMS"]);


if ($arParams["FORM_ACTION_ON_SECTION_PAGE"] === "Y" && $arParams["SECTION_CODE"])
{
	$rsSection = CIBlockSection::GetList(
		Array("SORT" => "ASC"),
		Array("IBLOCK_ID" => $arParams["IBLOCK_ID"], "=CODE" => $arParams["SECTION_CODE"]),
		$bIncCnt = false,
		$arSelectFields = Array("ID", "SECTION_PAGE_URL"),
		array('nTopCount' => 1)
	);
	if ($arSection = $rsSection->GetNext())
	{
		$arResult["FORM_ACTION"] = $arSection["SECTION_PAGE_URL"];
	}
}

$numExpanded = count(array_filter($arResult['ITEMS'], function ($item) {
	return $item['DISPLAY_EXPANDED'] == 'Y';
}));

$arParams["MAX_ITEMS_COUNT"] = min($numExpanded, $arParams["MAX_ITEMS_COUNT"] ? $arParams["MAX_ITEMS_COUNT"] : 6);

/**
 * � ���������� ������� ���� ���� � ������� ������ ��� ������������� � ������� �� �����
 *
 * @var bool $hasMetroMap ���� �� ��� ���������� ������ ����� ����� (���� ��� � ����� �������������� ������ �������)
 */
if (\Citrus\Arealty\Entity\SettingsTable::getValue('METRO_CITY_ID'))
{
	$metroCity = \Citrus\Arealty\Entity\SettingsTable::getRow([
		'filter' => ['=SITE_ID' => SITE_ID],
		'select' => ['CODE' => 'METRO_CITY.CODE', 'SVG' => 'METRO_CITY.SVG'],
	]);
	$hasMetroMap = strlen($metroCity['SVG']) > 0;
	$arResult['METRO_CITY'] = $metroCity['CODE'];
}
else
{
	$arResult['METRO_CITY'] = 'moscow';
	$hasMetroMap = true;
}

$customTemplateByUserType = [];
if ($hasMetroMap)
{
	$customTemplateByUserType['METRO'] = 'CitrusArealtyMetroStation';
}

/**
 * ��������� ������ ������� ����� � ������� ��������� � ���������� ������� �������
 */
$metroPropertyId = array_reduce($arResult['ITEMS'], function ($current, $property) {
	if ($property['USER_TYPE'] == 'CitrusArealtyMetroStation')
	{
		return $property['ID'];
	}
	return $current;
});

if ($availableMetroStations = \Citrus\Core\array_get($arResult, sprintf('ITEMS.%d.VALUES', $metroPropertyId)))
{
	$cityMetroStations = array_column(
		\Citrus\Arealty\Entity\Metro\StationTable::getItems($arResult['METRO_CITY']),
		null,
		'ID'
	);

	$arResult['ITEMS'][$metroPropertyId]['VALUES'] = array_intersect_key($availableMetroStations, $cityMetroStations);
}

//filter empty fields
$arResult["ITEMS"] = array_filter($arResult["ITEMS"], function($arItem){
	return $arItem['ID'] && !empty($arItem["VALUES"]) &&
				!( $arItem["DISPLAY_TYPE"] == "A" && ($arItem["VALUES"]["MAX"]["VALUE"] - $arItem["VALUES"]["MIN"]["VALUE"] <= 0) );
});
$arResult["SHOW_EXPANDED"] = count($arResult["ITEMS"]) > $arParams["MAX_ITEMS_COUNT"];

$arResult["ELEMENT_COUNT"] = CIBlockElement::GetList(array(), $this->__component->makeFilter($arParams['FILTER_NAME']), array(), false);

$getTemplateData = function ($templateName) use (&$arResult) {
	$template = array('NAME' => $templateName);

	$template['PATH'] = \Bitrix\Main\IO\Path::normalize(__DIR__.'/field_template/'.$templateName.'/') . '/';
	$template['ASSETS_PATH'] = '/field_template/'.$templateName.'/';

	if (File::isFileExists($template['PATH'].'template.php')) {
		//assets of field template
		if (File::isFileExists($template['PATH'].'style.css') &&
			array_search($template['ASSETS_PATH'].'style.css', $arResult["ADDITIONAL_STYLES"]) === false )
			$arResult["ADDITIONAL_STYLES"][] = $template['ASSETS_PATH'].'style.css';

		if (File::isFileExists($template['PATH'].'script.js') &&
			array_search($template['ASSETS_PATH'].'script.css', $arResult["ADDITIONAL_SCRIPTS"]) === false )
			$arResult["ADDITIONAL_SCRIPTS"][] = $template['ASSETS_PATH'].'script.js';
	} else {
		$template['PATH'] = false;
	}
	return $template;
};

/**
 * Field Template Settings
 */
$customTemplateByDisplayType = array(
	"NUMBERS" => array("A","B"),
	"DROPDOWN" => array("R", 'G', 'F',"P"),
	"CALENDAR" => "U",
	"LINE_CHECKBOX" => array("K", "H", "K1")
);

//for custom templates
$customTemplateByCode = array(
	//"DROPDOWN" => array("deal_type", "rooms")
);
//combine fields in 1 custom template by code
$customCombinedTemplates = array(
	//"LOCATION" => array("district", 'metro_stations'),
);

$arResult["ADDITIONAL_STYLES"] = array();
$arResult["ADDITIONAL_SCRIPTS"] = array();
$arNewItems = $properties = $propertyHints = [];

if (COption::GetOptionString('citrus.arealty', 'show_smartfilter_hint') !== 'N')
{
	$iblockId = $arParams['IBLOCK_ID'];
	$propertyIds = array_column($arResult['ITEMS'], 'ID');
	$properties = \Citrus\Arealty\Cache::remember('smart.filter.properties.' . $iblockId, 60*24, function () use ($iblockId, $propertyIds) {
		$properties = [];
		$propertiesIterator = CIBlockProperty::GetList([], ['IBLOCK_ID' => $iblockId]);
		while ($property = $propertiesIterator->Fetch())
		{
			if (in_array($property['ID'], $propertyIds))
			{
				$properties[$property['ID']] = $property;
			}
		}
		return $properties;
	});
	$propertyHints = array_filter(array_column($properties, 'HINT', 'ID'));
}

foreach ($arResult["ITEMS"] as &$arItem ) {

	//add hint
	if (array_key_exists($arItem['ID'], $propertyHints))
	{
		$arItem['HINT'] = $propertyHints[$arItem['ID']];
	}

	foreach ($customCombinedTemplates as $template => &$code) {
		if ( (is_array($code) && array_search($arItem["CODE"], $code) !== false )
			|| (is_string($code) && $arItem["CODE"] == $code) ) {
			$arItem['COMBINED_TEMPLATE'] = $template;
		}
	}
	if ($arItem['COMBINED_TEMPLATE']) {
		if ($arNewItems[$arItem['COMBINED_TEMPLATE']]) {
			$arNewItems[$arItem['COMBINED_TEMPLATE']]['FIELDS'][$arItem['CODE']] = $arItem;
			$arNewItems[$arItem['COMBINED_TEMPLATE']]['ID'] .= '_'.$arItem['ID'];
		} else {
			$arNewItems[$arItem['COMBINED_TEMPLATE']] = array(
				"TEMPLATE" => $getTemplateData($arItem['COMBINED_TEMPLATE']),
				"IS_COMBINE" => true,
				"CODE" => $arItem['COMBINED_TEMPLATE'],
				'ID' => $arItem['ID'],

				"FIELDS" => array(
					$arItem['CODE'] => $arItem
				)
			);
		}
	} else {
		$fieldTemplate = '';

		foreach ($customTemplateByDisplayType as $template => $type) {
			if ( (is_array($type) && array_search($arItem["DISPLAY_TYPE"], $type) !== false ) ||
				(is_string($type) && $arItem["DISPLAY_TYPE"] == $type) ) $fieldTemplate = $template;
		}

		foreach ($customTemplateByCode as $template => $code) {
			if ( (is_array($code) && array_search($arItem["CODE"], $code) !== false ) ||
				(is_string($code) && $arItem["CODE"] == $code) ) $fieldTemplate = $template;
		}

		foreach ($customTemplateByUserType as $template => $userType) {
			if ( (is_array($userType) && array_search($arItem["USER_TYPE"], $userType) !== false ) ||
				(is_string($userType) && $arItem["USER_TYPE"] == $userType) ) $fieldTemplate = $template;
		}

		//dm($fieldTemplate);

		//Default Template
		if (!$fieldTemplate) $fieldTemplate = "DROPDOWN";

		if ($fieldTemplate === 'NUMBERS') {
			$arItem["VALUES"]["MIN"]["HTML_VALUE"] = $arItem["VALUES"]["MIN"]["HTML_VALUE"] ? $arItem["VALUES"]["MIN"]["HTML_VALUE"] : '';
			$arItem["VALUES"]["MAX"]["HTML_VALUE"] = $arItem["VALUES"]["MAX"]["HTML_VALUE"] ? $arItem["VALUES"]["MAX"]["HTML_VALUE"] : '';
		}

		$arItem['TEMPLATE'] = $getTemplateData($fieldTemplate);
		$arNewItems[] = $arItem;
	}
}
$arResult['ITEMS'] = $arNewItems;

uasort($arResult['ITEMS'], function ($a, $b) {
	$a = $a['DISPLAY_EXPANDED'] == 'Y' ? 1 : 0;
	$b = $b['DISPLAY_EXPANDED'] == 'Y' ? 1 : 0;
	if ($a == $b) {
		return 0;
	}
	return ($a > $b) ? -1 : 1;
});

$this->__component->setResultCacheKeys(array(
	'ADDITIONAL_STYLES',
	'ADDITIONAL_SCRIPTS',
    'DEFAULT_ITEMS'
));