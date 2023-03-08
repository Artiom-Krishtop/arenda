<?php

namespace Citrus\Forms;

use Bitrix\Main;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Web\Json;
use Citrus\Forms\Events\OnSuccessFormSubmit;

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if (!Main\Loader::includeModule('citrus.forms'))
{
	return;
}

Loc::loadMessages(__FILE__);
Loc::loadMessages(__DIR__ . "/.parameters.php");

class BaseComponent extends \CBitrixComponent {
	const ACTION_ADD = 'add';
	const ACTION_UPDATE = 'update';
	const SESSION_PREFIX = 'CITRUS_FROM';
	const DEFAULT_TEMPLATE_ID = '.default';

	protected static $iblocks;

	protected $action;
	protected $ajaxMode = false;

	var $allowRegistration = false;
	var $addGroup = false;
	var $bxPrefix = "UF_";
	var $eventPostfix = "SmartForm";
	var $LAST_ERROR = array();
	var $listFType = array("L","E","G");
	var $formID = null;
	var $templateMap;

	static $arFieldTemplates;

	static $arFTemplateType = array(
		'captcha' => array('CAPTCHA'),
		'date' => array('Date','DateTime','date', 'datetime'),
		'file' => array('F'),
		'html' => array('HTML','T'),
		'list' => array('E','G','L', 'EAutocomplete','enumeration', 'EList', 'enum'),
		'text' => array('S','N','TEXT','string'),
		'number' => array("UserID"),
		'unknown' => array(),
	);

	protected $arAllowField = array(
		'VALID_ERROR_MSG',
		'VALIDRULE',
		'REGRULE',
		'ADDITIONAL',
		'DEFAULT',
		'CLASS',
		'PLACEHOLDER',
		'DESCRIPTION',
		'TITLE',
		'DEPTH_LAVEL',
		'IS_REQUIRED',
		'HIDE_FIELD',
		'ORIGINAL_TITLE',
		'GROUP_FIELD',
		'ACTIVE',
		'TEMPLATES',
		'TEMPLATE_ID'
	);

	public function onPrepareComponentParams($arParams) {
		$this->GetRequiredUserField();

		$arParams['CACHE_TIME'] = isset($arParams["CACHE_TIME"]) ? $arParams["CACHE_TIME"] : 36000000;
		$arParams['FORM_ACTIONS'] = array_key_exists('FORM_ACTIONS', $arParams) && strlen($arParams['FORM_ACTIONS']) > 0 ? trim(htmlspecialcharsbx($arParams['FORM_ACTIONS'])) : $GLOBALS['APPLICATION']->GetCurPage();

		$arParams['SEND_IMMEDIATE'] = array_key_exists('SEND_IMMEDIATE', $arParams) && $arParams['SEND_IMMEDIATE'] == "Y" ? "Y" : "N";

		$arParams['FIELDS'] = is_array($arParams['FIELDS']) ? $arParams['FIELDS'] : array();

		/**
		 * получим список пользовательских полей, и добавим в пользовательское свойство недостающие поля
		 */
		$arUserFields = $this->GetUserField();
		if(!empty($arParams['FIELDS'])) {
			$arUpFields = array_merge_recursive(array_intersect_key($arParams['FIELDS'], $arUserFields),array_intersect_key($arUserFields,$arParams['FIELDS']));
			foreach($arUpFields as $code => $value) {
				$arSettings = $GLOBALS['USER_FIELD_MANAGER']->PrepareSettings(0,$value);

				/**
				 * если регулярное выражение для проверки было указано в админке, то переопределим настройки компанента
				 */
				if($arSettings && array_key_exists('REGEXP', $arSettings))
					$value['VALIDRULE'] = strlen($arSettings['REGEXP']) > 0 ? $arSettings['REGEXP'] : trim($value['VALIDRULE']);

				/**
				 * переопределим сообщение об ошибке
				 */
				$value['VALID_ERROR_MSG'] = strlen($value['ERROR_MESSAGE']) > 0 ? $value['ERROR_MESSAGE'] : $value['VALID_ERROR_MSG'];
				$arParams['FIELDS'][$code] = $value;
			}

			foreach($arParams['FIELDS'] as $code => &$value) {
				$value['CODE'] = "FIELDS[{$code}]";

				if(is_string($value['VALIDRULE']) && strlen($value['VALIDRULE']) > 0) {
					$value['VALIDRULE'] = explode('|',$value['VALIDRULE']);
				} elseif(!$value['GROUP_FIELD']) {
					$value['VALIDRULE'] = array();
				}

				/**
				 * добавим обязательные поля в правила валидации
				 */
				if ('Y' == $value['IS_REQUIRED'] && is_array($value["VALIDRULE"]) && !in_array('required',$value["VALIDRULE"]))
					$value["VALIDRULE"][] = "required";
			}


			if(array_key_exists('PERSONAL_GENDER', $arParams['FIELDS'])) {
				$arParams['FIELDS']['PERSONAL_GENDER']['ITEMS'] = array(
					"M" => Loc::getMessage('CP_SMARTFORM_F_P_GENDER_M'),
					"F" => Loc::getMessage('CP_SMARTFORM_F_P_GENDER_F'),
				);

				$arParams['FIELDS']['PERSONAL_GENDER']['OLD_VALUE'] = "M";
			}
		}

		if(array_key_exists('PASSWORD', $arParams['FIELDS']))
			$arParams['FIELDS']['PASSWORD']['IS_PASSWORD'] = true;

		if(array_key_exists('CONFIRM_PASSWORD', $arParams['FIELDS']))
			$arParams['FIELDS']['CONFIRM_PASSWORD']['IS_PASSWORD'] = true;

		/**
		 * если в массиве шаблонов больше 1 значения и существует значнеие по умолчанию "все", то удалим это значение
		 */
		if(array_key_exists('MAILE_EVENT_TEMPLATE', $arParams)) {
			if(!is_array($arParams['MAILE_EVENT_TEMPLATE']))
				$arParams['MAILE_EVENT_TEMPLATE'] = array($arParams['MAILE_EVENT_TEMPLATE']);

			$arParams['MAILE_EVENT_TEMPLATE'] = array_diff($arParams['MAILE_EVENT_TEMPLATE'],array("","0"));
		}
		if(!is_array($arParams['MAILE_EVENT_TEMPLATE']) || empty($arParams['MAILE_EVENT_TEMPLATE']))
			$arParams['MAILE_EVENT_TEMPLATE'] = $this->getEventTemplates($arParams['MAIL_EVENT']);

		/**
		 * проверим глобальные настройки по авторизации
		 */
		$this->allowRegistration = \COption::GetOptionString("main", "new_user_registration", "Y") == "Y" ? true : false;
		$this->addGroup = strlen(\COption::GetOptionString("main", "new_user_registration_def_group", "")) > 0 ? array_merge(array(2),explode(",", \COption::GetOptionString("main", "new_user_registration_def_group", ""))) : array(2);

		$arParams['FORM_ID'] = array_key_exists('FORM_ID', $arParams) && strlen($arParams['FORM_ID']) > 0 ? trim(htmlspecialcharsbx($arParams['FORM_ID'])) : null;

		/**
		 * Подключаем сторонние плагины
		 */
		$this->loadExternalLibrary();

		return $arParams;
	}

	protected function getEventTemplates($mailEvent) {
		$rsMess = \CEventMessage::GetList($by = "site_id", $order = "desc", Array(
			"TYPE_ID" => $mailEvent,
			"ACTIVE" => "Y",
			"SITE_ID" => SITE_ID,
		));
		while($arr = $rsMess->Fetch())
			$arMailTemplate[] = $arr['ID'];
		return $arMailTemplate;
	}

	/**
	 * @deprecated Include citrus.forms module instead
	 */
	protected function loadExternalLibrary() {
		\Bitrix\Main\Loader::includeModule('citrus.forms');
	}

	/**
	 * Сохранение параметров компонента в сессии
	 */
	public function saveComponentParams($FORM_ID) {
		$arParams = $this->arParams;

		$signer = new Main\Security\Sign\Signer();
		$paramsStr = base64_encode(Json::encode($arParams));

		$hash = preg_replace('/\:/','_',$this->getName());
		$_SESSION[self::SESSION_PREFIX][$FORM_ID] = $signer->sign($paramsStr,$hash);
	}

	/**
	 * Получить набор параметров сохраненных в сессии
	 *
	 * @param string $FORM_ID - id формы
	 *
	 * @return bool|mixed - массив с данными (параметры компонента)
	 */
	public function loadComponentParams($FORM_ID) {
		$signer = new Main\Security\Sign\Signer();

		if(
			!isset($_SESSION[self::SESSION_PREFIX])
			|| !isset($_SESSION[self::SESSION_PREFIX][$FORM_ID])
		) {
			return false;
		}

		$strParams = $_SESSION[self::SESSION_PREFIX][$FORM_ID];
		$hash = preg_replace('/\:/','_',$this->getName());
		$parameters = $signer->unsign($strParams, $hash);

		return Json::decode(base64_decode($parameters));
	}

	public function executeComponent() {
		global $APPLICATION, $USER;

		/**
		 * если пользователь не авторизован то он ен может изменять свои личные данные
		 */
		if(false === $USER->IsAuthorized() || !$ID = $USER->GetID()) {
			ShowError(Loc::getMessage('CP_SMARTFORM_E_USER_NOT_AUTHORIZED'));
			return;
		}

		$this->setFormID($this->arParams['FORM_ID']);

		$this->arResult = array(
			"ID" => $ID,
			"FORM_ID" => $this->formID,
			"FORM_ACTIONS" => $this->arParams['FORM_ACTIONS'],
			"ITEMS" => $this->arParams['FIELDS'],
			"COMPONENT_NAME" => $this->getName()
		);

		/**
		 * получим первоначальные данные пользователя
		 */
		$this->GetCurUserFieldValue($this->arResult['ITEMS'],$ID);

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
				$arField = $this->validateFormField();
				if(false === $arField)
					throw new Main\ArgumentException(Loc::getMessage('CP_SMARTFORM_E_VALIDATE_ERROR'),'VALIDATE_ERROR');

				/**
				 * обновление полей пользователя
				 */
				$isUpdate = $this->executeComponentTask($arField,$ID);
				if(false === $isUpdate)
					throw new Main\ArgumentException(Loc::getMessage('CP_SMARTFORM_E_UPDATE_ERROR'),'UPDATE_ERROR');

				if($this->arParams['SEND_MESSAGE'] == "Y") {
					$arField['ID'] = $ID;
					$this->SendSuccessFormMessage($arField, $ID);
				}

				LocalRedirect($APPLICATION->GetCurPageParam("saccess=Y&id=" . $ID,array("saccess","id")));
			}
			catch(Main\ArgumentException $ex) {
				if(strlen($ex->getParameter()) > 0)
					$this->arResult['ERROR_TITLE'][$ex->getParameter()] = $ex->getMessage();
				else
					$this->arResult['ERROR_TITLE'] = $ex->getMessage();
				$this->arResult['ERRORS'] = $this->LAST_ERROR;
			}
		}

		if(array_key_exists("CAPTCHA", $this->arParams['FIELDS']))
			$this->arResult["CAPTCHA_CODE"] = htmlspecialcharsbx($APPLICATION->CaptchaGetCode());

		if (!$this->isAjaxMode())
		{
			$this->includeComponentTemplate();
		}
	}

	public function GetCurUserFieldValue(&$arFieldList,$id = false) {
		global $USER;
		if(false === $id || IntVal($id) <= 0)
			return;

		$objFireld = $USER->GetByID($id);
		if(!is_object($objFireld) || !$arField = $objFireld->Fetch())
			return;

		foreach ($arFieldList as $key => &$value) {
			if(
				!array_key_exists($key, $arField)
				|| ($key == "CONFIRM_PASSWORD" || $key == "PASSWORD")
			) {
				continue;
			}

			$value['OLD_VALUE'] = $arField[$key];
			if(true === $this->IsUserField($key)) {
				$value['ENTITY_VALUE_ID'] = $arField[$key];
				$value['VALUE'] = $arField[$key];
			}
		}
	}

	public function executeComponentTask($arFields = array(),$ID = false) {
		global $USER;
		if(empty($arFields) || false === $ID || IntVal($ID) <= 0)
			return true;

		$res = $USER->Update($ID, $arFields);
		if(false === $res)
			$this->LAST_ERROR[] = $USER->LAST_ERROR;

		return $res;
	}

	/**
	 * @param bool $ajaxMode
	 * @return BaseComponent
	 */
	public function setAjaxMode($ajaxMode = true)
	{
		$this->ajaxMode = $ajaxMode;

		return $this;
	}

	/**
	 * @return bool
	 */
	public function isAjaxMode()
	{
		return $this->ajaxMode;
	}

	protected function cleanOldValue() {
		foreach($this->arParams['FIELDS'] as $code => &$filed)
			$filed['OLD_VALUE'] = '';
	}

	public function validateFormField() {
		global $APPLICATION;
		$context = Main\HttpApplication::getInstance()->getContext();
		$request = $context->getRequest();

		$this->laodAction();

		try {
			/**
			 * получим список полей передавайемых формой. Если данных нет, то выведем ошибку
			 */
			/** @var array $arPostData */
			$arPostData = $request->get('FIELDS');
			if(false === $arPostData || !is_array($arPostData))
				throw new Main\ArgumentException(Loc::getMessage('CP_SMARTFORM_E_REQUEST_ARRAY_F_EMPTY'),'NOT_FIELDS');

			/**
			 * Проверка антиспама
			 */
			if($this->arParams['HIDDEN_ANTI_SPAM'] !== "N" && (bool) $request->get('GIFT'))
				$this->LAST_ERROR['HIDDEN_ANTI_SPAM'] = Loc::getMessage('CP_SMARTFORM_E_GIFT');

			/**
			 * получим список полей по умолчанию
			 */
			$arDefaultFields = $this->GetDefaultFields();

			/**
			 * Событие позволяет изменить поля перед валидацией
			 */
			$db_events = GetModuleEvents("main", "OnBeforeValidateFieldForm{$this->eventPostfix}");
			while($arEvent = $db_events->Fetch())
				ExecuteModuleEventEx($arEvent, array(&$arDefaultFields, &$arPostData));

			/**
			 * Процедура проверки полей на валидность
			 */
			foreach($this->arResult['ITEMS'] as $code => $arField) {
				/**
				 * Проверка является ли данное поле функциональным.
				 *
				 * Каждое поле проверяется, требует ли заполнения или проверки.
				 * Поля типа - группа полей не требуется проверять. Таким образом возможно убрать из процедуры
				 * валидации любое поле.
				 * Поле "группа" - несет лишь эстетическое назначение.
				 * */
				if(isset($arField['GROUP_FIELD']) && 'Y' === $arField['GROUP_FIELD'])
					continue;

				/**
				 * Поле CAPTCHA
				 * Данное поле имеет специфическую проверку. Ему нельзя назначить регулярное выражение.
				 * */
				if($code == "CAPTCHA" && 'Y' == $arField['IS_REQUIRED']) {

					/**
					 * Если используется google reCapcha то проверим её
					 * иначе штатная проверка Битрикс Capcha
					 */
					if('Y' == $this->arParams['USE_GOOGLE_RECAPTCHA']) {
						$reCaptcha = new \ReCaptcha\ReCaptcha($this->arParams['GOOGLE_RECAPTCHA_PRIVATE_KEY']);
						$gRecaptchaResponse = $request->get('g-recaptcha-response');
						$resp = $reCaptcha->verify($gRecaptchaResponse, $_SERVER['REMOTE_ADDR']);
						if (!$resp->isSuccess()) {
							$this->LAST_ERROR['CAPTCHA'] = $resp->getErrorCodes();
						}
					}
					else {
						if (!isset($arPostData[$code]) || !strlen(trim($arPostData[$code])))
						{
							$this->LAST_ERROR['CAPTCHA'] = Loc::getMessage("CP_SMARTFORM_E_REQUIRED_F_EMPTY" ,array("#FIELD#" => $this->arParams['FIELDS']['CAPTCHA']['TITLE']));
						}
						elseif (!$APPLICATION->CaptchaCheckCode($arPostData[$code], $request->get("captcha_sid")))
						{
							$this->LAST_ERROR['CAPTCHA'] = Loc::getMessage("CP_SMARTFORM_E_VALIDATION_FIELD", array("#FIELD#" => $this->arParams['FIELDS']['CAPTCHA']['TITLE']));
						}
					}

					continue;
				}

				/**
				 * Поля типа - файл.
				 * Проверка происходит в отдельной функции - GetFileListRequest
				 * */
				if($arField['TYPE'] == "F") {
					$isMultiple = $arField['MULTIPLE'] == "Y" ? true : false;
					$arFile = $this->GetFileListRequest($code,$isMultiple);

					if(false === $arFile && 'Y' == $arField['IS_REQUIRED'])
						$this->LAST_ERROR[$code] = Loc::getMessage("CP_SMARTFORM_E_REQUIRED_F_EMPTY" ,array("#FIELD#" => $arField['TITLE']));
					else
						$arAddUser[$code] = $arFile;

					continue;
				}

				/**
				 * Проверка обязательных полей.
				 *
				 * Проверим существует ли данное поле в массиве запроса. Если данное поле не было передано в форме,
				 * то выведем сообщение об ошибке
				 */
				if('Y' == $arField['IS_REQUIRED'] && !array_key_exists($code, $arPostData)) {
					$this->LAST_ERROR[$code] = Loc::getMessage("CP_SMARTFORM_E_REQUIRED_F_EMPTY" ,array("#FIELD#" => $arField['TITLE']));
					continue;
				}

				$fieldVal = $arPostData[$code];
				$bxError = false;
				if($arField['IS_REQUIRED'] == 'Y') {

					if(
						true === $this->IsUserField($code) &&
						($arField['MULTIPLE'] !="Y" && !in_array($arField['TYPE'],$this->listFType))
						&& strlen($fieldVal) <= 0
					) {
						$bxError = true;
					}
					elseif(
						true === $this->IsUserField($code) &&
						($arField['MULTIPLE'] !="Y" && in_array($arField['TYPE'],$this->listFType))
						&& strlen($fieldVal) <= 0
					){
						$bxError = true;
					}
					elseif(strlen($arPostData[$code]) <= 0) {
						$bxError = true;
					}

					if(true === $bxError) {
						$this->LAST_ERROR[$code] = Loc::getMessage("CP_SMARTFORM_E_REQUIRED_F_EMPTY" ,array("#FIELD#" => $arField['TITLE']));
						continue;
					}
				}

				if(false === $this->prepaData($fieldVal,$arField,$code)) {
					if(!array_key_exists($code,$this->LAST_ERROR))
						$this->LAST_ERROR[$code] = Loc::getMessage("CP_SMARTFORM_E_VALIDATION_FIELD" ,array("#FIELD#" => $arField['TITLE']));
				}

				if($arField['GROUP_FIELD'] != "Y")
					$arAddUser[$code] = $fieldVal;

				/**
				 * запоминаем старое значение формы, значение исключим с полей пароль и потверждение пароля
				 */
				if(
					$code != "CONFIRM_PASSWORD" && $code != "PASSWORD"
				) {
					$this->arResult['ITEMS'][$code]['OLD_VALUE'] = $fieldVal;
				}

				if($arField['TYPE'] == "USER_FIELD") {
					$this->arResult['ITEMS'][$code]['ENTITY_VALUE_ID'] = $fieldVal;
					$this->arResult['ITEMS'][$code]['VALUE'] = $fieldVal;
				}
			}

			/**
			 * проверим указан ли пароль для изменения и совпадает ли новый пароль и потвержденный пароль
			 */
			if(
				array_key_exists('CONFIRM_PASSWORD', $arPostData)
				&& array_key_exists('PASSWORD', $arPostData)
				&& (
					strlen($arPostData['PASSWORD']) > 0 || strlen($arPostData['CONFIRM_PASSWORD']) > 0
				)
				&& $arPostData['PASSWORD'] != $arPostData['CONFIRM_PASSWORD']
			) {
				throw new Main\ArgumentException(Loc::getMessage("CP_SMARTFORM_F_CONFIRM_PASSWORD_NOT_SUCCESS_ERROR"),'CONFIRM_PASSWORD_NOT_SUCCESS');
			}

			if(count($this->LAST_ERROR) <= 0) {
				/**
				 * заполним обязательные пользовательские поля,если они не были в списке полей
				 */
				global $USER_FIELD_MANAGER;
				// @todo Не понятно, нужно ли это
				$arRequiredUserField = $this->GetRequiredUserField($arAddUser);
				if(!$USER_FIELD_MANAGER->CheckFields("USER", 0, $arAddUser)) {
					if($err = $APPLICATION->GetException())
						throw new Main\ArgumentException($err->GetString(),'USER_FIELD_CHACK');
				}
			}
		}
		catch(Main\ArgumentException $ex) {
			if(strlen($ex->getParameter()) > 0)
				$this->LAST_ERROR[$ex->getParameter()] = $ex->getMessage();
			else
				$this->LAST_ERROR[] = $ex->getMessage();
			return false;
		}

		$db_events = GetModuleEvents("main", "OnAfterValidateFieldForm{$this->eventPostfix}");
		while($arEvent = $db_events->Fetch()) {
			ExecuteModuleEventEx($arEvent, array(&$arDefaultFields, &$arPostData,&$this->LAST_ERROR));
		}

		if(array_key_exists('CAPTCHA',$arAddUser))
			unset($arAddUser['CAPTCHA']);

		return !empty($this->LAST_ERROR) ? false : $arAddUser;
	}

	/**
	 * Отправляет сообщение если это требуется
	 *
	 * @param $arAddUser - Доступные поля для почтового события
	 * @param $ID - идентификатор добавленного элемента
	 */
	public function SendSuccessFormMessage($arAddUser, $ID) {
		$bxSendMail = true;
		$arEventFields = $this->getMailField($ID,$arAddUser);

		$arEventFields['USER_ID'] = $arEventFields['ID'];

		if(isset($this->arParams['EMAIL_TO_VALUE']) && strlen($this->arParams['EMAIL_TO_VALUE']))
			$arEventFields['EMAIL_TO'] = $this->arParams['EMAIL_TO_VALUE'];

		if(isset($this->arParams['EMAIL_SUBJECT']) && strlen($this->arParams['EMAIL_SUBJECT']))
			$arEventFields['EMAIL_SUBJECT'] = $this->arParams['EMAIL_SUBJECT'];

		(new OnSuccessFormSubmit($arEventFields, $ID))->send($this);

		$events = GetModuleEvents("main", "OnBeforeUpdateFormSendMail{$this->eventPostfix}");

		while ($arEvent = $events->Fetch())
			$bxSendMail = ExecuteModuleEventEx($arEvent, array(&$arEventFields,$ID));

		if(!isset($bxSendMail) || true === $bxSendMail) {
			$event = new \CEvent;
			foreach ($this->arParams['MAILE_EVENT_TEMPLATE'] as $value) {
				$arFile = isset($arAddUser['FILES']) ? $arAddUser['FILES'] : false;
				if(false !== $arFile && !is_array($arFile))
					$arFile = array($arFile);

				foreach (GetModuleEvents(CITRUS_FORMS_MODULE, "OnBeforeSend{$this->eventPostfix}", true) as $arEvent)
					ExecuteModuleEventEx($arEvent, array(&$this, &$arEventFields, &$arFile));

				if($this->arParams['SEND_IMMEDIATE'] == "N")
					$event->Send($this->arParams['MAIL_EVENT'], SITE_ID, $arEventFields,"N",$value,$arFile);
				else
					$event->SendImmediate($this->arParams['MAIL_EVENT'], SITE_ID, $arEventFields,"N",$value,$arFile);
			}
		}

		$events = GetModuleEvents("main", "OnAfterUpdateFormSendMail{$this->eventPostfix}");
		while ($arEvent = $events->Fetch())
			ExecuteModuleEventEx($arEvent, array(&$arEventFields,$ID));
	}

	protected function addMissingField() {
		$defaultField = $this->GetDefaultFields();
		$defaultField = array_merge($defaultField['FIELDS'],$defaultField['USER_FIELD']);

		foreach ($this->arResult['ITEMS'] as $code => &$dField) {
			if(!array_key_exists($code, $defaultField))
				continue;

			$dField = array_merge($defaultField[$code],$dField);

			$this->arResult['ITEMS'][$code] = $dField;
		}
	}

	/**
	 * Список полей по умолчанию
	 *
	 * Функция позволяет получить список всех полей, которые доступны в форме.
	 * Каждое поле содержит список доступных атрибутов:
	 *  ORIGINAL_TITLE - оригинальное название поля
	 *  TITLE - нименование поля, которое можно изменить при настройке компонента
	 *  TYPE - тип поля (F - файл, S - строка, L - список, E - привязка к элементу ИБ, G - привязка к разделу)
	 *  TOOLTIP - произвольный текст, подсказка для поля
	 *  IS_REQUIRED - признак обязательности поля
	 *  VALIDRULE - правилол валидации для формы (в виде регулярного выражения)
	 *  VALID_ERROR_MSG - текст ошибки при проверки правилом валидации
	 *  HIDE_FIELD - признак скрытое поле или нет
	 *
	 * @return array(FIELDS,USER_FIELD) USER_FIELD - список пользовательских полей, FIELDS - список полей формы
	 */
	public function GetDefaultFields() {
		$arCountry = array();
		$arCountryList = GetCountryArray();
		foreach($arCountryList['reference_id'] as $index => $val) {
			$arCountry[$val] = $arCountryList['reference'][$index];
		}

		$arDefaultFields = array(
			"NAME" => array("ORIGINAL_TITLE" => Loc::getMessage('CP_SMARTFORM_F_NAME_TITLE'),"TITLE" => Loc::getMessage('CP_SMARTFORM_F_NAME_TITLE'),"TYPE" => "S","TOOLTIP" => "","IS_REQUIRED" => "N","VALIDRULE" => "","VALID_ERROR_MSG" => "","HIDE_FIELD" => "N", "ACTIVE" => "Y"),
			"LAST_NAME" => array("ORIGINAL_TITLE" => Loc::getMessage('CP_SMARTFORM_F_LAST_NAME_TITLE'),"TITLE" => Loc::getMessage('CP_SMARTFORM_F_LAST_NAME_TITLE'),"TYPE" => "S","TOOLTIP" => "","IS_REQUIRED" => "N","VALIDRULE" => "","VALID_ERROR_MSG" => "","HIDE_FIELD" => "N", "ACTIVE" => "Y"),
			"SECOND_NAME" => array("ORIGINAL_TITLE" => Loc::getMessage('CP_SMARTFORM_F_SECOND_NAME_TITLE'),"TITLE" => Loc::getMessage('CP_SMARTFORM_F_SECOND_NAME_TITLE'),"TYPE" => "S","TOOLTIP" => "","IS_REQUIRED" => "N","VALIDRULE" => "","VALID_ERROR_MSG" => "","HIDE_FIELD" => "N", "ACTIVE" => "Y"),
			"EMAIL" => array("ORIGINAL_TITLE" => Loc::getMessage('CP_SMARTFORM_F_EMAIL_TITLE'),"TITLE" => Loc::getMessage('CP_SMARTFORM_F_EMAIL_TITLE'),"TYPE" => "S","TOOLTIP" => "","IS_REQUIRED" => true,"VALIDRULE" => "","VALID_ERROR_MSG" => "","HIDE_FIELD" => "N", "ACTIVE" => "Y"),
			"LOGIN" => array("ORIGINAL_TITLE" => Loc::getMessage('CP_SMARTFORM_F_LOGIN_TITLE'),"TITLE" => Loc::getMessage('CP_SMARTFORM_F_LOGIN_TITLE'),"TYPE" => "S","TOOLTIP" => "","IS_REQUIRED" => true,"VALIDRULE" => "","VALID_ERROR_MSG" => "","HIDE_FIELD" => "N", "ACTIVE" => "Y"),
			"PASSWORD" => array("ORIGINAL_TITLE" => Loc::getMessage('CP_SMARTFORM_F_PASSWORD_TITLE'),"TITLE" => Loc::getMessage('CP_SMARTFORM_F_PASSWORD_TITLE'),"TYPE" => "S","TOOLTIP" => "","IS_REQUIRED" => true,"VALIDRULE" => "","VALID_ERROR_MSG" => "","HIDE_FIELD" => "N", "ACTIVE" => "Y"),
			"CONFIRM_PASSWORD" => array("ORIGINAL_TITLE" => Loc::getMessage('CP_SMARTFORM_F_CONFIRM_PASSWORD_TITLE'),"TITLE" => Loc::getMessage('CP_SMARTFORM_F_CONFIRM_PASSWORD_TITLE'),"TYPE" => "S","TOOLTIP" => "","IS_REQUIRED" => true,"VALIDRULE" => "","VALID_ERROR_MSG" => "","HIDE_FIELD" => "N", "ACTIVE" => "Y"),

			"PERSONAL_BIRTHDAY" => array("ORIGINAL_TITLE" => Loc::getMessage("CP_SMARTFORM_F_BIRTHDAY_TITLE"), "TITLE"=> Loc::getMessage("CP_SMARTFORM_F_BIRTHDAY_TITLE"),"TYPE" => "Date","TOOLTIP" => "","IS_REQUIRED" => "N","VALIDRULE" => "","VALID_ERROR_MSG" => "","HIDE_FIELD" => "N", "ACTIVE" => "Y"),
			"PERSONAL_PHOTO" => array("ORIGINAL_TITLE" => Loc::getMessage("CP_SMARTFORM_F_PHOTO_TITLE"), "TITLE"=> Loc::getMessage("CP_SMARTFORM_F_PHOTO_TITLE"),"TYPE" => "F","TOOLTIP" => "","IS_REQUIRED" => "N","VALIDRULE" => "","VALID_ERROR_MSG" => "","HIDE_FIELD" => "N", "ACTIVE" => "Y"),
			"PERSONAL_GENDER" => array("ORIGINAL_TITLE" => Loc::getMessage("CP_SMARTFORM_F_P_GENDER_TITLE"), "TITLE"=> Loc::getMessage("CP_SMARTFORM_F_P_GENDER_TITLE"),"TYPE" => "L","TOOLTIP" => "","IS_REQUIRED" => "N","VALIDRULE" => "","VALID_ERROR_MSG" => "","HIDE_FIELD" => "N", "ACTIVE" => "Y"),
			"PERSONAL_PROFESSION" => array("ORIGINAL_TITLE" => Loc::getMessage("CP_SMARTFORM_F_P_PROFESSION_TITLE"), "TITLE"=> Loc::getMessage("CP_SMARTFORM_F_P_PROFESSION_TITLE"),"TYPE" => "S","TOOLTIP" => "","IS_REQUIRED" => true,"VALIDRULE" => "","VALID_ERROR_MSG" => "","HIDE_FIELD" => "N", "ACTIVE" => "Y"),
			"PERSONAL_WWW" => array("ORIGINAL_TITLE" => Loc::getMessage("CP_SMARTFORM_F_P_WWW_TITLE"), "TITLE"=> Loc::getMessage("CP_SMARTFORM_F_P_WWW_TITLE"),"TYPE" => "S","TOOLTIP" => "","IS_REQUIRED" => "N","VALIDRULE" => "","VALID_ERROR_MSG" => "","HIDE_FIELD" => "N", "ACTIVE" => "Y"),
			"PERSONAL_ICQ" => array("ORIGINAL_TITLE" => Loc::getMessage("CP_SMARTFORM_F_P_ICQ_TITLE"), "TITLE"=> Loc::getMessage("CP_SMARTFORM_F_P_ICQ_TITLE"),"TYPE" => "S","TOOLTIP" => "","IS_REQUIRED" => "N","VALIDRULE" => "","VALID_ERROR_MSG" => "","HIDE_FIELD" => "N", "ACTIVE" => "Y"),
			"PERSONAL_PHONE" => array("ORIGINAL_TITLE" => Loc::getMessage("CP_SMARTFORM_F_P_PHONE_TITLE"), "TITLE"=> Loc::getMessage("CP_SMARTFORM_F_P_PHONE_TITLE"),"TYPE" => "S","TOOLTIP" => "","IS_REQUIRED" => "N","VALIDRULE" => "","VALID_ERROR_MSG" => "","HIDE_FIELD" => "N", "ACTIVE" => "Y"),
			"PERSONAL_FAX" => array("ORIGINAL_TITLE" => Loc::getMessage("CP_SMARTFORM_F_P_FAX_TITLE"), "TITLE"=> Loc::getMessage("CP_SMARTFORM_F_P_FAX_TITLE"),"TYPE" => "S","TOOLTIP" => "","IS_REQUIRED" => "N","VALIDRULE" => "","VALID_ERROR_MSG" => "","HIDE_FIELD" => "N", "ACTIVE" => "Y"),
			"PERSONAL_MOBILE" => array("ORIGINAL_TITLE" => Loc::getMessage("CP_SMARTFORM_F_P_MOBILE_TITLE"), "TITLE"=> Loc::getMessage("CP_SMARTFORM_F_P_MOBILE_TITLE"),"TYPE" => "S","TOOLTIP" => "","IS_REQUIRED" => "N","VALIDRULE" => "","VALID_ERROR_MSG" => "","HIDE_FIELD" => "N", "ACTIVE" => "Y"),
			"PERSONAL_PAGER" => array("ORIGINAL_TITLE" => Loc::getMessage("CP_SMARTFORM_F_P_PAGER_TITLE"), "TITLE"=> Loc::getMessage("CP_SMARTFORM_F_P_PAGER_TITLE"),"TYPE" => "S","TOOLTIP" => "","IS_REQUIRED" => "N","VALIDRULE" => "","VALID_ERROR_MSG" => "","HIDE_FIELD" => "N", "ACTIVE" => "Y"),
			"PERSONAL_MAILBOX" => array("ORIGINAL_TITLE" => Loc::getMessage("CP_SMARTFORM_F_P_MAILBOX_TITLE"), "TITLE"=> Loc::getMessage("CP_SMARTFORM_F_P_MAILBOX_TITLE"),"TYPE" => "S","TOOLTIP" => "","IS_REQUIRED" => "N","VALIDRULE" => "","VALID_ERROR_MSG" => "","HIDE_FIELD" => "N", "ACTIVE" => "Y"),
			"PERSONAL_COUNTRY" => array("ORIGINAL_TITLE" => Loc::getMessage("CP_SMARTFORM_F_P_COUNTRY_TITLE"), "TITLE"=> Loc::getMessage("CP_SMARTFORM_F_P_COUNTRY_TITLE"),"TYPE" => "L","TOOLTIP" => "","IS_REQUIRED" => "N","VALIDRULE" => "","VALID_ERROR_MSG" => "","HIDE_FIELD" => "N", "ACTIVE" => "Y","ITEMS" => $arCountry),
			"PERSONAL_STATE" => array("ORIGINAL_TITLE" => Loc::getMessage("CP_SMARTFORM_F_P_STATE_TITLE"), "TITLE"=> Loc::getMessage("CP_SMARTFORM_F_P_STATE_TITLE"),"TYPE" => "S","TOOLTIP" => "","IS_REQUIRED" => "N","VALIDRULE" => "","VALID_ERROR_MSG" => "", "HIDE_FIELD" => "N", "ACTIVE" => "Y"),
			"PERSONAL_CITY" => array("ORIGINAL_TITLE" => Loc::getMessage("CP_SMARTFORM_F_P_CITY_TITLE"), "TITLE"=> Loc::getMessage("CP_SMARTFORM_F_P_CITY_TITLE"),"TYPE" => "S","TOOLTIP" => "","IS_REQUIRED" => "N","VALIDRULE" => "","VALID_ERROR_MSG" => "","HIDE_FIELD" => "N", "ACTIVE" => "Y"),
			"PERSONAL_STREET" => array("ORIGINAL_TITLE" => Loc::getMessage("CP_SMARTFORM_F_P_STREET_TITLE"), "TITLE"=> Loc::getMessage("CP_SMARTFORM_F_P_STREET_TITLE"),"TYPE" => "S","TOOLTIP" => "","IS_REQUIRED" => "N","VALIDRULE" => "","VALID_ERROR_MSG" => "","HIDE_FIELD" => "N", "ACTIVE" => "Y"),
			"PERSONAL_ZIP" => array("ORIGINAL_TITLE" => Loc::getMessage("CP_SMARTFORM_F_P_ZIP_TITLE"), "TITLE"=> Loc::getMessage("CP_SMARTFORM_F_P_ZIP_TITLE"),"TYPE" => "S","TOOLTIP" => "","IS_REQUIRED" => "N","VALIDRULE" => "","VALID_ERROR_MSG" => "","HIDE_FIELD" => "N", "ACTIVE" => "Y"),
			"PERSONAL_NOTES" => array("ORIGINAL_TITLE" => Loc::getMessage("CP_SMARTFORM_F_P_NOTES_TITLE"), "TITLE"=> Loc::getMessage("CP_SMARTFORM_F_P_NOTES_TITLE"),"TYPE" => "S","TOOLTIP" => "","IS_REQUIRED" => "N","VALIDRULE" => "","VALID_ERROR_MSG" => "","HIDE_FIELD" => "N", "ACTIVE" => "Y"),

			"WORK_COMPANY" => array("ORIGINAL_TITLE" => Loc::getMessage("CP_SMARTFORM_F_W_COMPANY"), "TITLE"=> Loc::getMessage("CP_SMARTFORM_F_W_COMPANY"),"TYPE" => "S","TOOLTIP" => "","IS_REQUIRED" => "N","VALIDRULE" => "","VALID_ERROR_MSG" => "","HIDE_FIELD" => "N", "ACTIVE" => "Y"),
			"WORK_DEPARTMENT" => array("ORIGINAL_TITLE" => Loc::getMessage("CP_SMARTFORM_F_W_DEPARTMENT"), "TITLE"=> Loc::getMessage("CP_SMARTFORM_F_W_DEPARTMENT"),"TYPE" => "S","TOOLTIP" => "","IS_REQUIRED" => "N","VALIDRULE" => "","VALID_ERROR_MSG" => "","HIDE_FIELD" => "N", "ACTIVE" => "Y"),
			"WORK_POSITION" => array("ORIGINAL_TITLE" => Loc::getMessage("CP_SMARTFORM_F_W_POSITION"), "TITLE"=> Loc::getMessage("CP_SMARTFORM_F_W_POSITION"),"TYPE" => "S","TOOLTIP" => "","IS_REQUIRED" => "N","VALIDRULE" => "","VALID_ERROR_MSG" => "","HIDE_FIELD" => "N", "ACTIVE" => "Y"),
			"WORK_WWW" => array("ORIGINAL_TITLE" => Loc::getMessage("CP_SMARTFORM_F_W_WWW"), "TITLE"=> Loc::getMessage("CP_SMARTFORM_F_W_WWW"),"TYPE" => "S","TOOLTIP" => "","IS_REQUIRED" => "N","VALIDRULE" => "","VALID_ERROR_MSG" => "","HIDE_FIELD" => "N", "ACTIVE" => "Y"),
			"WORK_PROFILE" => array("ORIGINAL_TITLE" => Loc::getMessage("CP_SMARTFORM_F_W_PROFILE"), "TITLE"=> Loc::getMessage("CP_SMARTFORM_F_W_PROFILE"),"TYPE" => "T","TOOLTIP" => "","IS_REQUIRED" => "N","VALIDRULE" => "","VALID_ERROR_MSG" => "","HIDE_FIELD" => "N", "ACTIVE" => "Y"),
			"WORK_LOGO" => array("ORIGINAL_TITLE" => Loc::getMessage("CP_SMARTFORM_F_W_LOGO"), "TITLE"=> Loc::getMessage("CP_SMARTFORM_F_W_LOGO"),"TYPE" => "F","TOOLTIP" => "","IS_REQUIRED" => "N","VALIDRULE" => "","VALID_ERROR_MSG" => "","HIDE_FIELD" => "N", "ACTIVE" => "Y"),
			"WORK_PHONE" => array("ORIGINAL_TITLE" => Loc::getMessage("CP_SMARTFORM_F_W_PHONE"), "TITLE"=> Loc::getMessage("CP_SMARTFORM_F_W_PHONE"),"TYPE" => "S","TOOLTIP" => "","IS_REQUIRED" => "N","VALIDRULE" => "","VALID_ERROR_MSG" => "","HIDE_FIELD" => "N", "ACTIVE" => "Y"),
			"WORK_FAX" => array("ORIGINAL_TITLE" => Loc::getMessage("CP_SMARTFORM_F_W_FAX"), "TITLE"=> Loc::getMessage("CP_SMARTFORM_F_W_FAX"),"TYPE" => "S","TOOLTIP" => "","IS_REQUIRED" => "N","VALIDRULE" => "","VALID_ERROR_MSG" => "","HIDE_FIELD" => "N", "ACTIVE" => "Y"),
			"WORK_PAGER" => array("ORIGINAL_TITLE" => Loc::getMessage("CP_SMARTFORM_F_W_PAGER"), "TITLE"=> Loc::getMessage("CP_SMARTFORM_F_W_PAGER"),"TYPE" => "S","TOOLTIP" => "","IS_REQUIRED" => "N","VALIDRULE" => "","VALID_ERROR_MSG" => "","HIDE_FIELD" => "N", "ACTIVE" => "Y"),
			"WORK_COUNTRY" => array("ORIGINAL_TITLE" => Loc::getMessage("CP_SMARTFORM_F_W_COUNTRY"), "TITLE"=> Loc::getMessage("CP_SMARTFORM_F_W_COUNTRY"),"TYPE" => "L","TOOLTIP" => "","IS_REQUIRED" => "N","VALIDRULE" => "","VALID_ERROR_MSG" => "","HIDE_FIELD" => "N", "ACTIVE" => "Y","ITEMS" => $arCountry),
			"WORK_STATE" => array("ORIGINAL_TITLE" => Loc::getMessage("CP_SMARTFORM_F_W_STATE"), "TITLE"=> Loc::getMessage("CP_SMARTFORM_F_W_STATE"),"TYPE" => "S","TOOLTIP" => "","IS_REQUIRED" => "N","VALIDRULE" => "","VALID_ERROR_MSG" => "","HIDE_FIELD" => "N", "ACTIVE" => "Y"),
			"WORK_CITY" => array("ORIGINAL_TITLE" => Loc::getMessage("CP_SMARTFORM_F_W_CITY"), "TITLE"=> Loc::getMessage("CP_SMARTFORM_F_W_CITY"),"TYPE" => "S","TOOLTIP" => "","IS_REQUIRED" => "N","VALIDRULE" => "","VALID_ERROR_MSG" => "","HIDE_FIELD" => "N", "ACTIVE" => "Y"),
			"WORK_ZIP" => array("ORIGINAL_TITLE" => Loc::getMessage("CP_SMARTFORM_F_W_ZIP"), "TITLE"=> Loc::getMessage("CP_SMARTFORM_F_W_ZIP"),"TYPE" => "S","TOOLTIP" => "","IS_REQUIRED" => "N","VALIDRULE" => "","VALID_ERROR_MSG" => "","HIDE_FIELD" => "N", "ACTIVE" => "Y"),
			"WORK_STREET" => array("ORIGINAL_TITLE" => Loc::getMessage("CP_SMARTFORM_F_W_STREET"), "TITLE"=> Loc::getMessage("CP_SMARTFORM_F_W_STREET"),"TYPE" => "S","TOOLTIP" => "","IS_REQUIRED" => "N","VALIDRULE" => "","VALID_ERROR_MSG" => "","HIDE_FIELD" => "N", "ACTIVE" => "Y"),
			"WORK_MAILBOX" => array("ORIGINAL_TITLE" => Loc::getMessage("CP_SMARTFORM_F_W_MAILBOX"), "TITLE"=> Loc::getMessage("CP_SMARTFORM_F_W_MAILBOX"),"TYPE" => "S","TOOLTIP" => "","IS_REQUIRED" => "N","VALIDRULE" => "","VALID_ERROR_MSG" => "","HIDE_FIELD" => "N", "ACTIVE" => "Y"),
			"WORK_NOTES" => array("ORIGINAL_TITLE" => Loc::getMessage("CP_SMARTFORM_F_W_NOTES"), "TITLE"=> Loc::getMessage("CP_SMARTFORM_F_W_NOTES"),"TYPE" => "S","TOOLTIP" => "","IS_REQUIRED" => "N","VALIDRULE" => "","VALID_ERROR_MSG" => "","HIDE_FIELD" => "N", "ACTIVE" => "Y"),

			"CAPTCHA" => Array("IS_REQUIRED" => true, "ORIGINAL_TITLE" => Loc::getMessage("CP_SMARTFORM_F_CAPTCHA_TITLE"), "TITLE"=> Loc::getMessage("CP_SMARTFORM_F_CAPTCHA_TITLE"), "TOOLTIP" => Loc::getMessage("CP_SMARTFORM_F_CAPTCHA_TOOLTIP_TITLE"),"TYPE" => "CAPTCHA","VALIDRULE" => "","VALID_ERROR_MSG" => "","HIDE_FIELD" => "N", "ACTIVE" => "Y"),

		);

		$arDefaultUserFields = array();
		$arUserFields = $this->GetUserField();
		if($arUserFields) {
			foreach($arUserFields as $key => $value) {
				$arDefaultUserFields[$key] = array(
					"ORIGINAL_TITLE" => strlen($value['LIST_COLUMN_LABEL']) > 0 ? $value['LIST_COLUMN_LABEL'] : $value['FIELD_NAME'],
					"TITLE" => strlen($value['LIST_COLUMN_LABEL']) > 0 ? $value['LIST_COLUMN_LABEL'] : $value['FIELD_NAME'],
					"TYPE" => "USER_FIELD",
					"USER_TYPE" => $value['USER_TYPE'],
					"IS_REQUIRED" => $value['MANDATORY'] == "Y" ? true : false,
					"TOOLTIP" => $value['HELP_MESSAGE'],
					"VALID_ERROR_MSG" => $value['ERROR_MESSAGE'],
					"VALIDRULE" => isset($value['SETTINGS']['REGEXP']) ? $value['SETTINGS']['REGEXP'] : '',
					"HIDE_FIELD" => "N", "ACTIVE" => "Y",
				);

				if(isset($value['SETTINGS']['REGEXP']) && strlen($value['SETTINGS']['REGEXP']) > 0) {
					$arDefaultUserFields[$key]['REGEXP'] = true;
				}

				if('enum' != $value['USER_TYPE']['BASE_TYPE'])
					continue;

				/** @var \CUserFieldEnum $ufClassEnum */
				$ufClassEnum = $value['USER_TYPE']['CLASS_NAME'];
				$resEnum = $ufClassEnum::GetList(array(), array(
					"ID" => $value["ID"],
				));

				while($arE = $resEnum->GetNext()) {
					$arDefaultUserFields[$key]['ITEMS'][] = array(
						'ID' => $arE['ID'],
						'VALUE' => $arE['VALUE']
					);
				}
			}
		}
		$db_events = GetModuleEvents("main", "OnBeforeGetDefaultValueForm{$this->eventPostfix}");
		while($arEvent = $db_events->Fetch())
			ExecuteModuleEventEx($arEvent, array(&$arDefaultFields, &$arDefaultUserFields));

		return array(
			"FIELDS" => $arDefaultFields,
			"USER_FIELD" => $arDefaultUserFields
		);
	}

	/**
	 * получить список доступных пользовательских полей
	 *
	 * @return array - список доступных пользовательских полей
	 */
	public function GetUserField() {
		$arUserFields = $GLOBALS["USER_FIELD_MANAGER"]->GetUserFields("USER", 0, LANGUAGE_ID);
		if($arUserFields) {
			foreach($arUserFields as $key => $value) {
				$value['ORIGINAL_FIELD_NAME'] = $value['FIELD_NAME'];
				$value['FIELD_NAME'] = "FIELDS[" . $value['FIELD_NAME'] . "]";
				$arDefaultUserFields[$key] = $value;
			}
		}
		return !isset($arDefaultUserFields) ? array() : $arDefaultUserFields;
	}

	/**
	 * определяем пользовательское поле это или нет
	 **/
	private function IsUserField($code) {
		return substr($code,0,strlen($this->bxPrefix)) == $this->bxPrefix ? true : false;
	}

	public function getFileListRequest($code = false,$multiple = false) {
		$arFile = false;
		$context = Main\HttpApplication::getInstance()->getContext();
		$request = $context->getRequest();

		/** @var array $arDelFiles */
		$arDelFiles = $request->get('del_FIELDS');

		if(isset($arDelFiles[$code])) {
			$arDelFiles = $arDelFiles[$code];
			if(!$multiple && (int)$arDelFiles > 0)
				$arFile = array('del' => 'Y');
			elseif($multiple && is_array($arDelFiles) && !empty($arDelFiles)) {
				foreach($arDelFiles as $id)
					$arFile[$id] = array('del' => 'Y');
			}
		}

		$arFilesList = $request->getFile('FIELDS');
		if(null === $arFilesList)
			return $arFile;

		$error = $arFilesList['error'][$code];
		if(!$multiple) {
			if($error == 4)
				return $arFile;
			elseif($error > 0)
				throw new Main\ArgumentException(Loc::getMessage("CP_SMARTFORM_UPLOAD_FILE_ERROR_CODE_{$error}"), 'UPLOAD_FILE');

			$arFile = array(
				'name' => $arFilesList['name'][$code],
				'type' => $arFilesList['type'][$code],
				'tmp_name' => $arFilesList['tmp_name'][$code],
				'size' => $arFilesList['size'][$code],
			);
		}
		else {
			foreach($error as $index => $errorCode) {
				if($errorCode == 4)
					continue;
				elseif($errorCode > 0)
					throw new Main\ArgumentException(Loc::getMessage("CP_SMARTFORM_UPLOAD_FILE_ERROR_CODE_{$error}"), 'UPLOAD_FILE');

				$arFile['n'.$index] = array(
					'name' => $arFilesList['name'][$code][$index],
					'type' => $arFilesList['type'][$code][$index],
					'tmp_name' => $arFilesList['tmp_name'][$code][$index],
					'size' => $arFilesList['size'][$code][$index],
				);
			}
		}

		return (empty($arFile) ? array() : $arFile);
	}

	public function prepaData(&$value,$arField,$code) {
		global $DB;

		$value = is_array($value) ? array_diff($value, array("")) : $value;
		if (empty($value)) {
			return $value;
		}
		$arValue = is_array($value) ? $value : array($value);
		foreach($arValue as $index => &$itVal) {
			switch($arField['TYPE']) {
				case 'USER_FIELD':
					if($arField['USER_TYPE_ID'] == "enumeration") {
						if(!is_array($itVal) && $itVal == '0')
							$itVal = false;
						if(is_array($value) && in_array('0',$value)) {
							$itVal = false;
						}
					}
					return true;
					break;

				case 'DATE':
					if (strlen($itVal) > 0 && !$DB->IsDate($itVal, FORMAT_DATETIME, LANGUAGE_ID))
						return false;
					break;

				case 'E':
					if(is_array($itVal) && !isset($itVal['VALUE']))
						unset($value[$index]);
					break;
				case 'G':
				case 'L':
					//if($arField['ITEMS'])
					break;

				case 'F':
					$itVal = IntVal($itVal);
					if ($itVal > 0)
						$itVal = \CFile::MakeFileArray($itVal);
					else
						return false;
					break;

				case 'T':
					if(is_array($itVal) && !isset($itVal['VALUE'])) {

					}
					else {
						$itVal = trim(htmlspecialcharsbx($itVal));
					}
					break;
				case 'N':
					$itVal = IntVal($itVal);
					break;

				default:
					$itVal = trim(htmlspecialcharsbx($itVal));
			}
		}

		if('ACTIVE' == $code) {
			$value = (is_array($value) && in_array('Y',$value) ? 'Y' : '');
		}

		if(
			$this->arParams['USE_SERVER_VALIDATE'] == "Y" &&
			$arField['TYPE'] != "USER_FIELD" && strlen($arField['VALIDRULE']) > 0 && !preg_match($arField['VALIDRULE'], $value)
		) {
			$this->LAST_ERROR[$code] = strlen($arField['VALID_ERROR_MSG']) > 0 ? htmlspecialchars_decode($arField['VALID_ERROR_MSG']) : Loc::getMessage("CP_SMARTFORM_E_VALIDATION_FIELD" ,array("#FIELD#" => $arField['TITLE']));
			return false;
		}

		return true;
	}

	public function GetRequiredUserField(&$arIsField = array()){
		global $USER_FIELD_MANAGER;

		$arFields = array();
		$rsData = \CUserTypeEntity::GetList(array(),array("ENTITY_ID" => "USER","MANDATORY" => "Y"));
		while($arRes = $rsData->Fetch())
			if(!array_key_exists($arRes['CODE'], $arIsField))
				$arFields[$arRes['FIELD_NAME']] = $arRes;

		if(empty($arFields)) return false;

		foreach ($arFields as $CODE => $arField) {
			$arSettings = $USER_FIELD_MANAGER->PrepareSettings(0,$arField);
			if(isset($arSettings['SMARTFORM_VALUE']))
				$arIsField[$CODE] = $arField['MULTIPLE'] == "Y" ? array($arSettings['SMARTFORM_VALUE']) : $arSettings['SMARTFORM_VALUE'];
		}

		return $arFields;
	}

	public function __generationPassword() {
		$def_group = \COption::GetOptionString("main", "new_user_registration_def_group", "");
		if($def_group!="")
			$arPolicy = \CUser::GetGroupPolicy(explode(",", $def_group));
		else
			$arPolicy = \CUser::GetGroupPolicy(array());

		$password_min_length = intval($arPolicy["PASSWORD_LENGTH"]);
		if($password_min_length <= 0)
			$password_min_length = 6;

		$pass = randString($password_min_length + 5, array(
			"abcdefghijklnmopqrstuvwxyz",
			"ABCDEFGHIJKLNMOPQRSTUVWXYZ",
			"0123456789",
			",.<>/?;:[]{}\\|~!@#\$%^&*()-_+=",
		));

		return $pass;
	}

	public function getFormID() {
		return $this->formID;
	}

	public function setFormID($id = null) {
		if(null === $id)
			$this->formID = md5(serialize($this->arParams) . $this->GetName());
		else
			$this->formID = $id;
	}

	public function GetParametrsFieldInput($title = '',$btnTitle = '',$groupName = 'BASE',$path = null) {

		/**
		 * Редактирование из визуального редактора не работает (#45950)
		 */
		if (!isset($_REQUEST['component_params_manager']))
		{
			$arDefaultFields = $this->GetDefaultFields();
			$arData = array_merge($arDefaultFields['FIELDS'],$arDefaultFields['USER_FIELD']);
			$componentPath = $path != null ? $path : $this->getPath();

			/**
			 * TODO тут надо подумать, может как то проще можно получить путь до шаблона компонента
			 */
			$this->initComponentTemplate();
			if($this->__template instanceof \CBitrixComponentTemplate)
				$templatePath = $_SERVER['DOCUMENT_ROOT'] . $this->__template->__folder;
			else
				$templatePath = null;

			$arFieldTemplates = $this->getFieldTemplate($templatePath);
			if(!empty($arFieldTemplates)) {
				foreach($arData as $code => &$f) {
					if(isset($f['USER_TYPE']) && strlen($f['USER_TYPE']['USER_TYPE_ID']) > 0)
						$type = $this->getTemplateType($f['USER_TYPE']['USER_TYPE_ID']);
					else
						$type = $this->getTemplateType($f['TYPE']);
					$f['TEMPLATES'] = isset($this->templateMap[$type]) ? $this->templateMap[$type] : false;
				}
			}
		}

		return array(
			'NAME' => strlen($title) > 0 ? $title : Loc::getMessage('CP_SMARTFORM_PARAM_TITLE'),
			'TYPE' => 'CUSTOM',
			'JS_COMPONENT_PATH' => $componentPath,
			'JS_FILE' => \CUtil::GetAdditionalFileURL(CITRUS_FORMS_BX_ROOT . '/js/citrus.forms/btn.js', true),
			'JS_EVENT' => isset($_REQUEST['component_params_manager'])
				? 'showSettingsBtnUnavailable'
				: 'showSettingsBtn',
			'JS_DATA' => isset($_REQUEST['component_params_manager'])
				? null
				: $arData,
			'PARENT' => $groupName,
			'MESSAGE' => Loc::loadLanguageFile(__DIR__ . "/settings.php")
		);
	}

	public function GetComponentParametrs($arGroup = array(),$arCurrent = array()) {
		$arComponentParams = self::GetDefaultComponentParametrs($arGroup,$arCurrent);

		$arComponentParams['PARAMETERS']['FIELDS'] = $this->GetParametrsFieldInput(
			Loc::getMessage('PAR_SMARTFORM_FIELDS_NAME'),
			Loc::getMessage('PAR_SMARTFORM_FIELD_DATA_SET'),
			'FIELDS'
		);

		return $arComponentParams;
	}

	/**
	 * Возвращает абсолютный путь к папке, содержащей шаблоны полей
	 *
	 * @return string
	 */
	protected static function getFieldTemplatesDir()
	{
		return __DIR__;
	}

	public function getFieldTemplate(\CBitrixComponentTemplate $template = null) {
		$path = static::getFieldTemplatesDir();

		if(null === self::$arFieldTemplates)
			self::$arFieldTemplates = $this->loadFieldTemplate($path . '/field');

		$arTemplate = self::$arFieldTemplates;
		if(isset($template)) {
			$rSub = $this->loadFieldTemplate($_SERVER['DOCUMENT_ROOT'] . $template->GetFolder() . '/field');
			$arTemplate = array_merge_recursive($arTemplate,$rSub);
		}

		$arResult = array();
		foreach($arTemplate as $type => $list) {
			if(!isset($arResult[$type])) {
				$arResult[$type] = array();
				$this->templateMap[$type] = array();
			}

			/**
			 * @var $dir Main\IO\Directory;
			 */
			foreach($list as $dir) {
				$templateName = $dir->getName();
				$arResult[$type][$templateName] = $dir;

				$descFile = $dir->getPhysicalPath() . '/.description.php';
				if(!Main\IO\File::isFileExists($descFile))
					$this->templateMap[$type][$templateName] = array(
						'title' => $dir->getName()
					);
				else
					$this->templateMap[$type][$templateName] = require $descFile;
			}
		}

		return $arResult;
	}

	protected function loadFieldTemplate($path) {
		$arTemplate = array();

		$dir = new Main\IO\Directory(Main\IO\Path::normalize($path));
		if(false === $dir->isExists())
			return $arTemplate;

		$arChaild = $dir->getChildren();

		foreach($arChaild as $dir) {
			if ($dir->isDirectory())
			{
				/** @var Main\IO\Directory $dir*/
				$type = $dir->getName();
				$arTemplate[$type] = $dir->getChildren();
			}
		}

		return $arTemplate;
	}

	protected function prepeaParamsBeforeOpenSetting(array $arFields) {
		$arAllow = array_flip($this->arAllowField);

		foreach($arFields as &$f) {
			$f = array_intersect_key($f,$arAllow);
		}
		return $arFields;
	}

	public function getTemplateType($type) {
		$returnType = 'unknown';
		if(null === $type)
			return $returnType;

		foreach(self::$arFTemplateType as $rtype => $arVariant){
			if(!in_array($type,$arVariant))
				continue;
			return $rtype;
		}

		return $returnType;
	}

	protected function parseParamFieldValue($strValue) {
		if(is_array($strValue))
			return $strValue;

		if(
			(substr($strValue, 0, 2) == "={" || substr($strValue, 0, 2) == "Y{")
			&& substr($strValue, -1, 1)=="}" && strlen($strValue)>3
		){
			$strValue = eval('return ' . substr($strValue, 2, -1) . ';');
		}
		return $strValue;
	}

	public static function GetDefaultComponentParametrs($arGroup = array(),$arCurrent = array()) {
		if(empty($arGroup))
			return false;
		else
			$arGroup = array_flip($arGroup);

		$arComponentParam  = array(
			"GROUPS" => array(
				"BASE" => array(
					"NAME" => Loc::getMessage("PAR_SMARTFORM_G_BASE"),
					"SORT" => 100
				),
				"FIELDS" => array(
					"NAME" => Loc::getMessage("PAR_SMARTFORM_G_FIELDS"),
					"SORT" => 100
				),
				"MAIL_SETTINGS" => array(
					"NAME" => Loc::getMessage("PAR_SMARTFORM_G_SETTINGS"),
					"SORT" => 200
				),
				"VISUAL" => array(
					"NAME" => Loc::getMessage("PAR_SMARTFORM_G_VISUAL"),
					"SORT" => 300
				),
				"RECAPTCHA" => array(
					"NAME" => Loc::getMessage("PAR_SMARTFORM_G_RECAPTCHA"),
					"SORT" => 400
				),
				'MESSAGES' => array(
					"NAME" => Loc::getMessage("PAR_SMARTFORM_G_MESSAGES"),
					"SORT" => 500
				),
				/*'USER_CONSENT' => array(

				)*/
			),
			"PARAMETERS" => array(
				'FORM_TITLE' => array(
					"PARENT" => "MESSAGES",
					"NAME" => Loc::getMessage("PAR_SMARTFORM_F_FORM_TITLE"),
					"TYPE" => "STRING",
				),
				'SUCCESS_TEXT' => array(
					"PARENT" => "MESSAGES",
					"NAME" => Loc::getMessage("PAR_SMARTFORM_F_SUCCESS_TEXT"),
					"TYPE" => "STRING",
				),
				'ERROR_TEXT' => array(
					"PARENT" => "MESSAGES",
					"NAME" => Loc::getMessage("PAR_SMARTFORM_F_ERROR_TEXT"),
					"TYPE" => "STRING",
				),
				'BUTTON_TITLE' => array(
					"PARENT" => "MESSAGES",
					"NAME" => Loc::getMessage("PAR_SMARTFORM_F_BUTTON_TITLE"),
					"TYPE" => "STRING",
				),
				'BEFORE_FORM_TOOLTIP' => array(
					"PARENT" => "MESSAGES",
					"NAME" => Loc::getMessage("PAR_SMARTFORM_F_BEFORE_FORM_TOOLTIP"),
					"TYPE" => "STRING",
				),
				'AFTER_FORM_TOOLTIP' => array(
					"PARENT" => "MESSAGES",
					"NAME" => Loc::getMessage("PAR_SMARTFORM_F_AFTER_FORM_TOOLTIP"),
					"TYPE" => "STRING",
				),
				'USE_SERVER_VALIDATE' => array(
					"PARENT" => "BASE",
					"NAME" => Loc::getMessage("PAR_SMARTOFRM_USE_SERVER_VALIDATE"),
					"TYPE" => "CHECKBOX",
					"DEFAULT" => "N",
				),
				"SEND_MESSAGE" => array(
					"PARENT" => "MAIL_SETTINGS",
					"NAME" => Loc::getMessage("PAR_SMARTFORM_SEND_MESSAGE_SET"),
					"TYPE" => "CHECKBOX",
					"DEFAULT" => "N",
					"REFRESH" => "Y"
				),
				'SAVE_SESSION' => array(
					"PARENT" => "BASE",
					"NAME" => Loc::getMessage("PAR_SMARTFORM_SAVE_IN_SESSION"),
					"TYPE" => "CHECKBOX",
					"DEFAULT" => "Y"
				),
				'FORM_ID' => array(
					"PARENT" => "BASE",
					"NAME" => Loc::getMessage("PAR_SMARTFORM_FORM_ID"),
					"TYPE" => "STRING",
					"DEFAULT" => md5(time())
				),
				'USE_GOOGLE_RECAPTCHA' => array(
					"PARENT" => "RECAPTCHA",
					"NAME" => Loc::getMessage("PAR_SMARTFORM_USE_GOOGLE_RECAPTCHA"),
					"TYPE" => "CHECKBOX",
					"DEFAULT" => "N",
					"REFRESH" => "Y"
				),
				'AJAX' => array(
					"PARENT" => "BASE",
					"NAME" => Loc::getMessage("PAR_SMARTFORM_AJAX"),
					"TYPE" => "CHECKBOX",
					"DEFAULT" => "Y",
				),
				"AGREEMENT_LINK"=>array(
					"NAME" => Loc::getMessage("PAR_SMARTFORM_AGREEMENT_LINK"),
					"TYPE" => "STRING",
					'PARENT' => 'ADDITIONAL',
					'DEFAULT' => ""
				),
				"FORM_CLASS" => array(
					"NAME" => Loc::getMessage("PAR_SMARTFORM_FORM_WRAPPER_CLASS"),
					"TYPE" => "STRING",
					'PARENT' => 'VISUAL',
					"DEFAULT" => "",
				),
				/*"AGREEMENT_FIELDS" => array(
					"NAME" => Loc::getMessage('PAR_SMARTFORM_FORM_AGREEMENT_FIELDS'),
					"PARENT" => "USER_CONSENT",
					"TYPE" => "STRING",
					'SORT' => 1000,
					"DEFAULT" => Loc::getMessage('PAR_SMARTFORM_FORM_AGREEMENT_FIELDS_DEFAULT'),
				)*/
			)
		);

		$arComponentParam['GROUPS'] = array_intersect_key($arComponentParam['GROUPS'],$arGroup);
		if(empty($arComponentParam['GROUPS']))
			return false;

		foreach($arComponentParam['PARAMETERS'] as $code => $val) {
			if(!array_key_exists($val['PARENT'],$arComponentParam['GROUPS']))
				unset($arComponentParam['PARAMETERS'][$code]);
		}

		if(
			isset($arCurrent['USE_GOOGLE_RECAPTCHA'])
			&& 'Y' == $arCurrent['USE_GOOGLE_RECAPTCHA']
		) {
			$arComponentParam['PARAMETERS']['GOOGLE_RECAPTCHA_PUBLIC_KEY'] = array(
				"PARENT" => "RECAPTCHA",
				"NAME" => GetMessage("PAR_SMARTFORM_GOOGLE_RECAPTCHA_PUBLIC_KEY"),
				"TYPE" => "STRING",
				"DEFAULT" => ""
			);
			$arComponentParam['PARAMETERS']['GOOGLE_RECAPTCHA_PRIVATE_KEY'] = array(
				"PARENT" => "RECAPTCHA",
				"NAME" => GetMessage("PAR_SMARTFORM_GOOGLE_RECAPTCHA_PRIVATE_KEY"),
				"TYPE" => "STRING",
				"DEFAULT" => ""
			);
		}
//		$arComponentParam['PARAMETERS']["USER_CONSENT"] = array();


		if($arCurrent['SEND_MESSAGE'] == "Y") {
			$rsEventType = \CEventType::GetList(array( "LID" => LANGUAGE_ID));
			$arMailEvent = array();
			while($arEvent = $rsEventType->Fetch()) {
				$arMailEvent[$arEvent['EVENT_NAME']] = "[".$arEvent["EVENT_NAME"]."] ".$arEvent["NAME"];
			}

			$arMailTemplate = array(
				0 => Loc::getMessage('PAR_SMARTFORM_ALL_MAILE_EVENT_TEMPLATE')
			);

			if(isset($arCurrent['MAIL_EVENT']) && strlen($arCurrent['MAIL_EVENT']) > 0) {
				$arMailTemplate = array();
				$rsMess = \CEventMessage::GetList($by = "site_id", $order = "desc", Array("TYPE_ID" => $arCurrent['MAIL_EVENT'], "ACTIVE" => "Y"));
				while($arr = $rsMess->Fetch())
					$arMailTemplate[$arr['ID']] = "[" . $arr['ID'] ."]" . $arr['SUBJECT'];
			}

			$arComponentParam['PARAMETERS']['MAIL_EVENT'] = array(
				"PARENT" => "MAIL_SETTINGS",
				"NAME" => Loc::getMessage("PAR_SMARTFORM_MAIL_EVENT_SET"),
				"TYPE" => "LIST",
				"MULTIPLE" => "N",
				"VALUES" => $arMailEvent,
				"REFRESH" => "Y"
			);
			$arComponentParam['PARAMETERS']['MAILE_EVENT_TEMPLATE'] = array(
				"PARENT" => "MAIL_SETTINGS",
				"NAME" => Loc::getMessage("PAR_SMARTFORM_MAILE_EVENT_TEMPLATE_SET"),
				"TYPE" => "LIST",
				"MULTIPLE" => "Y",
				"VALUES" => $arMailTemplate,
			);
			$arComponentParam['PARAMETERS']['SEND_IMMEDIATE'] = array(
				"PARENT" => "MAIL_SETTINGS",
				"NAME" => Loc::getMessage("PAR_SMARTFORM_SEND_IMMEDIATE"),
				"TYPE" => "CHECKBOX",
				"DEFAULT" => "N",
			);
			$arComponentParam['PARAMETERS']['EMAIL_TO_VALUE'] = array(
				"PARENT" => "MAIL_SETTINGS",
				"NAME" => Loc::getMessage("PAR_SMARTFORM_EMAIL_TO"),
				"TYPE" => "STRING",
				"DEFAULT" => ""
			);
			$arComponentParam['PARAMETERS']['EMAIL_SUBJECT'] = array(
				"PARENT" => "MAIL_SETTINGS",
				"NAME" => Loc::getMessage("PAR_SMARTFORM_EMAIL_SUBJECT"),
				"TYPE" => "STRING",
				"DEFAULT" => ""
			);
		}

		return $arComponentParam;
	}

	public function getComponentPathByClassName($componentName) {
		$path2Comp = \CComponentEngine::MakeComponentPath($componentName);
		return getLocalPath("components".$path2Comp);
	}

	public function getFieldType($field) {
		if(isset($field['USER_TYPE']) && is_array($field['USER_TYPE']))
			$field['USER_TYPE'] = $field['USER_TYPE']['BASE_TYPE'];

		return $field['USER_TYPE'] ? $field['USER_TYPE'] : $field['TYPE'];
	}

	/**
	 * Подключает шаблон поля
	 *
	 * @param $field - данные поля
	 * @param array $templateData
	 */
	public function includeFieldTemplate(&$field, &$templateData = null) {
		if (null === $templateData) {
			$templateData = [];
		}
		if(null === $field || !is_array($field))
			throw new Main\SystemException(Loc::getMessage("CITRUS_SMARTFORM_MISSING_FIELD_CODE"));

		$field['BASE_TYPE'] = $this->getFieldType($field);
		$fieldType = $this->getTemplateType($field['BASE_TYPE']);

		$arFTemplate = $this->getFieldTemplate($this->getTemplate());
		if (!isset($arFTemplate[$fieldType])){
			echo 'Template not found for field: ';
			echo '<pre>';
			print_r($field);
			echo '</pre>';
			return;
		}

		if(!isset($field['TEMPLATE_ID']))
			$field['TEMPLATE_ID'] = self::DEFAULT_TEMPLATE_ID;

		if(isset($field['TEMPLATE_ID']) && isset($arFTemplate[$fieldType][$field['TEMPLATE_ID']]))
			$templateField = $arFTemplate[$fieldType][$field['TEMPLATE_ID']];
		else
			$templateField = $arFTemplate[$fieldType]['.default'];

		if (!($templateField instanceof Main\IO\Directory))
			return;

//			$documentRoot = Main\SiteTable::getDocumentRoot(SITE_ID);
//			$AssetsPath = str_replace($documentRoot, '', $templateField->getPhysicalPath());

		// проблема с симлинками
		$AssetsPath = getLocalPath("/components/citrus.forms/base/field/{$fieldType}/{$field['TEMPLATE_ID']}/");

		$field['TEMPLATE'] = array(
			"TYPE" => $fieldType,
			"PATH" => $templateField->getPhysicalPath(),
			"ASSETS" => array(
				"PATH" => $AssetsPath
			)
		);

		global $APPLICATION;

		if(!is_array($field['TEMPLATE']) || strlen($field['TEMPLATE']['PATH']) <= 0)
			throw new Main\SystemException(Loc::getMessage("CITRUS_SMARTFORM_CANT_FIND_FIELD_TEMPLATE"));

		if(Main\IO\File::isFileExists($field['TEMPLATE']['PATH'] . '/style.css')) {
			$this->getTemplate()->addExternalCss($field['TEMPLATE']['ASSETS']['PATH'] . '/style.css');
		}

		if(Main\IO\File::isFileExists($field['TEMPLATE']['PATH'] . '/script.js')){
			$this->getTemplate()->addExternalJs($field['TEMPLATE']['ASSETS']['PATH'] . '/script.js');
		}

		/**
		 * Данные переменные используются в подключаемом шаблоне
		 *
		 * $arResult - масси с данными
		 * $arParams - содержит настройки компонента
		 * $component - экземпляр класса, сам компонент
		 * $fieldInfo - данные по текущему полю
		 * $templateData - сохраняются в кеше и передаются в component_epilog.php шаблона
		 */
		$arResult = $this->arResult;
		$arParams = $this->arParams;
		$component = $this;
		$fieldInfo = &$field;
		if (!is_array($templateData))
		{
			$templateData = [];
		}
		if (!isset($templateData['cjscore']) || !is_array($templateData['cjscore']))
		{
			$templateData['cjscore'] = [];
		}

		if (Main\IO\File::isFileExists($field['TEMPLATE']['PATH'] . '/template.php'))
		{
			include $field['TEMPLATE']['PATH'] . '/template.php';
		}

		unset($arResult, $arParams, $component, $fieldInfo);
	}

	protected function laodAction() {
		if(
			$this->arParams['EDIT_ELEMENT'] == "Y" && $this->arParams['ELEMENT_ID'] > 0
		) {
			$elementID = (int)$this->arParams['ELEMENT_ID'];
			$isUpdate = \CUser::GetByID($elementID)->fetch();
			if($isUpdate)
				$this->action = self::ACTION_UPDATE;
			else
				$this->action = self::ACTION_ADD;
		}
		else {
			$this->action = self::ACTION_ADD;
		}
	}

	public function isUpdateAction() {
		return ($this->action == self::ACTION_UPDATE);
	}

	public function isAddAction() {
		return ($this->action == self::ACTION_ADD);
	}


	/**
	 * Возвращает ID инфоблока на текущем сайте по его коду
	 *
	 * @param string $code Символьный код инфоблока
	 * @param int $siteId ID сайта (не обязательный)
	 * @param bool $raiseException
	 * @return int ID инфоблока
	 * @throws Main\ArgumentException
	 * @throws \ErrorException
	 */
	public static function getIblock($code, $siteId = null, $raiseException = true)
	{
		$iblocks = self::getIBlockIds($siteId);

		if ($code == "")
		{
			throw new Main\ArgumentException("Empty \$code parameter", 'code');
		}
		if (array_key_exists($code, $iblocks))
		{
			return $iblocks[$code];
		}
		if ($raiseException)
		{
			throw new \ErrorException("IBlock „{$code}“ not found");
		}
		return null;
	}

	/**
	 * Возвращает массив с соответствиями символьных кодов инфоблоков их ID (для текущего сайта)
	 *
	 * @param int $siteId ID сайта (не обязательный)
	 * @return array Ассоциативный массив, где ключи — символьные коды инфоблоков, а значения — их ID
	 * @throws \Exception
	 */
	public static function getIBlockIds($siteId = null)
	{
		if (null === $siteId)
		{
			$siteId = defined('WIZARD_SITE_ID')
				? WIZARD_SITE_ID
				: defined('ADMIN_SECTION') ? null : SITE_ID;
		}

		if (null === static::$iblocks || !isset(static::$iblocks[$siteId]))
		{
//			static::$iblocks = Cache::remember(__METHOD__ . $siteId, 30 * 60 * 24, function () use ($siteId)
//			{
				if (!Main\Loader::includeModule("iblock"))
				{
					throw new \Exception(Loc::getMessage("CITRUS_AREALTY_IBLOCK_MODULE_NOT_FOUND"));
				}

				$iblocks = array();
				$iblocks[$siteId] = array();
				$filter = Array("!CODE" => false, "CHECK_PERMISSIONS" => "N");
				if ($siteId)
				{
					$filter["SITE_ID"] = $siteId;
				}
				$dbIblock = \CIBlock::GetList(
					Array(),
					$filter,
					$bIncCnt = false
				);
				while ($iblock = $dbIblock->Fetch())
				{
//					Cache::registerIblockCacheTag($iblock['ID']);
					if (array_key_exists($iblock["CODE"], $iblocks) && !is_array($iblocks[$iblock["CODE"]]))
					{
						$iblocks[$siteId][$iblock["CODE"]] = array($iblocks[$iblock["CODE"]]);
						$iblocks[$siteId][$iblock["CODE"]][] = (int)$iblock["ID"];
					}
					else
					{
						$iblocks[$siteId][$iblock["CODE"]] = (int)$iblock["ID"];
					}
				}

				static::$iblocks[$siteId] = $iblocks[$siteId];

//				return $iblocks;
//			}, __FUNCTION__);
		}

		return static::$iblocks[$siteId];
	}

	public function getMailField($newID, $arUpdateFieldValues) {
		$arMailFields = array();

		/**
		 * добавим возможность использования ID добавленного элемента в шаблоне почтового события
		 */
		if (isset($newID) && $newID)
			$arMailFields['ID'] = $newID;

		$arMailFields = array_merge($arMailFields, $arUpdateFieldValues);

		$fields = $this->arResult['ITEMS'];
		foreach ($arMailFields as $msgCode => $msgValue) {
			if (!array_key_exists($msgCode, $fields) || strlen($msgValue) <= 0) continue;

			$item = $fields[$msgCode];
			$title = strlen($item['TITLE']) > 0 ? $item['TITLE'] : $item['ORIGINAL_TITLE'];
			$arMailFields['MESSAGE'][] = Loc::getMessage('CP_SMARTFORM_F_ADD_FORM_MAIL_MESSAGE_TEMPLATE',
				array("#TITLE#" => $title, "#VALUE#" => $msgValue));
		}

		if (isset($arMailFields['MESSAGE']) && !empty($arMailFields['MESSAGE'])) {
			$arMailFields['MESSAGE'] = implode("\n", $arMailFields['MESSAGE']);
		}

		$bxSendMail = true;
		$events = GetModuleEvents("main", "OnPrepeaDataBeforeSendEmail");
		while ($arEvent = $events->Fetch()) {
			$bxSendMail = ExecuteModuleEventEx($arEvent, array(&$arMailFields));
		}

		return true === $bxSendMail || null === $bxSendMail ? $arMailFields : false;
	}

	public static function getParentComponentName()
	{
		if (property_exists(get_called_class(), 'parentComponentName'))
		{
			return static::$parentComponentName;
		}
		throw new Main\SystemException("Cant't find parent component name");
	}
}
