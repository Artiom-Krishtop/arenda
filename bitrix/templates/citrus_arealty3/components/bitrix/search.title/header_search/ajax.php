<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
    $ajaxResult = array(
        "QUERY" => $arResult["query"],
        "ITEMS" => array()
    );
    $ajaxResult["arResult"] = $arResult;

    foreach($arResult["SEARCH"] as $arItem) {
        $ajaxResult["ITEMS"][] = $arItem;
    }
    echo \Bitrix\Main\Web\Json::encode($ajaxResult);
?>