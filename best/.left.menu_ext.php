<?php

global $APPLICATION;

$aMenuLinks = array_merge($aMenuLinks, $APPLICATION->IncludeComponent(
	"bitrix:menu.sections",
    "",
    array(
        "IBLOCK_TYPE" => "realty",
        "IBLOCK_ID" => CModule::IncludeModule("citrus.arealty") ? \Citrus\Arealty\Helper::getIblock('offers') : '',
        "DEPTH_LEVEL" => "2",
        "CACHE_TYPE" => "A",
        "CACHE_TIME" => "36000000",
    ),
    false,
    array('HIDE_ICONS' => 'Y')
));
