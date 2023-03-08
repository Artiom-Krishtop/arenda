<?php 

define('NEED_AUTH', true);

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

global $USER;

if($USER->IsAuthorized()){
	LocalRedirect('/account/');
}

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");