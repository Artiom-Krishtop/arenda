<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use Citrus\Arealty\Object\Address;

foreach ($arResult["PROPERTY_LIST_FULL"] as $key => $prop) {
    if(!empty($prop['CODE'])){
        $arResult["PROPERTY_LIST_FULL"][$prop['CODE']] = $prop;

        unset($arResult["PROPERTY_LIST_FULL"][$key]); 
    }
}

$arResult['TEMPLATE_TABS'] = array(
    'COMMON_PARAMETERS' => array(
        'ACTIVE',
        'NAME',
        'DATE_ACTIVE_FROM',
        'DATE_ACTIVE_TO',
        'deal_type',
        'NEW_COMMERCIAL_TYPE',
        'ownership_type'
    ),
    // 'CONTACTS' => array(

    // ),
    'ADRESS' => array(
        // 'COUNTRY',
        // 'CITY',
        // 'CITY_AREA',
        // 'district',
        // 'ADDRESS',
        // 'BUILDING',

    ),
    'PARAMETERS' => array(
        'category',
        'NEW_FLOOR',
        'rooms_html',
        'NEW_ROOMS_AREA',
        'rooms',
        'NEW_ROOMS_TYPE',
        'NEW_COMMERCIAL_FEATURES',
        'common_area',
        'phone_lines',
        'metro_stations',
        'office_class',
        'garage_features',
        'DESCRIPTION',
    ),
    'PRICE' => array(
        'cost',
        'price_for_meter',
        'cost_curr',
        'cost_period',
        'cost_unit'
    ),
    'PHOTO' => array(
        'photo'
    ),
    'PANORAMIC_PHOTO' => array(
        'PANORAMIC_PHOTOS'
    )
);



