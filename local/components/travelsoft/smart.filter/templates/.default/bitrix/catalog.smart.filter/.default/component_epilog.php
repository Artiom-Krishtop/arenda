<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use \Bitrix\Main\Page\Asset;

/** @var array $templateData */
/** @var @global CMain $APPLICATION */

CJSCore::Init(array('jquery', 'cui_form', 'ajax'));

//����������� ������ � �������� ��������
foreach ($arResult["ADDITIONAL_SCRIPTS"] as $link) {
	Asset::getInstance()->addJs($templateFolder.'/'.$link);
}
foreach ($arResult["ADDITIONAL_STYLES"] as $link) {
	Asset::getInstance()->addCss($templateFolder.'/'.$link);
}

/*if(isset($_REQUEST["arrFilter_63_MIN"]) && isset($_REQUEST["arrFilter_63_MAX"]) && $_REQUEST["arrFilter_63_MIN"] > 0 && $_REQUEST["arrFilter_63_MAX"] > 0){
    $GLOBALS[$arParams["FILTER_NAME"]]["><PROPERTY_63"] = array($_REQUEST["arrFilter_63_MIN"],$_REQUEST["arrFilter_63_MAX"]);
}
foreach ($arResult["DEFAULT_ITEMS"][35]["VALUES"] as $key=>$item){
    if(isset($_REQUEST[$item["CONTROL_ID"]])){
        $GLOBALS[$arParams["FILTER_NAME"]]["PROPERTY_35"][] = $key;
    }
}
foreach ($arResult["DEFAULT_ITEMS"][97]["VALUES"] as $key=>$item) {
    if (isset($_REQUEST[$item["CONTROL_ID"]])) {
        $GLOBALS[$arParams["FILTER_NAME"]]["PROPERTY_97"][] = $key;
    }
}*/