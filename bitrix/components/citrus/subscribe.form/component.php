<?php

/**
 * based on http://marketplace.1c-bitrix.ru/solutions/asd.subscribequick/
 *
 * @var $USER CUser
 */

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

$arResult = array();
$arParams['JS_KEY'] = md5(LICENSE_KEY);
$arParams['USER_EMAIL'] = $USER->GetEmail();
$arParams['NO_CONFIRMATION'] = !isset($arParams['NO_CONFIRMATION']) || $arParams['NO_CONFIRMATION'] != 'Y' ? 'N' : 'Y';
$arParams['FORMAT'] = isset($arParams['FORMAT']) ? $arParams['FORMAT'] : 'text';

if ($arParams['INC_JQUERY'] == 'Y')
{
	CUtil::InitJSCore(array('jquery'));
}

$arResult['ACTION'] = require __DIR__ . '/action.php';

$this->IncludeComponentTemplate();