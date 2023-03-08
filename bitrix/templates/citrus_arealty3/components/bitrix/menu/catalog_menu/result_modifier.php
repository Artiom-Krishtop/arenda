<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
    die();

$newResult = array();
$newResult["ACTIVE_INDEX"] = 0;
$newResult["ITEMS"] = array();
foreach($arResult as $i => $arItem) {
    $newResult["ITEMS"][$i] = $arItem;
    if($arItem["SELECTED"]) $newResult["ACTIVE_INDEX"] = $i;
}
$arResult = $newResult;
