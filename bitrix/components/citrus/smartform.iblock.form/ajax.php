<?php
/** @global CUser $USER */
use Bitrix\Main\Entity\EntityError;
use Bitrix\Main\Entity\Result;
use Bitrix\Main\SystemException;
use Bitrix\Main\Web\Json;
use \Bitrix\Main\Localization\Loc;

/** @global CMain $APPLICATION */
define('STOP_STATISTICS', true);
define('NO_AGENT_CHECK', true);
define('PUBLIC_AJAX_MODE', true);

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

$result = new Result();
$request = \Bitrix\Main\HttpApplication::getInstance()->getContext()->getRequest();
$request->addFilter(new \Bitrix\Main\Web\PostDecodeFilter);

Loc::loadMessages(__FILE__);

try {
	CUtil::JSPostUnescape();

	if (!check_bitrix_sessid() && !$request->isPost())
		throw new SystemException(Loc::getMessage("CP_IBADDFORM_AJAX_REQUEST_ERROR"));

	$componentName = $request->get('component');

	CBitrixComponent::includeComponentClass($componentName);

	$form = new CBCitrusIBAddFormComponent();
	$form->initComponent($componentName);
	$FORM_ID = $request->get('FORM_ID');

	$arParams = $form->loadComponentParams($FORM_ID);
	if(!is_array($arParams))
		throw new SystemException(Loc::getMessage("CP_IBADDFORM_AJAX_E_LOAD_PARAMS"));

	$form->arParams = $form->onPrepareComponentParams($arParams);

	$form->executeComponent(true);

	$result->setData($form->arResult);

	if(isset($form->arResult['ERRORS'])) {
		foreach($form->arResult['ERRORS'] as $code => $mess) {
			$result->addError(new EntityError($mess,$code));
		}
	}
}
catch(SystemException $ex) {
	$result->addError(new EntityError($ex->getMessage()));
}

if($result->isSuccess()) {
	echo Json::encode(array(
		'fields' => $result->getData(),
		'message' => strlen($form->arParams['SUCCESS_TEXT']) > 0 ? $form->arParams['SUCCESS_TEXT'] : Loc::getMessage("CP_IBADDFORM_AJAX_E_RESPONSE")
	));
}
else {
	\CHTTP::SetStatus(502);
	echo Json::encode(array(
		'fields' => $result->getData(),
		'message' => $result->getErrorMessages()
	));
}

require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_after.php');

