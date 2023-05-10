<?php

namespace Citrus\Forms;

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ArgumentException;

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

Loc::loadMessages(__FILE__);

$parentComponentName = "citrus.forms:base";
\CBitrixComponent::includeComponentClass($parentComponentName);

class UserRegistrationComponent extends BaseComponent {
	public static $parentComponentName = "citrus.forms:base";

	var $eventPostfix = "UserRegForm";
	var $allowRegistration = false;

	public function onPrepareComponentParams($arParams) {
		$arParams = parent::onPrepareComponentParams($arParams);

		// проверим глобальные настройки по авторизации
		$this->allowRegistration = \COption::GetOptionString("main", "new_user_registration", "Y") == "Y" ? true : false;

		if(is_array($arParams['ADD_GROUP']) && !empty($arParams['ADD_GROUP']))
			$this->addGroup = $arParams['ADD_GROUP'];

		return $arParams;
	}

	public function executeComponent($isAjax = null) {
		global $APPLICATION, $USER;
		
		/**
		 * если пользователь не авторизован то он ен может изменять свои личные данные
		 */
//		if(true === $USER->IsAuthorized()) {
//			ShowError(Loc::getMessage('CP_UREGFORM_E_USER_NOT_AUTHORIZED'));
//			return false;
//		}

		if(false === $this->allowRegistration && $this->arParams['USE_MAIN_SETTINGS'] == "Y") {
			ShowError(Loc::getMessage('CP_UREGFORM_E_USER_NOT_REGISTR'));
			return false;
		}

		if(
			$this->arParams['USE_MAIN_SETTINGS'] == "Y"
			&& \COption::GetOptionString("main", "new_user_registration_email_confirmation", "Y") == "Y"
		) {
			$needConfirmEmail = true;
			$this->arParams['ACTIVE'] = "N";
		}
		else {
			$needConfirmEmail = false;
			$this->arParams['ACTIVE'] = "Y";
		}

		$this->setFormID($this->arParams['FORM_ID']);

		if('Y' == $this->arParams['SAVE_SESSION'])
			$this->saveComponentParams($this->getFormID());
		
		$this->arResult = array(
			"FORM_ID" => $this->getFormID(),
			"FORM_ACTIONS" => $this->arParams['FORM_ACTIONS'],
			"ITEMS" => $this->arParams['FIELDS'],
			"COMPONENT_NAME" => $this->getName()
		);
		
		$this->addMissingField();
		
		$context = \Bitrix\Main\HttpApplication::getInstance()->getContext();
		$request = $context->getRequest();
		if(
			check_bitrix_sessid()
			&& $request->get('FORM_ID') == $this->getFormID()
		) {
			try {
				/**
				 * валидирование полей перед обновлением
				 */
				$arField = $this->ValidateFormField();
				if(false === $arField)
					throw new ArgumentException(Loc::getMessage('CP_UREGFORM_E_VALIDATE_ERROR'),'VALIDATE_ERROR');

				/**
				 * Зарегистрируем пользователя на сайте
				 */
				$ID = $this->executeComponentTask($arField);
				if(false === $ID)
					throw new ArgumentException(Loc::getMessage('CP_UREGFORM_E_UPDATE_ERROR'),'UPDATE_ERROR');

				if($this->arParams['USE_MAIN_SETTINGS'] == "N") {

					$arField['ID'] = $ID;
					if($this->arParams['SEND_MESSAGE'] == "Y")
						$this->SendSuccessFormMessage($arField);

				}
				else {
					/**
					 * Если требуется проверить учетную запись то отправим сообщение пользователю с инстпукцией как
					 * это сделать
					 */
					if(true === $needConfirmEmail) {
						$arField['USER_ID'] = $ID;
						$event = new CEvent;
						$event->SendImmediate("NEW_USER_CONFIRM", SITE_ID, $arField);
					}
				}

				$events = GetModuleEvents("main", "OnBeforeUserRegisterFormSuccess");
				while($arEvent = $events->Fetch()) {
					if(ExecuteModuleEventEx($arEvent, array(&$arField)) === false) {
						if($err = $APPLICATION->GetException()) {
							throw new ArgumentException($err->GetString(),'BEFORE_USER_REGISTER_SUCCESS_EVENT');
						}
					}
				}

				$this->cleanOldValue();

				$_SESSION['FORM_RESULT'][$this->getFormID()] = array('ID' => $ID);

				if($this->arParams['REDIRECT_AFTER_SUCCESS'] == "Y")
					LocalRedirect($APPLICATION->GetCurPageParam("saccess=Y&id=" . $ID,array("saccess","id")));
			}
			catch(ArgumentException $ex) {
				if(strlen($ex->getParameter()) > 0)
					$this->arResult['ERROR_TITLE'][$ex->getParameter()] = $ex->getMessage();
				else
					$this->arResult['ERROR_TITLE'] = $ex->getMessage();
				$this->arResult['ERRORS'] = $this->LAST_ERROR;
			}
		}
		else {
			/**
			 * получим первоначальные данные пользователя
			 */
			$this->GetCurUserFieldValue($this->arParams['FIELDS'],$ID);
		}
		
		if(array_key_exists("CAPTCHA", $this->arParams['FIELDS']))
			$this->arResult["CAPTCHA_CODE"] = htmlspecialcharsbx($APPLICATION->CaptchaGetCode());
		
		if(isset($_SESSION['FORM_RESULT'][$this->getFormID()])) {
			$this->arResult['SUCCESS_RESULT'] = $_SESSION['FORM_RESULT'][$this->getFormID()];
			unset($_SESSION['FORM_RESULT'][$this->getFormID()]);
		}

		if(null === $isAjax)
			$this->includeComponentTemplate();
	}

	public function executeComponentTask(&$arField) {
		global $DB,$USER,$REMOTE_ADDR,$APPLICATION;

		try {
			/**
			 * проверим нужна ли проверка на уникальность email
			 */
			if(
				array_key_exists("EMAIL", $arField) && strlen($arField['EMAIL']) > 0
				&& \COption::GetOptionString("main", "new_user_email_uniq_check", "N") === "Y"
			) {

				/**
				 * проверим есть ли пользоователь с таким email на сайте
				 */
				$res = \CUser::GetList($b, $o, array("=EMAIL" => $arField["EMAIL"]));
				if($res->Fetch())
					throw new ArgumentException(Loc::getMessage("CP_UREGFORM_REGISTER_USER_WITH_EMAIL_EXIST", array("#EMAIL#" => htmlspecialcharsbx($arField["EMAIL"]))),'USER_FIELD_CHACK');
			}

			$arField["CHECKWORD"] = randString(8);
			$arField["~CHECKWORD_TIME"] = $DB->CurrentTimeFunction();
			$arField["ACTIVE"] = $this->arParams['ACTIVE'];
			$arField["CONFIRM_CODE"] = $this->arParams['ACTIVE'] == "N" ? randString(8): "";
			$arField["LID"] = SITE_ID;
			$arField["USER_IP"] = $_SERVER["REMOTE_ADDR"];
			$arField["USER_HOST"] = @gethostbyaddr($REMOTE_ADDR);
			$arField["GROUP_ID"] = $this->addGroup;

			/**
			 * проверим указан ли пароль для изменения и совпадает ли новый пароль и потвержденный пароль
			 */
			if(!array_key_exists('CONFIRM_PASSWORD', $arField)
				&& array_key_exists('PASSWORD', $arField)
			) {
				$arField['CONFIRM_PASSWORD'] = $arField['PASSWORD'];
			}
			elseif(
				array_key_exists('CONFIRM_PASSWORD', $arField)
				&& !array_key_exists('PASSWORD', $arField)
			) {
				$arField['PASSWORD'] = $arField['CONFIRM_PASSWORD'];
			}
			elseif(
				!array_key_exists('CONFIRM_PASSWORD', $arField)
				&& !array_key_exists('PASSWORD', $arField)
			) {
				$pass = $this->__generationPassword();
				$arField['CONFIRM_PASSWORD'] = $pass;
				$arField['PASSWORD'] = $pass;
			}
			else {}

			if(
				!array_key_exists("EMAIL", $arField)
				&& \COption::GetOptionString("main", "new_user_email_required", "N") === "Y"
			) {
				throw new ArgumentException(Loc::getMessage("CP_UREGFORM_REGISTER_F_USER_EMAIL"),'USER_EMAIL');
			}

			if(
				!array_key_exists("LOGIN", $arField)
				&& array_key_exists("EMAIL", $arField) && strlen($arField['EMAIL']) > 0
			) {
				$arField['LOGIN'] = $arField['EMAIL'];
			}

			if(!array_key_exists("LOGIN", $arField)) {
				throw new ArgumentException(Loc::getMessage("CP_UREGFORM_REGISTER_F_USER_LOGIN"),'USER_LOGIN');
			}

			$bOk = true;
			$events = GetModuleEvents("main", "OnBeforeUserRegisterForm");
			while($arEvent = $events->Fetch()) {
				if(ExecuteModuleEventEx($arEvent, array(&$arField)) === false) {
					if($err = $APPLICATION->GetException()) {
						throw new ArgumentException($err->GetString(),'BEFORE_USER_REGISTER_EVENT');
					}
				}
			}

			/**
			 * добавление  пользователя в базу
			 */
			$user = new \CUser();
			$ID = $user->Add($arField);
			if(IntVal($ID) <= 0) {
				if(\COption::GetOptionString("main", "event_log_register_fail", "N") === "Y")
					\CEventLog::Log("SECURITY", "USER_REGISTER_FAIL", "main", $ID, implode("<br>", $user->LAST_ERROR));
				throw new ArgumentException($user->LAST_ERROR,'USER_REGISTER_FAIL');
			}
			$arField['USER_ID'] = $ID;
			$event = new \CEvent();
			$event->SendImmediate('NEW_USER', SITE_ID, $arField);

			if(\COption::GetOptionString("main", "event_log_register", "N") === "Y")
				\CEventLog::Log("SECURITY", "USER_REGISTER", "main", $ID);

			$events = GetModuleEvents('main', 'OnAfterUserRegister');

			while ($arEvent = $events->Fetch())
				ExecuteModuleEventEx($arEvent, array(&$arField));

			if ($arField["ACTIVE"] == "Y") {
				if (!$arAuthResult = $USER->Login($arField["LOGIN"], $arField["PASSWORD"]))
					throw new ArgumentException(Loc::getMessage('CP_UREGFORM_AFTER_REGISTER_LOGIN_FAIL'),
						'USER_AFTER_REGISTER_LOGIN_FAIL');
			}
		}
		catch(ArgumentException $ex) {
			if(strlen($ex->getParameter()) > 0)
				$this->LAST_ERROR[$ex->getParameter()] = $ex->getMessage();
			else
				$this->LAST_ERROR[] = $ex->getMessage();
			return false;
		}
		return $ID;
	}
	
	/**
	 * @param int $iblockID
	 * @param mixed[] $currentValue
	 * @param string $parentComponentName
	 * @return array
	 *
	 * @todo Нужно избавиться от переопределения аргументов метода в базовом классе
	 */
	public function GetParametrsFieldInput($currentValue, $parentComponentName) {
		//$parent = new \CBCitrusSmartForm();
		//$arFields = $parent->GetParametrsFieldInput('','','BASE',$this->getComponentPathByClassName($parentComponentName));
		$parentComponentName = $parentComponentName ?: static::$parentComponentName;
		// @todo Явно указан базовый класс, зачем тогда $parentComponentName?
		$parent = new BaseComponent();
		$arFields = $parent->GetParametrsFieldInput('','','BASE',$this->getComponentPathByClassName($parentComponentName));
		
		if(!isset($currentValue['FIELDS']) || !is_array($currentValue['FIELDS']) || empty($currentValue['FIELDS'])) {
			$arFields['JS_DATA'] = $this->prepeaParamsBeforeOpenSetting($arFields['JS_DATA']);
			return $arFields;
		}
		
		if(!isset($currentValue['FIELDS']) || !is_array($currentValue['FIELDS']))
			$curField = array();
		else
			$curField = $currentValue['FIELDS'];
		
		/**
		 * TODO великий костыль! пришлось парсить список полей,
		 * потому что в визуальном редакторе поля передаются не верно (из настроек модуля)
		 */
		foreach($curField as $code => &$f)
			$f = $this->parseParamFieldValue($f);
		
		foreach($arFields['JS_DATA'] as $dIndex => $dF) {
			if(array_key_exists($dIndex,$curField)) {
				$curField[$dIndex]['ACTIVE'] = 'Y';
				$curField[$dIndex] = array_merge($dF,$curField[$dIndex]);
			}
			else {
				$dF['ACTIVE'] = 'N';
				$curField[$dIndex] = $dF;
			}
		}
		
		$curField = $this->prepeaParamsBeforeOpenSetting($curField);
		
		$arFields['JS_DATA'] = $curField;
		
		return $arFields;
	}
}
