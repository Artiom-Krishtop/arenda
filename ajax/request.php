<?php

use Bitrix\Main\Loader;

define('STOP_STATISTICS', true); // отключаем модуль веб-аналитики
define("NO_KEEP_STATISTIC", true);  // отключаем модуль веб-аналитики
define("NO_AGENT_CHECK", true); // отключаем обработку агентов
define("DisableEventsCheck", true); // запрет на отправку неотправленных почтовых сообщений из БД https://dev.1c-bitrix.ru/api_help/main/general/mailevents.php
define("BX_COMPRESSION_DISABLED", true);

require $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php";
$APPLICATION->SetTitle("Отправить заявку");

$isAjax = \Bitrix\Main\Context::getCurrent()->getRequest()->isAjaxRequest();
if ($isAjax)
{
	define("PUBLIC_AJAX_MODE", true); // запрет на вывод отладочной информации, включаемой параметрами запроса и меню Отладка
	Citrus\Arealty\Helper::initTemplatePlugins();
}
else
{
	require $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_after.php";
}

if (!Loader::includeModule("citrus.arealty"))
{
	return;
}

?><?$APPLICATION->IncludeComponent(
	"citrus.core:include",
	"popup_wrapper",
	array(
		"AREA_FILE_SHOW" => 'component',
		"_COMPONENT" => "citrus.forms:iblock.element",
		"_COMPONENT_TEMPLATE" => "simple",

		"AFTER_FORM_TOOLTIP" => "",
		"AJAX" => "Y",
		"ANCHOR_ID" => "",
		"BEFORE_FORM_TOOLTIP" => "",
		"BUTTON_POSITION" => "CENTER",
		"BUTTON_TITLE" => "",
		"COMPONENT_TEMPLATE" => ".default",
		"EDIT_ELEMENT" => "N",
		"ELEMENT_ID" => "",
		"ERROR_TEXT" => "",
		"FIELDS" => array(
			"PROPERTY_href" => array(
				"TITLE" => "Отправлено со страницы",
				"IS_REQUIRED" => "N",
				"HIDE_FIELD" => "Y",
				"ORIGINAL_TITLE" => "[54] Отправлено со страницы",
				"DEFAULT" => Citrus\Arealty\Helper::getPath(),
			),
			"NAME" => array(
				"ORIGINAL_TITLE" => "Название",
				"TITLE" => "Введите имя",
				"IS_REQUIRED" => "Y",
				"HIDE_FIELD" => "N",
			),
			"PROPERTY_phone" => array(
				"TITLE" => "Телефон",
				"IS_REQUIRED" => "Y",
				"HIDE_FIELD" => "N",
				"ORIGINAL_TITLE" => "[55] Телефон",
				"TEMPLATE_ID" => "phone",
			),
			"PREVIEW_TEXT" => array(
				"ORIGINAL_TITLE" => "Описание для анонса",
				"TITLE" => "Дополнительная информация",
				"IS_REQUIRED" => "Y",
				"HIDE_FIELD" => "N",
				"DESCRIPTION" => "",
			),
		),
		"FORM_ID" => "qwe123",
		"FORM_STYLE" => "WHITE",
		"FORM_TITLE" => "",
		"IBLOCK_ID" => "",
		"IBLOCK_CODE" => "requests",
		"IBLOCK_TYPE" => "feedback",
		"JQUERY_VALID" => "Y",
		"MAILE_EVENT_TEMPLATE" => array(
		),
		"MAIL_EVENT" => "CITRUS_REALTY_NEW_REQUEST",
		"NOT_CREATE_ELEMENT" => "N",
		"PARENT_SECTION" => "",
		"PARENT_SECTION_CODE" => "voprosy-posetiteley",
		"REDIRECT_AFTER_SUCCESS" => "N",
		"SAVE_SESSION" => "Y",
		"SEND_IMMEDIATE" => "N",
		"SEND_MESSAGE" => "Y",
		"SUB_TEXT" => "",
		"SUCCESS_TEXT" => "Спасибо! Вам перезвонят в ближайшее время.",
		"USER_SERVER_VALIDATE" => "N",
		"USE_SERVER_VALIDATE" => "N",
		"AGREEMENT_LINK" => "/agreement/",
		"USE_GOOGLE_RECAPTCHA" => "N",
		"HIDDEN_ANTI_SPAM" => "Y"
	),
	false,
	$isAjax ? ['HIDE_ICONS' => 'Y'] : []
);?><?php

//require $_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php";
