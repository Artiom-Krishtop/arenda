<?php

use Bitrix\Main\Loader;

define('STOP_STATISTICS', true); // отключаем модуль веб-аналитики
define("NO_KEEP_STATISTIC", true);  // отключаем модуль веб-аналитики
define("NO_AGENT_CHECK", true); // отключаем обработку агентов
define("DisableEventsCheck", true); // запрет на отправку неотправленных почтовых сообщений из БД https://dev.1c-bitrix.ru/api_help/main/general/mailevents.php
define("BX_COMPRESSION_DISABLED", true);

require $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php";
$APPLICATION->SetTitle("Отправить презентацию");

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

if (isset($_REQUEST['params'])){
	$arParams = \Citrus\Core\Components\Pdf::decodeParams($_REQUEST['params']);

	if (isset($_REQUEST['currency'])) {
		$arParams['CURRENCY'] = htmlspecialcharsEx($_REQUEST['currency']);
	}
	$arParams = \Citrus\Core\Components\Pdf::encodeParams($arParams);
}


?><?$APPLICATION->IncludeComponent(
	"citrus.core:include",
	"popup_wrapper",
	array(
		"AREA_FILE_SHOW" => 'component',
		"_COMPONENT" => "citrus.forms:iblock.element",
		"_COMPONENT_TEMPLATE" => "simple",
		"MODAL_CONTAINER_CLASS" => "modal-pdf",

		"AFTER_FORM_TOOLTIP" => "",
		"AJAX" => "Y",
		"ANCHOR_ID" => "",
		"BEFORE_FORM_TOOLTIP" => "",
		"BUTTON_POSITION" => "LEFT",
		"BUTTON_TITLE" => "",
		"COMPONENT_TEMPLATE" => "simple",
		"EDIT_ELEMENT" => "N",
		"ELEMENT_ID" => "",
		"ERROR_TEXT" => "",
		"FIELDS" => array(
			"NAME" => array(
				"ORIGINAL_TITLE" => "Название",
				"TITLE" => "Email",
				"IS_REQUIRED" => "Y",
				"HIDE_FIELD" => "N",
				"TEMPLATE_ID" => "text",
				"VALIDRULE" => "email",
			),
			"PDF_PARAMS" => array(
				"TITLE" => "Данные",
				"IS_REQUIRED" => "N",
				"HIDE_FIELD" => "Y",
				"ORIGINAL_TITLE" => "Данные",
				"TEMPLATE_ID" => "text",
				"DEFAULT" => $arParams,
			),
		),
		"FORM_ID" => "pdfform",
		"FORM_STYLE" => "WHITE",
		"FORM_TITLE" => "",
		"IBLOCK_ID" => "",
		"IBLOCK_CODE" => "arealty_pdf",
		"IBLOCK_TYPE" => "feedback",
		"JQUERY_VALID" => "Y",
		"MAILE_EVENT_TEMPLATE" => array(
		),
		"MAIL_EVENT" => "CITRUS_REALTY_PDF_SEND",
		"NOT_CREATE_ELEMENT" => "N",
		"PARENT_SECTION" => "",
		"PARENT_SECTION_CODE" => "",
		"REDIRECT_AFTER_SUCCESS" => "N",
		"SAVE_SESSION" => "Y",
		"SEND_IMMEDIATE" => "N",
		"SEND_MESSAGE" => "Y",
		"SUB_TEXT" => "",
		"SUCCESS_TEXT" => "Презентация успешно отправлена!",
		"USER_SERVER_VALIDATE" => "N",
		"USE_SERVER_VALIDATE" => "N",
		"AGREEMENT_LINK" => "/agreement/",
		"USE_GOOGLE_RECAPTCHA" => "N",
		"HIDDEN_ANTI_SPAM" => "Y",
		"EMAIL_TO_VALUE" => "",
		"EMAIL_SUBJECT" => "",
		"FORM_CLASS" => "",
		"FORM_MOD" => "INLINE",
		"BUTTON_CLASS" => "",
		"AGREEMENT_FIELDS" => "Имя, Email, Телефон",
		"USER_CONSENT" => "N",
		"USER_CONSENT_ID" => "0",
		"USER_CONSENT_IS_CHECKED" => "Y",
		"USER_CONSENT_IS_LOADED" => "N"
	),
	false,
	['HIDE_ICONS' => 'Y']
);?><?php

//require $_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php";
