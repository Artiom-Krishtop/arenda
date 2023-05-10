<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

$request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();

if ($arParams['AJAX'] == "Y" && $request->get("FORM_ID") === $arResult["FORM_ID"])
{
	$APPLICATION->RestartBuffer();

	if ($arResult['MESSAGE'])
	{
		$respons = array(
			'success' => true,
			'message' => $arResult["MESSAGE"],
		);
	}

	if (count($arResult["ERRORS"]) > 0)
	{
		$respons = array(
			'success' => false,
			'message' => $arResult["ERRORS"],
		);
	}
	$respons["CAPTCHA_CODE"] = $arResult["CAPTCHA_CODE"];

	echo \Bitrix\Main\Web\Json::encode($respons);
	die();
}
