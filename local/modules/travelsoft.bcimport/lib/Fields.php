<?php

namespace travelsoft;

use \Bitrix\Main\Config\Option;

class Fields
{
    private static function getCodeField($codeField)
    {
        return Option::get("travelsoft.bcimport", $codeField);
    }

    /**
     * @return string
     */
    public static function tourTypeStoreId()
    {
        return self::getCodeField('TOUR_TYPE_STORE_ID');
    }

    public static function getApiUrl()
    {
        return self::getCodeField('API_URL');
    }


    public static function cityStoreId()
    {
        return self::getCodeField('CITY_STORE_ID');
    }

    public static function buildingStoreId(){
        return '22';
    }

    public static function countryStoreId(){
        return '23';
    }

    public static function offerStoreId(){
        return '13';
    }

    public static function regionStoreId(){
        return '24';
    }
}
