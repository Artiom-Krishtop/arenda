<?
if($arParams['JQUERY_VALID'] == "Y") {
	// jQuery плагин для валидации
	$APPLICATION->AddHeadScript($this->__template->__folder . "/js/jquery.validate/jquery.validate.js");
	// языковые файлы для валидации
	$APPLICATION->AddHeadScript($this->__template->__folder . "/js/jquery.validate/lang/" . LANGUAGE_ID . "/message.js");
}

if (isset($_REQUEST['ajax'])) {
	// добавляет скрипт защиты от ботов
	$APPLICATION->ShowHeadStrings();
}

if($arParams['AJAX'] == "Y" && $_REQUEST['ajax_param']) {
	die();
}
?>