<?php

namespace EventHandlers;

use Bitrix\Iblock\ElementTable;
use CIBlockElement;
use ITG\Clients\TelegramClient;

class IblockElementHandler
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

    public function onStartIBlockElementAdd(&$arFields)
    {
        self::generateMnemonicCode($arFields);
    }

    public function onStartIBlockElementUpdate(&$arFields)
    {
        if(strlen($arFields['CODE']) == 0){
            self::generateMnemonicCode($arFields);
        }
    }

    public static function generateMnemonicCode(&$arFields)
    {
        if(strlen($arFields['NAME']) > 0 && intval($arFields['IBLOCK_ID']) > 0){
            $arIblock = \CIBlock::GetArrayByID($arFields['IBLOCK_ID']);

            if (!empty($arIblock)){
                $config = $arIblock['FIELDS']['CODE']['DEFAULT_VALUE'];

                $settings = [
                    'max_len' => $config['TRANS_LEN'],
                    'change_case' => $config['TRANS_CASE'],
                    'replace_space' => $config['TRANS_SPACE'],
                    'replace_other' => $config['TRANS_OTHER'],
                    'delete_repeat_replace' => ($config['TRANS_EAT'] == 'Y'),
                ];

                $code = \CUtil::translit($arFields['NAME'], 'ru', $settings);
                dd($arFields[$code]);
                if(self::isExistsMnemonicCode($code)){
                    $list = [];
                    $iterator = ElementTable::getList([
                        'select' => ['ID', 'CODE'],
                        'filter' => [
                            '=IBLOCK_ID' => $arIblock['ID'],
                            '%=CODE' => $code . '%',
                            '=WF_STATUS_ID' => 1,
                            '==WF_PARENT_ELEMENT_ID' => null,
                        ],
                    ]);

                    while ($row = $iterator->fetch()){
                        $list[$row['CODE']] = true;
                    }
    
                    if (isset($list[$code])){
                        $code .= '_';
                        $i = 1;

                        while (isset($list[$code . $i])){
                            $i++;
                        }
    
                        $code .= $i;
                    }
                }

                $arFields['CODE'] = $code;
            }
        }
    }

    public static function isExistsMnemonicCode($code)
    {
        $dbRes = ElementTable::query()
            ->setSelect(array('ID'))
            ->setFilter(array('=CODE' => $code))
            ->exec();

        if($dbRes->fetch()){
            return true;
        }

        return false;
    }

    public function onAfterIBlockElementAdd(&$arFields)
    {
        if($arFields['ID'] && $arFields['ACTIVE'] == 'Y'){
            self::$propData = self::getPropertyData($arFields['IBLOCK_ID']);
            self::$propEnumData = self::getPropertyEnumData($arFields['IBLOCK_ID']);

            $description = self::getDescription($arFields);
            $photo = self::getPhoto($arFields);

            $telegram = new TelegramClient();
            $telegram->sendPhoto($photo, $description);
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

                if(in_array($prop['PROPERTY_TYPE'], array('S', 'N')) && empty($prop['USER_TYPE'])){
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
            $strReturn = implode(', ', $value);
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