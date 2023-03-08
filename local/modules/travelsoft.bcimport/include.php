<?php
$classes = [
    "travelsoft\\Tools" => "lib/Tools.php",
    "travelsoft\\Gateway" => "lib/Gateway.php",
    "travelsoft\\Fields" => "lib/Fields.php",
    "travelsoft\\Utils" => "lib/Utils.php",
    "travelsoft\\Settings" => "lib/Settings.php",


    "travelsoft\\adapters\\Highloadblock" => "lib/adapters/Highloadblock.php",
    "travelsoft\\adapters\\Iblock" => "lib/adapters/Iblock.php",
    "travelsoft\\adapters\\Store" => "lib/adapters/Store.php",
    "travelsoft\\adapters\\Cache" => "lib/adapters/Cache.php",


    "travelsoft\\stores\\City" => "lib/stores/City.php",
    "travelsoft\\stores\\Offer" => "lib/stores/Offer.php",
    "travelsoft\\stores\\Building" => "lib/stores/Building.php",
    "travelsoft\\stores\\Region" => "lib/stores/Region.php",
    "travelsoft\\stores\\Country" => "lib/stores/Country.php",
];
CModule::AddAutoloadClasses("travelsoft.bcimport", $classes);
