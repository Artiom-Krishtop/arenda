<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use \Bitrix\Main\Page\Asset;

/** @var array $templateData */
/** @var @global CMain $APPLICATION */

CJSCore::Init(array('jquery', 'cui_form'));

//подключение стилей и скриптов шаблонов
foreach ($arResult["ADDITIONAL_SCRIPTS"] as $link) {
	Asset::getInstance()->addJs($templateFolder.'/'.$link);
}
foreach ($arResult["ADDITIONAL_STYLES"] as $link) {
	Asset::getInstance()->addCss($templateFolder.'/'.$link);
}