<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

if(!empty($arResult['ITEMS'])){
    foreach ($arResult['ITEMS'] as &$arItem) {
        $arItem["APPLICATION_IMAGES"] = array();

        if(isset($arItem['PROPERTIES'])){
            if(!empty($arItem['PROPERTIES']['photo']['VALUE'])){
                if(!is_array($arItem['PROPERTIES']['photo']['VALUE'])){
                    $arItem['PROPERTIES']['photo']['VALUE'] = array($arItem['PROPERTIES']['photo']['VALUE']);
                }

                $arItem['APPLICATION_IMAGES'] = array_merge($arItem['APPLICATION_IMAGES'], $arItem['PROPERTIES']['photo']['VALUE']);
                unset($arItem['PROPERTIES']['photo']);
            }

            if(!empty($arItem['PROPERTIES']['PANORAMIC_PHOTOS']['VALUE'])){
                if(!is_array($arItem['PROPERTIES']['PANORAMIC_PHOTOS']['VALUE'])){
                    $arItem['PROPERTIES']['PANORAMIC_PHOTOS']['VALUE'] = array($arItem['PROPERTIES']['PANORAMIC_PHOTOS']['VALUE']);
                }
                
                $arItem['APPLICATION_IMAGES'] = array_merge($arItem['APPLICATION_IMAGES'], $arItem['PROPERTIES']['PANORAMIC_PHOTOS']['VALUE']);
                unset($arItem['PROPERTIES']['PANORAMIC_PHOTOS']);
            }

            foreach ($arItem['PROPERTIES'] as $key => $prop) {
                if(!empty($prop['VALUE'])){
                    if($prop['PROPERTY_TYPE'] == 'F'){
                        continue;
                    }
    
                    if(in_array($prop['PROPERTY_TYPE'], array('S', 'N'))){
                        if(is_array($arItem['PROPERTIES'][$key]['VALUE'])){
                            if(!empty($arItem['PROPERTIES'][$key]['VALUE'])){
                                $arItem['PROPERTIES'][$key]['VALUE'] = $arItem['PROPERTIES'][$key]['VALUE']['TEXT'];
                            }else {
                                $arItem['PROPERTIES'][$key]['VALUE'] = implode(', ', $arItem['PROPERTIES'][$key]['VALUE']);
                            }
                        }
                    }
    
                    if($prop['PROPERTY_TYPE'] == 'L' && empty($prop['USER_TYPE'])){
                        if(is_array($arItem['PROPERTIES'][$key]['VALUE'])){
                            $arItem['PROPERTIES'][$key]['VALUE'] = implode(', ', $arItem['PROPERTIES'][$key]['VALUE']);
                        }
                    }
    
                    if($prop['PROPERTY_TYPE'] == 'E'){
                        $dbRes = CIBlockElement::GetByID($arItem['PROPERTIES'][$key]['VALUE']);

                        if($res = $dbRes->Fetch()){
                            $arItem['PROPERTIES'][$key]['VALUE'] = $res['NAME'];
                        }else {
                            unset($arItem['PROPERTIES'][$key]);
                        }
                    }
                }else {
                    unset($arItem['PROPERTIES'][$key]);
                }
            }
        }
    }

    unset($arItem);
}