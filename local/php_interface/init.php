<?php

require_once 'vendor/autoload.php';

use Bitrix\Main\EventManager;
use Bitrix\Main\Loader;

require_once 'functions.php';
require_once 'defines.php';

/* Автозагрузчики классов */
Loader::registerAutoLoadClasses(null,array(
    '\ITG\Custom\Favourites' => '/local/php_interface/Classes/Favourites.php',
    '\ITG\Custom\GeoProperty' => '/local/php_interface/Classes/GeoProperty.php',
    '\EventHandlers\RegistrationEventHandler' => '/local/php_interface/EventHandlers/RegistrationEventHandler.php',
    '\EventHandlers\UserAuthEventHandler' => '/local/php_interface/EventHandlers/UserAuthEventHandler.php',
    '\EventHandlers\UserModeration' => '/local/php_interface/EventHandlers/UserModeration.php',
    '\EventHandlers\Classes\GeoDataHandler' => '/local/php_interface/EventHandlers/Classes/GeoDataHandler.php',
    '\EventHandlers\Classes\TelegramHandler' => '/local/php_interface/EventHandlers/Classes/TelegramHandler.php',
    '\EventHandlers\Classes\MnenonicCodeHandler' => '/local/php_interface/EventHandlers/Classes/MnenonicCodeHandler.php',
    '\EventHandlers\IblockElementHandler' => '/local/php_interface/EventHandlers/IblockElementHandler.php',
    'ITG\Clients\TelegramClient' => '/local/php_interface/Clients/TelegramClient.php',
    'ITG\Clients\InstagramClient' => '/local/php_interface/Clients/InstagramClient.php',
));

$handler = EventManager::getInstance();

$handler->addEventHandler("main", "OnBeforeUserRegister", array("EventHandlers\\RegistrationEventHandler", "onBeforeUserRegister"));
$handler->addEventHandler("main", "OnAfterUserRegister", array("EventHandlers\\RegistrationEventHandler", "onAfterUserRegister"));
$handler->addEventHandler("main", "OnAfterUserLogin", array("EventHandlers\\UserAuthEventHandler", "onAfterUserLogin"));
$handler->addEventHandler("main", "OnBeforeUserUpdate", array("EventHandlers\\UserModeration", "onBeforeUserUpdate"));
$handler->addEventHandler("main", "OnUserDelete", array("EventHandlers\\UserModeration", "onUserDelete"));
$handler->addEventHandler("iblock", "OnAfterIBlockElementAdd", array("EventHandlers\\IblockElementHandler", "onAfterIBlockElementAdd"));
$handler->addEventHandler("iblock", "OnAfterIBlockElementUpdate", array("EventHandlers\\IblockElementHandler", "onAfterIBlockElementUpdate"));
$handler->addEventHandler("iblock", "OnStartIBlockElementAdd", array("EventHandlers\\IblockElementHandler", "onStartIBlockElementAdd"));
$handler->addEventHandler("iblock", "OnStartIBlockElementUpdate", array("EventHandlers\\IblockElementHandler", "onStartIBlockElementUpdate"));











