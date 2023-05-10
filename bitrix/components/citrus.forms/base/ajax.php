<?php
/** @global CUser $USER */

use Bitrix\Main\Entity\EntityError;
use Bitrix\Main\Entity\Result;
use Bitrix\Main\SystemException;
use \Bitrix\Main\Localization\Loc;

/** @global CMain $APPLICATION */
define('STOP_STATISTICS', true);
define('NO_AGENT_CHECK', true);
define('PUBLIC_AJAX_MODE', true);

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

if (!\Bitrix\Main\Loader::includeModule('citrus.forms'))
{
	return;
}

$result = new Result();
$request = \Bitrix\Main\HttpApplication::getInstance()->getContext()->getRequest();
$request->addFilter(new \Bitrix\Main\Web\PostDecodeFilter);

Loc::loadMessages(__FILE__);

try
{
	CUtil::JSPostUnescape();

	if (!check_bitrix_sessid() && !$request->isPost())
	{
		throw new SystemException(Loc::getMessage("CP_SMARTFORM_AJAX_REQUEST_ERROR"));
	}

	$componentName = $request->get('component');

	$beforeClasses = get_declared_classes();
	CBitrixComponent::includeComponentClass($componentName);
	$afterClasses = get_declared_classes();

	$arIncludeClass = array_diff($afterClasses, $beforeClasses);
	if (empty($arIncludeClass))
	{
		throw new SystemException(Loc::getMessage("CP_SMARTFORM_AJAX_FAILED_TO_FIND_COMPONENT"));
	}

	$arIncludeClass = array_reverse(array_values($arIncludeClass));
	$className = $arIncludeClass[0];

	if (!is_subclass_of($className, '\Citrus\Forms\BaseComponent'))
	{
		throw new SystemException(Loc::getMessage("CP_SMARTFORM_AJAX_FAILED_TO_FIND_COMPONENT"));
	}

	/** @var \Citrus\Forms\BaseComponent $form */
	$form = new $className();
	$form->initComponent($componentName);

	$FORM_ID = $request->get('FORM_ID');
	$arParams = $form->loadComponentParams($FORM_ID);
	if (!is_array($arParams))
	{
		throw new SystemException(Loc::getMessage("CP_SMARTFORM_AJAX_E_LOAD_PARAMS"));
	}

	$form->arParams = $form->onPrepareComponentParams($arParams);
	$form->setAjaxMode();

	$form->executeComponent();

	$result->setData($form->arResult);

	if (isset($form->arResult['ERRORS']))
	{
		foreach ($form->arResult['ERRORS'] as $code => $mess)
		{
			$result->addError(new EntityError($mess, $code));
		}
	}
}
catch (SystemException $ex)
{
	$result->addError(new EntityError($ex->getMessage()));
}

if ($result->isSuccess())
{
	\Citrus\Forms\jsonResponse(array(
		'fields' => $result->getData(),
		'message' => strlen($form->arParams['SUCCESS_TEXT']) > 0 ? $form->arParams['SUCCESS_TEXT'] : Loc::getMessage("CP_SMARTFORM_AJAX_E_RESPONSE"),
	));
}
else
{
	\Citrus\Forms\jsonResponse(array(
		'fields' => $result->getData(),
		'message' => $result->getErrorMessages(),
	), 502);
}

require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_after.php');

