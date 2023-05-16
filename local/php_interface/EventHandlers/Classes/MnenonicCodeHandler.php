<?php

namespace EventHandlers\Classes;

use Bitrix\Iblock\ElementTable;

class MnenonicCodeHandler
{    
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
}
