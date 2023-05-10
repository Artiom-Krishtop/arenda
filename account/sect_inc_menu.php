<?php

$APPLICATION->IncludeComponent(
    "bitrix:menu",
    "personal-menu",
    Array(
        "ROOT_MENU_TYPE" => "account", 
        "MAX_LEVEL" => "1", 
        "CHILD_MENU_TYPE" => "account", 
        "USE_EXT" => "N",
        "DELAY" => "N",
        "ALLOW_MULTI_SELECT" => "N",
        "MENU_CACHE_TYPE" => "N", 
        "MENU_CACHE_TIME" => "3600", 
        "MENU_CACHE_USE_GROUPS" => "Y", 
        "MENU_CACHE_GET_VARS" => "" 
    )
);