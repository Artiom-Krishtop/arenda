<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/local/vendor/autoload.php'; // change path as needed

use Bitrix\Main\EventManager;
use Bitrix\Main\Loader;
use Bitrix\Main\Web\HttpClient;
use ITG\Clients\InstagramClient;

require_once 'functions.php';
require_once 'defines.php';

/* Автозагрузчики классов */
Loader::registerAutoLoadClasses(null,array(
    '\ITG\Custom\Favourites' => '/local/php_interface/Classes/Favourites.php',
    '\EventHandlers\RegistrationEventHandler' => '/local/php_interface/EventHandlers/RegistrationEventHandler.php',
    '\EventHandlers\UserAuthEventHandler' => '/local/php_interface/EventHandlers/UserAuthEventHandler.php',
    '\EventHandlers\UserModeration' => '/local/php_interface/EventHandlers/UserModeration.php',
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

// if(isset($_REQUEST['test'])){
//     $httpClient = new HttpClient();
//     // $httpClient->setHeader('User-Agent', 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/107.0.5304.88 Safari/537.36');
//     $fb = new \Facebook\Facebook([
//         'app_id' => '503725704980245',
//         'app_secret' => '9208ae75723a9fbe8c481aa62fe27cbe',
//         'default_graph_version' => 'v15.0',
//         // 'default_access_token' => 'EAAHKIse1pxUBABScKZApvvBFW2eje982h5xgF7ZBzebwSh27ts1CBygYqeeG4B3v7KZAuMlJZCwjpoiakCcFKGgSlfmZA6LjDBL2zMky89Y173ktuZBF4iPNKPZCG8Rt3xM8G7Up74u5jKfyOwAXVK1jwwZCDVnmmqGESc7BnRzKQ6mEeVsSCB3eLz83gsyaG5piY7dBiTF4E7acZAv54uMZB8h8YGhsYZAluCSj17lKPLGWhXoInG628hxhsa8SlhOrzsZD'
//     ]);


//     $helper = $fb->getRedirectLoginHelper();

//     $permissions = ['email']; // Optional permissions
//     $loginUrl = $helper->getLoginUrl('https://arenda-pom.itg-soft.by/?test=test', $permissions);

//     dd($httpClient->get($loginUrl));
//     try {
//          $accessToken = $helper->getAccessToken();
//     } catch(Facebook\Exception\ResponseException $e) {
//         // When Graph returns an error
//         echo 'Graph returned an error: ' . $e->getMessage();
//         exit;
//     } catch(Facebook\Exception\SDKException $e) {
//         // When validation fails or other local issues
//         echo 'Facebook SDK returned an error: ' . $e->getMessage();
//         exit;
//     }
    
//     dd($accessToken);
// }









