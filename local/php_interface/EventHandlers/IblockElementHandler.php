<?php

namespace EventHandlers;

use Bitrix\Main\Loader;
use EventHandlers\Classes\GeoDataHandler;
use EventHandlers\Classes\MnenonicCodeHandler;
use EventHandlers\Classes\TelegramHandler;

class IblockElementHandler
{
    public function onStartIBlockElementAdd(&$arFields)
    {
        MnenonicCodeHandler::generateMnemonicCode($arFields);
    }

    public function onStartIBlockElementUpdate(&$arFields)
    {
        if(strlen($arFields['CODE']) == 0){
            MnenonicCodeHandler::generateMnemonicCode($arFields);
        }
    }

    public function onAfterIBlockElementAdd(&$arFields)
    {
        if(self::getIblockId('realty', 'offers') == $arFields['IBLOCK_ID'] && $arFields['ID']){
            GeoDataHandler::addGeoData($arFields);
            TelegramHandler::publicAdvInTelegram($arFields);
        }
    }

    public function onAfterIBlockElementUpdate(&$arFields)
    {
        if(self::getIblockId('realty', 'offers') == $arFields['IBLOCK_ID'] && $arFields['ID']){
            GeoDataHandler::addGeoData($arFields);
        }
    }

    protected static function getIblockId($type, $code)
    {
        if(Loader::includeModule('iblock')){
            $dbRes = \CIBlock::GetList(
                array(),
                array(
                    'TYPE' => $type,
                    'CODE' => $code
                )
            );

            if($res = $dbRes->Fetch()){
                return $res['ID'];
            }
        }
        
        return null;
    }
}