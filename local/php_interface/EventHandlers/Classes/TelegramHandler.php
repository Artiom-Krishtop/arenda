<?php

namespace EventHandlers\Classes;

use ITG\Clients\TelegramClient;

class TelegramHandler
{
    protected static $propData;
    protected static $propEnumData;
    protected static $viewProp = array(
        'deal_type',
        'NEW_COMMERCIAL_TYPE',
        'ownership_type',
        'COUNTRY',
        'CITY',
        'CITY_AREA',
        'ADDRESS',
        'BUILDING',
        'NEW_FLOOR',
        'district',
        'metro_stations',
        'category',
        'rooms_html',
        'NEW_ROOMS_AREA',
        'rooms',
        'NEW_ROOMS_TYPE',
        'NEW_COMMERCIAL_FEATURES',
        'common_area',
        'phone_lines',
        'office_class',
        'garage_features',
        'DESCRIPTION',
        'cost',
        'price_for_meter',
        'cost_curr',
        'cost_period',
        'cost_unit'
    );

    public static function publicAdvInTelegram(&$arFields)
    {
        if($arFields['ACTIVE'] == 'Y'){
            self::$propData = self::getPropertyData($arFields['IBLOCK_ID']);
            self::$propEnumData = self::getPropertyEnumData($arFields['IBLOCK_ID']);

            $description = self::getDescription($arFields);
            $photo = self::getPhoto($arFields);

            $telegram = new TelegramClient();

            if(!empty($photo)){
                $telegram->sendPhoto($photo, $description);
            }else{
                $telegram->sendMessage($description);
            }
        }
    }

    protected function getPropertyData($iBlockID)
    {
        $propFields = array();

        $dbPropRes = \CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("ACTIVE"=>"Y", "IBLOCK_ID" => $iBlockID));
        
        while ($propRes= $dbPropRes->GetNext()){
            $propFields[$propRes['ID']] = $propRes;
        }

        return $propFields;
    }

    protected function getPropertyEnumData($iBlockID)
    {
        $propFields = array();

        $dbPropRes = \CIBlockPropertyEnum::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("IBLOCK_ID" => $iBlockID));
        
        while ($propRes= $dbPropRes->GetNext()){
            $propFields[$propRes['ID']] = $propRes;
        }

        return $propFields;
    }

    protected function getDescription($arFields)
    {
        $description = '';

        if(!empty($arFields['NAME'])){
            $description .= '<b>' . $arFields['NAME'] . '</b>' . "\n\n";
        }

        $description .= 'Характеристики помещения:' . "\n";

        foreach (self::$propData as $propID => $prop) {
            if(in_array($prop['CODE'], self::$viewProp) && !empty($arFields['PROPERTY_VALUES'][$propID])){
                if($prop['PROPERTY_TYPE'] == 'F'){
                    continue;
                }
                
                $value = null;

                if(in_array($prop['PROPERTY_TYPE'], array('S', 'N'))){
                    $value = self::getValueStringOrNumType($arFields['PROPERTY_VALUES'][$propID]);
                }

                if($prop['PROPERTY_TYPE'] == 'L' && empty($prop['USER_TYPE'])){
                    $value = self::getValueListType($arFields['PROPERTY_VALUES'][$propID]);
                }

                if($prop['PROPERTY_TYPE'] == 'E'){
                    $value = self::getValueEListType($arFields['PROPERTY_VALUES'][$propID]);
                }

                if(!empty($value)){
                    $description .= ' - <b>' . $prop['NAME'] . '</b> : ' . $value . "\n"; 
                }
            }
        }

        $detailPageUrl = self::getDetailPageUrl($arFields['ID']);

        if(!empty($detailPageUrl)){
            $description .= "\n" . 'Подробности <a href="' . $detailPageUrl . '">на сайте.</a>';   
        }

        return $description;
    }

    protected function getValueEListType($id)
    {
        $value = array();

        $arSort = array('SORT' => 'ASC', 'ID' => 'DESC');
        $arFilter = array('ID' => $id);
        $arSelect = array('NAME');

        $dbRes = \CIBlockElement::getList($arSort, $arFilter, false, false, $arSelect);
        
        while($res = $dbRes->Fetch()){
            $value[] = $res['NAME'];
        }
        
        return implode(', ', $value);
    }

    protected function getValueStringOrNumType($value)
    {
        $strReturn = '';

        if(is_array($value)){
            if(!empty($value['VALUE'])){
                $strReturn = $value['VALUE']['TEXT'];
            }else {
                $strReturn = implode(', ', $value);
            }
        }else {
            $strReturn = $value;
        }
        
        return $strReturn;
    }

    protected function getValueListType($value)
    {
        $strReturn = '';

        if(is_array($value)){
            $arrValue = array();

            foreach ($value as $enumValue) {
                $arrValue[] = self::$propEnumData[$enumValue]['VALUE'];
            }

            $strReturn = implode(', ', $arrValue);
        }else {
            $strReturn = self::$propEnumData[$value]['VALUE'];
        }

        return $strReturn;
    }

    protected function getDetailPageUrl($elementId)
    {
        $url = '';

        $dbRes = \CIBlockElement::GetByID($elementId);

        if($res = $dbRes->GetNext()){
            $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https://' : 'http://';
            $url = $protocol . $_SERVER['SERVER_NAME'] . $res['DETAIL_PAGE_URL'];
        }

        return $url;
    }

    protected function getPhoto($arFields)
    {
        $fotoData = array();

        foreach (self::$propData as $propID => $prop) {
            if($prop['CODE'] == 'photo' && !empty($arFields['PROPERTY_VALUES'][$propID])){
                $fotoData = $arFields['PROPERTY_VALUES'][$propID];

                break;
            }
        }

        return $fotoData;
    }
}