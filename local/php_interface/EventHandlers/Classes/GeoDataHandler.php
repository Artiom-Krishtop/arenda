<?php

namespace EventHandlers\Classes;

use Bitrix\Main\Loader;
use CIBlock;
use CIBlockElement;
use Citrus\Yandex\Geo\GeoObject;

class GeoDataHandler
{
    protected static $geoIblocks = array();

    public static function addGeoData($arFields)
    {
        if(Loader::includeModule('citrus.arealty')){
            $geoData = array();
            $obGeoObject = self::getGeoObject($arFields['IBLOCK_ID'], $arFields['ID']);

            if($obGeoObject instanceof GeoObject){
                self::getGeoIblocks(); 

                if(intval($countryId = self::getCountryId($obGeoObject)) > 0){
                    $geoData['COUNTRY'] = $countryId;
                }
                
                if(intval($districtId = self::getDistrictId($obGeoObject)) > 0){
                    $geoData['CITY_AREA'] = $districtId;
                }
            
                if(intval($cityId = self::getCityId($obGeoObject)) > 0){
                    $geoData['CITY'] = $cityId;
                }

                if(strlen($fullAdress = self::getFullAdress($obGeoObject)) > 0){
                    $geoData['ADDRESS'] = $fullAdress;
                }

                if(!empty($geoData)){
                    CIBlockElement::SetPropertyValuesEx($arFields['ID'], $arFields['IBLOCK_ID'], $geoData);
                }
            }
        }
    }

    protected static function getFullAdress(GeoObject $obGeoObject)
    {
        return $obGeoObject->getAddress();
    }

    protected static function getGeoIblocks()
    {
        if(Loader::includeModule('iblock')){
            $dbRes = CIBlock::GetList(
                array(),
                array(
                    'TYPE' => 'geography'
                )
            );

            while ($res = $dbRes->Fetch()) {
                self::$geoIblocks[$res['CODE']] = $res['ID'];
            }
        }

        return self::$geoIblocks;
    }

    protected static function getGeoObject($iblockId, $elementId)
    {
        if (Loader::includeModule('iblock')){    
            $db = \CIBlockElement::GetList(
                array(),
                array(
                    "IBLOCK_ID" => $iblockId, 
                    'ID' => $elementId
                ),
                false,
                false,
                array(
                    'PROPERTY_GEODATA'
                )
            );
    
            if ($el = $db->GetNext()){
                if(isset($el['PROPERTY_GEODATA_VALUE']) && $el['PROPERTY_GEODATA_VALUE'] instanceof GeoObject){
                    return $el['PROPERTY_GEODATA_VALUE'];
                }
            }
		}

        return null;
    }

    protected static function getDistrictId(GeoObject $obGeoObject)
    {
        if(intval(self::$geoIblocks['region']) > 0){
            $district = str_replace(array('район', ' '), '', trim($obGeoObject->getSubAdministrativeAreaName()));
    
            if(strlen($district) > 0 && Loader::includeModule('iblock')){
                $db = \CIBlockElement::GetList(
                    array(),
                    array(
                        "IBLOCK_ID" => self::$geoIblocks['region'], 
                        '=NAME' => $district
                    ),
                    false,
                    false,
                    array(
                        'ID'
                    )
                );
        
                if ($dbDistrict = $db->GetNext()){
                    return $dbDistrict['ID'];
                }else {
                    $el = new \CIBlockElement();
                    $districtId = $el->Add(array('IBLOCK_ID' => self::$geoIblocks['region'], 'NAME' => $district));
    
                    if($districtId){
                        return $districtId;
                    }
                }
            }
        }

        return null;
    }

    protected static function getCountryId(GeoObject $obGeoObject)
    {
        if(intval(self::$geoIblocks['country']) > 0){        
            $country = trim($obGeoObject->getCountry());
    
            if(strlen($country) > 0 && Loader::includeModule('iblock')){
                $db = \CIBlockElement::GetList(
                    array(),
                    array(
                        "IBLOCK_ID" => self::$geoIblocks['country'], 
                        '=NAME' => $country
                    ),
                    false,
                    false,
                    array(
                        'ID'
                    )
                );
        
                if ($dbCountry = $db->GetNext()){
                    return $dbCountry['ID'];
                }else {
                    $el = new \CIBlockElement();
                    $countryId = $el->Add(array('IBLOCK_ID' => self::$geoIblocks['country'], 'NAME' => $country));
    
                    if($countryId){
                        return $countryId;
                    }
                }
            }
        }

        return null;
    }

    protected static function getCityId(GeoObject $obGeoObject)
    {
        if(intval(self::$geoIblocks['city']) > 0){
            $city = trim($obGeoObject->getLocalityName());
            
            if(strlen($city) > 0 && Loader::includeModule('iblock')){
                $db = \CIBlockElement::GetList(
                    array(),
                    array(
                        "IBLOCK_ID" => self::$geoIblocks['city'], 
                        '=NAME' => $city
                    ),
                    false,
                    false,
                    array(
                        'ID'
                    )
                );
        
                if ($dbcity = $db->GetNext()){
                    return $dbcity['ID'];
                }else {
                    $el = new \CIBlockElement();
                    $cityId = $el->Add(array('IBLOCK_ID' => self::$geoIblocks['city'], 'NAME' => $city));

                    if($cityId){
                        return $cityId;
                    }
                }
            }
        }

        return null;
    }
}