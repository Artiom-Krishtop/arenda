<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc,
	Bitrix\Main\SystemException,
	Bitrix\Main\ArgumentException,
	Bitrix\Iblock
	;

Loc::loadMessages(__FILE__);

$parenComponentName = "citrus:smartform";
CBitrixComponent::includeComponentClass($parenComponentName);

class CBCitrusIBAddFormComponent extends CBCitrusSmartForm {
	var $bxPrefix = "PROPERTY_";
	var $eventPostfix = "CitrusIBAddForm";
	var $LAST_ERROR = false;
	var $arParams = null;
	var $arListType = array('G', 'L');
	var $iblockId = false;
	var $defaultField = array();

	public $__arCache;

	protected $fields;

	/**
	 * Позволяет обработать список выходных параметров
	 *
	 * @param $arParams - массив с параметрами компонента
	 *
	 * @return array - массив с параметрами компонента
	 */
	public function onPrepareComponentParams($arParams) {
		$arParams = parent::onPrepareComponentParams($arParams);

		if ((int)$arParams['IBLOCK_ID'] <= 0 && strlen($arParams['IBLOCK_CODE']) > 0)
		{
			$arParams['IBLOCK_ID'] = self::getIblock($arParams['IBLOCK_CODE']);
		}

		$this->setIblockId((int)$arParams['IBLOCK_ID']);

		return $arParams;
	}

	/** @inheritdoc */
	protected static function getClassPath() {
		return __DIR__;
	}

	/**
	 * @return bool|string
	 */
	public function executeComponent($isAjax = null) {
		global $APPLICATION;

		if(false === \Bitrix\Main\Loader::includeModule('iblock')) {
			ShowError(Loc::getMessage('CP_IBADDFORM_E_IBLOCK_MODULE_NOT_INSTALL'));
			return false;
		}

		if($this->getIblockId() <= 0) {
			ShowError(Loc::getMessage('CP_IBADDFORM_E_IBLOCK_ID_EMPTY'));
			return false;
		}

		$this->setFormID($this->arParams['FORM_ID']);

		if('Y' == $this->arParams['SAVE_SESSION'])
			$this->saveComponentParams($this->getFormID());

		$this->arResult = array(
			"IBLOCK_ID" => $this->getIblockId(),
			"FORM_ID" => $this->getFormID(),
			"FORM_ACTIONS" => $this->arParams['FORM_ACTIONS'],
			"ITEMS" => $this->arParams['FIELDS']
		);

		$this->addMissingField();

		$context = Bitrix\Main\HttpApplication::getInstance()->getContext();
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
					throw new ArgumentException(Loc::getMessage('CP_SMARTFORM_E_VALIDATE_ERROR'),'VALIDATE_ERROR');

				// FIX for upload PREVIEW_PICTURE
				if (!empty($_FILES["FIELDS"]["tmp_name"]["PREVIEW_PICTURE"]))
				{
					$tmpPic = \CFile::MakeFileArray($_FILES["FIELDS"]["tmp_name"]["PREVIEW_PICTURE"]);
					$tmpPic["name"] .= ".jpg";
					$arField["PREVIEW_PICTURE"] = $tmpPic;
				}
				// FIX for upload DETAIL_PICTURE
				if (!empty($_FILES["FIELDS"]["tmp_name"]["DETAIL_PICTURE"]))
				{
					$tmpPic = \CFile::MakeFileArray($_FILES["FIELDS"]["tmp_name"]["DETAIL_PICTURE"]);
					$tmpPic["name"] .= ".jpg";
					$arField["DETAIL_PICTURE"] = $tmpPic;
				}
				
				/**
				 * Создадим запись в инфоблоке или обновим её если требуется
				 */
				$ID = $this->executeComponentTask($arField);
				if(false === $ID)
					throw new ArgumentException(Loc::getMessage('CP_SMARTFORM_E_UPDATE_ERROR'),'UPDATE_ERROR');

				if($this->arParams['SEND_MESSAGE'] == "Y") {
					$this->SendSuccessFormMessage($arField,$ID);
				}

				if($this->arParams['EDIT_ELEMENT'] != "Y" || $this->arParams['ELEMENT_ID'] <= 0)
					$this->cleanOldValue();

				$_SESSION['FORM_RESULT'][$this->getFormID()] = array('ID' => $ID);

				if($this->arParams['REDIRECT_AFTER_SUCCESS'] == "Y")
					LocalRedirect($APPLICATION->GetCurPageParam("saccess=Y&id=" . $ID,array("saccess","id")));
			}
			catch(ArgumentException $ex) {
				if(strlen($ex->getParameter()) > 0)
					$this->arResult['ERRORS'][$ex->getParameter()] = $ex->getMessage();
				else
					$this->arResult['ERRORS'] = $ex->getMessage();

				if(!empty($this->LAST_ERROR)) {
					if(!is_array($this->LAST_ERROR))
						$this->LAST_ERROR = array($this->LAST_ERROR);
					$this->arResult['ERRORS'] = array_merge($this->arResult['ERRORS'],$this->LAST_ERROR);
				}
			}
		}
		else {
			$this->setDefaultValue();
		}

		if(array_key_exists("CAPTCHA", $this->arParams['FIELDS']))
			$this->arResult["CAPTCHA_CODE"] = htmlspecialcharsbx($APPLICATION->CaptchaGetCode());

		if(isset($_SESSION['FORM_RESULT'][$this->getFormID()])) {
			$this->arResult['SUCCESS_RESULT'] = $_SESSION['FORM_RESULT'][$this->getFormID()];
			unset($_SESSION['FORM_RESULT'][$this->getFormID()]);
		}

		if(null === $isAjax)
			$this->includeComponentTemplate();

		return "";
	}

	public function executeComponentTask($arUpdate = array(),$ID = false) {
		$events = GetModuleEvents("main", "OnPrepeaDataFromUpdateOrAdd");
		while ($arEvent = $events->Fetch()) {
			ExecuteModuleEventEx($arEvent, array(&$arUpdate));
		}

		$arUpdate['IBLOCK_ID'] = $this->getIblockId();
		$arUpdate = $this->checkIBlockSettings($arUpdate);

		foreach ($arUpdate as $propCode => $propValue) {
			if(!$this->IsProperty($propCode))
				continue;

			$arUpdate['PROPERTY_VALUES'][$this->getOriginalPropCode($propCode)] = $propValue;
			unset($arUpdate[$propCode]);
		}

		$this->checkIBlockSection($arUpdate);

		/**
		 * Добавим привязку к разделу, если раздел указан в настройках компонента
		 */
		if(isset($this->arParams['PARENT_SECTION']) && (int)$this->arParams['PARENT_SECTION'] > 0)
			$arSecion = Iblock\SectionTable::getById($this->arParams['PARENT_SECTION'])->fetch();
		elseif($this->arParams['PARENT_SECTION_CODE'] && strlen($this->arParams['PARENT_SECTION_CODE'])) {
			$arSecion = Iblock\SectionTable::getList(array(
				'filter' => array($this->arParams['PARENT_SECTION_CODE']),
				'limit' => 1,
				'select' => array('ID','NAME')
			))->fetch();
		}
		else
			$arSecion = false;

		if(false !== $arSecion && (!isset($arUpdate['IBLOCK_SECTION']) || false == $arUpdate['IBLOCK_SECTION']))
			$arUpdate['IBLOCK_SECTION'] = $arSecion['ID'];
		else
			$arUpdate['IBLOCK_SECTION'] = false;

		$obCIBlockElement = new CIBlockElement();

		/**
		 * если значение параметра не установленно элемент не создаётся
		 */
		if ($this->arParams['NOT_CREATE_ELEMENT'] == "Y") {
			return true;
		}

		/**
		 * исключим свойства из массива, чтобы обновить иил добавить элемент
		 */
		$arrUpdate = array_diff_key($arUpdate, array('PROPERTY_VALUES' => ''));
		if($this->isUpdateAction()) {
			$newID = $this->arParams['ELEMENT_ID'];
			/**
			 * обновим поля у элемента
			 */
			if (!empty($arrUpdate))
			{
				$res = $obCIBlockElement->Update($newID, $arrUpdate);
				if(!$res)
				{
					throw new ArgumentException($obCIBlockElement->LAST_ERROR, 'UPDATE_ELEMENT');
				}
			}
		}
		else {

			$arUpdate['IBLOCK_SECTION_ID'] = $arUpdate['IBLOCK_SECTION'];
			unset($arUpdate['IBLOCK_SECTION']);

			$newID = $obCIBlockElement->Add($arrUpdate, false, true, true);
		}

		/**
		 * обновим свойства элемента
		 */
		if (
			$newID &&
			(array_key_exists('PROPERTY_VALUES', $arUpdate) && !empty($arUpdate['PROPERTY_VALUES']))
		) {
			$obCIBlockElement->SetPropertyValuesEx($newID, $this->arParams['IBLOCK_ID'], $arUpdate['PROPERTY_VALUES']);
		}

		if (!$newID) {
			throw new ArgumentException($obCIBlockElement->LAST_ERROR, 'ADD_ELEMENT');
		}
		$this->arParams['ELEMENT_ID'] = $newID;

		return $newID;
	}

	protected function checkIBlockSection(array &$Fields) {

	}

	protected function checkIBlockSettings(array $arField) {
		$iblockID = $this->getIblockId();

		$arIblockField = CIBlock::GetFields($iblockID);

		/**
		 * проверим обязательное поле NAME, если его нет то заполним его по умолчанию
		 */
		if (!array_key_exists('NAME',$arField))
			$arField['NAME'] = Loc::getMessage('CP_IBADDFORM_F_IBLOCK_NAME_FIELD_DEFAULT');
		/*
				if(
					'Y' == $arIblockField['IBLOCK_SECTION']['IS_REQUIRED']
					&& !isset($arField['IBLOCK_SECTION'])
				){
					$arField['IBLOCK_SECTION'] = false;
				}*/

		/**
		 * проверим обязательность заполнения символьного кода
		 */
		if(
			'Y' == $arIblockField['CODE']['IS_REQUIRED']
			&& !isset($arField['CODE'])
		){
			$codeSetting = $arIblockField['CODE']['DEFAULT_VALUE'];
			if($codeSetting['TRANSLITERATION']) {
				$fieldCode = CUtil::translit($arField['NAME'],LANGUAGE_ID,array(
					'max_len' => $codeSetting['TRANS_LEN'],
					'change_case' => $codeSetting['TRANS_CASE'],
					'replace_space' => $codeSetting['TRANS_SPACE'],
					'replace_other' => $codeSetting['TRANS_OTHER'],
					'delete_repeat_replace' => ($codeSetting['TRANS_LEN'] == 'Y'),
				));
			}
			else {
				$fieldCode = $arField['NAME'];
			}

			if('Y' == $codeSetting['UNIQUE']) {
				$res = Iblock\ElementTable::getList(array(
					'filter' => array(
						'IBLOCK_ID' => $iblockID,
						'CODE' => $fieldCode
					),
					'select' => array('ID','NAME')
				))->fetch();
				if($res)
					$fieldCode .= microtime();
			}

			$arField['CODE'] = $fieldCode;
		}

		return $arField;
	}

	protected function cleanOldValue() {
		foreach($this->arResult['ITEMS'] as $code => &$filed)
			$filed['OLD_VALUE'] = '';
	}

	protected function setDefaultValue() {
		$arData = false;
		if ($this->arParams['EDIT_ELEMENT'] == "Y" && $this->arParams['ELEMENT_ID'] > 0) {
			$arData = $this->getOldData($this->arParams);
		}

		foreach($this->arResult['ITEMS'] as $code => &$filed) {
			if(false !== $arData && array_key_exists($code,$arData)) {
				$filed['OLD_VALUE'] = $arData[$code];
				continue;
			}

			if(!isset($filed['DEFAULT']))
				continue;

			$filed['OLD_VALUE'] = $filed['DEFAULT'];
		}
	}

	public function getOldData($arResult) {
		if($this->arParams['ELEMENT_ID'] <= 0)
			return false;

		$arSelectFields = array_keys($arResult["FIELDS"]);
		$arFilterFields = array(
			"IBLOCK_ID" => $this->arParams['IBLOCK_ID'],
			"ID" => $this->arParams['ELEMENT_ID'],
		);

		$obj = CIBlockElement::GetList(array(), $arFilterFields);
		if (is_object($obj) && $objEl = $obj->GetNextElement()) {
			$arField = $objEl->GetFields();
			$arProp = $objEl->GetProperties();

			/**
			 * список старых значений полей ИБ
			 */
			$arField = array_intersect_key($arField, array_flip($arSelectFields));

			$arProperty = array();
			foreach ($arProp as $key => $value) {
				if (!in_array($this->bxPrefix . $key, $arSelectFields)) continue;

				$arProperty[$this->bxPrefix . $key] = in_array($value['PROPERTY_TYPE'],
					$this->arListType) ? $value['VALUE_ENUM_ID'] : $value['VALUE'];
			}
			return array_merge($arField, $arProperty);
		}

		return false;
	}

	public function getOriginalPropCode($code) {
		if (strlen($code) <= 0 || !$this->IsProperty($code)) return false;

		return str_replace($this->bxPrefix, "", $code);
	}

	/**
	 * Функция позволяет проверить является ли поле свойством ИБ
	 */
	private function IsProperty($code) {
		$parent = '/^' . $this->bxPrefix . '[A-z\d\[\]]+$/';

		return preg_match($parent, $code);
	}

	public function getMailField($newID, $arUpdateFieldValues) {
		$arMailFields = array();

		$btn = \CIBlock::GetPanelButtons(
			$this->arParams["IBLOCK_ID"],
			$newID,
			0,
			array("SESSID"=>false, "CATALOG"=>true)
		);
		if(isset($btn['edit'])) {
			$arMailFields['EDIT_LINK'] = $btn['edit']['edit_element']['ACTION_URL'];
		}

		/**
		 * добавим возможность использования ID добавленного элемента в шаблоне почтового события
		 */
		if (isset($newID) && $newID) {
			$arMailFields['ID'] = $newID;
		}

		$arMailFields = array_merge($arMailFields, $arUpdateFieldValues);
		foreach ($arUpdateFieldValues as $k => $v) {
			$arMailFields["RAWVALUE_" . $k] = $v;
		}

		$obj = CIBlockElement::GetByID($newID)->GetNextElement(false);
		$arField = $obj->GetFields();
		$arProp = $obj->GetProperties();
		foreach ($this->arResult['ITEMS'] as $code => $info) {
			$propCode = $this->getOriginalPropCode($code);
			if (
				(false !== $propCode && !array_key_exists($propCode, $arProp))
				|| (false === $propCode && !array_key_exists($code, $arField))
			) {
				continue;
			}

			if (false === $propCode)
			{
				$arMailFields[$code] = strip_tags($arField[$code]);
			}
			else
			{
				$arMailFields[$code] = "";
				$prop = &$arProp[$propCode];
				$boolArr = is_array($prop["VALUE"]);
				if (
					($boolArr && !empty($prop["VALUE"]))
					|| (!$boolArr && strlen($prop["VALUE"]) > 0)
				)
				{
					$res = CIBlockFormatProperties::GetDisplayValue($arField, $prop, "catalog_out");
					if ($res)
					{
						$value = is_array($res['DISPLAY_VALUE']) ? implode('<br>',
							$res['DISPLAY_VALUE']) : $res['DISPLAY_VALUE'];
						$value = strip_tags(nl2br($value));
						$arMailFields[$code] = $value;
					}
				}
			}
		}

		$fields = $this->arResult['ITEMS'];
		foreach ($arMailFields as $msgCode => $msgValue) {
			if (!array_key_exists($msgCode, $fields) || strlen($msgValue) <= 0) continue;

			$item = $fields[$msgCode];
			$title = strlen($item['TITLE']) > 0 ? $item['TITLE'] : $item['ORIGINAL_TITLE'];
			$arMailFields['MESSAGE'][] = Loc::getMessage('CP_IBADDFORM_F_ADD_FORM_MAIL_MESSAGE_TEMPLATE',
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

	public function GetComponentParametrs($arGroup = array(),$arCurrent = array()) {
		return self::GetDefaultComponentParametrs($arGroup,$arCurrent);
	}

	/**
	 * @param int $iblockID
	 * @param mixed[] $currentValue
	 * @param string $parentComponentName
	 * @return array
	 *
	 * @todo Нужно избавиться от переопределения аргументов метода в базовом классе
	 */
	public function GetFieldData($iblockID, $currentValue, $parentComponentName) {
		$parent = new CBCitrusSmartForm();
		$arFields = $parent->GetParametrsFieldInput('','','BASE',$this->getComponentPathByClassName($parentComponentName));

		$defaultFields = $this->GetDefaultFields($iblockID,true);

		$arFields['JS_DATA'] = $defaultFields;

		if(!isset($currentValue['FIELDS']) || !is_array($currentValue['FIELDS']) || empty($currentValue['FIELDS'])) {
			$arFields['JS_DATA'] = $this->prepeaParamsBeforeOpenSetting($arFields['JS_DATA']);
			return $arFields;
		}

		$curField = $currentValue['FIELDS'];

		/**
		 * TODO великий костыль! пришлось парсить список полей,
		 * потому что в визуальном редакторе поля передаются не верно (из настроек модуля)
		 */
		foreach($curField as $code => &$f)
			$f = $this->parseParamFieldValue($f);

		foreach($defaultFields as $dIndex => $dF) {
			if(array_key_exists($dIndex,$curField)) {
				$curField[$dIndex]['ACTIVE'] = 'Y';
				$curField[$dIndex] = array_merge($dF,$curField[$dIndex]);
			}
			else {
				$dF['ACTIVE'] = 'N';
				$curField[$dIndex] = $dF;
			}
		}

		/**
		 * TODO тут надо подумать, может как то проще можно получить путь до шаблона компонента
		 */
		$this->initComponentTemplate();
		if($this->__template instanceof CBitrixComponentTemplate)
			$templatePath = $_SERVER['DOCUMENT_ROOT'] . $this->__template->__folder;
		else
			$templatePath = null;

		$arFieldTemplates = $this->getFieldTemplate($templatePath);

		if(!empty($arFieldTemplates)) {
			foreach($curField as $code => &$f) {
				if(isset($f['USER_TYPE']) && strlen($f['USER_TYPE']) > 0)
					$type = $this->getTemplateType($f['USER_TYPE']);
				else
					$type = $this->getTemplateType($f['TYPE']);

				$f['TEMPLATES'] = isset($this->templateMap[$type]) ? $this->templateMap[$type] : false;
			}
		}

		$curField = $this->prepeaParamsBeforeOpenSetting($curField);

		$arFields['JS_DATA'] = $curField;

		return $arFields;
	}

	/**
	 * @return bool
	 */
	public function getIblockId() {
		return $this->iblockId;
	}

	/**
	 * @param bool $iblockId
	 */
	public function setIblockId($iblockId) {
		$this->iblockId = (int)$iblockId;
	}

	/**
	 * Список полей по умолчанию
	 *
	 * Функция позволяет получить список всех полей, которые доступны в форме.
	 * Каждое поле содержит список доступных атрибутов:
	 *
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
	public function GetDefaultFields($iblockID = false,$bIncludeProperties = true, $bIncludeMoreFields = true) {
		if(false === $iblockID)
			$iblockID = $this->getIblockId();

		$arDeafultHLField = array();

		$__cacheKey = md5(serialize(Array($iblockID, $bIncludeProperties, $bIncludeMoreFields)));
		try {

			if($iblockID <= 0)
				throw new SystemException(Loc::getMessage("CP_IBADDFORM_E_IBLOCK_ID_EMPTY"));

			if ( is_array($this->__arCache) && array_key_exists($__cacheKey, $this->__arCache))
				return $this->__arCache[$__cacheKey];

			$arDefaultFields = Array(
				"CODE" => Array(
					"TOOLTIP" => "",
					"IS_REQUIRED" => "N",
					"VALIDRULE" => "",
					"VALID_ERROR_MSG" => "",
					"HIDE_FIELD" => 'N',
					'TYPE' => 'S',
					'MULTIPLE' => 'N',
					'ACTIVE' => 'Y'
				),
				"XML_ID" => Array('TYPE' => 'S', 'MULTIPLE' => 'N','ACTIVE' => 'Y'),
				"NAME" => Array('TYPE' => 'S', 'MULTIPLE' => 'N','ACTIVE' => 'Y'),
				"IBLOCK_SECTION" => Array('TYPE' => 'L', 'MULTIPLE' => 'N','ACTIVE' => 'Y'),
				"ACTIVE" => Array(
					'TYPE' => 'L',
					'LIST_TYPE' => 'C',
					'MULTIPLE' => 'Y',
					'ITEMS' => array(
						array('ID' => 'Y',"VALUE" => Loc::getMessage("CITRUS_SMARTFORM_YES"))
					),
					'ACTIVE' => 'Y'
				),
				"ACTIVE_FROM" => Array(
					'TYPE' => 'S',
					'MULTIPLE' => 'N',
					'USER_TYPE' => 'DateTime',
					'ACTIVE' => 'Y'
				),
				"ACTIVE_TO" => Array('TYPE' => 'S', 'MULTIPLE' => 'N', 'USER_TYPE' => 'DateTime','ACTIVE' => 'Y'),
				"SORT" => Array('TYPE' => 'N', 'MULTIPLE' => 'N','ACTIVE' => 'Y'),
				"PREVIEW_PICTURE" => Array(
					'ACTIVE' => 'Y',
					'TYPE' => 'F',
					'FILE_TYPE' => 'jpg, gif, bmp, png, jpeg',
					'MULTIPLE' => 'N',
					'WITH_DESCRIPTION' => 'Y',
				),
				"PREVIEW_TEXT" => Array(
					'ACTIVE' => 'Y',
					'TYPE' => "T",
					'MULTIPLE' => 'N',
					'ROW_COUNT' => 5,
					'COL_COUNT' => 30
				),
				"DETAIL_PICTURE" => Array(
					'ACTIVE' => 'Y',
					'TYPE' => 'F',
					'FILE_TYPE' => 'jpg, gif, bmp, png, jpeg',
					'MULTIPLE' => 'N',
					'WITH_DESCRIPTION' => 'Y',
				),
				"DETAIL_TEXT" => Array(
					'ACTIVE' => 'Y',
					'TYPE' => "T",
					'MULTIPLE' => 'N',
					'ROW_COUNT' => 5,
					'COL_COUNT' => 30
				),
				"TAGS" => Array(
					"ACTIVE" => "Y",
					'TYPE' => 'S',
					'MULTIPLE' => 'N',
				),
				"CAPTCHA" => Array(
					"ACTIVE" => "Y",
					"IS_REQUIRED" => "Y",
					"ORIGINAL_TITLE" => Loc::getMessage("CP_SMARTFORM_F_CAPTCHA_TITLE"),
					"TITLE"=> Loc::getMessage("CP_SMARTFORM_F_CAPTCHA_TITLE"),
					"TOOLTIP" => Loc::getMessage("CP_SMARTFORM_F_CAPTCHA_TOOLTIP_TITLE"),
					"TYPE" => "CAPTCHA",
					"VALIDRULE" => "",
					"VALID_ERROR_MSG" => "",
					"HIDE_FIELD" => "N"
				)
			);

			/**
			 * заполнить подписи к полям
			 */
			foreach ($arDefaultFields as $code => &$arField) {
				if ($code == 'CAPTCHA') {
					continue;
				}
				$arField["ORIGINAL_TITLE"] = Loc::getMessage("IBLOCK_FIELD_" . $code);
				if (!array_key_exists("TITLE", $arField) && strlen($code) > 0) {
					$arField["TITLE"] = $arField["ORIGINAL_TITLE"];
				}
			}

			if(false === \Bitrix\Main\Loader::includeModule('iblock'))
				return $arDefaultFields;

			if ($bIncludeProperties && $iblockID > 0) {
				$rsProperties = CIBlockProperty::GetList(Array("SORT" => "ASC"), Array("IBLOCK_ID" => $iblockID));
				while ($arProperty = $rsProperties->GetNext(false, false)) {
					$field = "PROPERTY_" . (empty($arProperty["CODE"]) ? $arProperty['ID'] : $arProperty["CODE"]);

					$arDefaultFields[$field] = $arFields[$field] = array_filter(Array(
						"TITLE" => $arProperty["NAME"],
						"IS_REQUIRED" => $arProperty["IS_REQUIRED"],
						"HIDE_FIELD" => "N",
						"ACTIVE" => "Y",
						"TYPE" => $arProperty['PROPERTY_TYPE'],
						"MULTIPLE" => $arProperty['MULTIPLE'],
						"WITH_DESCRIPTION" => $arProperty['WITH_DESCRIPTION'],
						"FILE_TYPE" => isset($arProperty['FILE_TYPE']) ? $arProperty['FILE_TYPE'] : null,
					));

					if ($bIncludeMoreFields) {
						$arPropMoreValue = array(
							"PROPERTY_ID" => $arProperty['ID'],
							"USER_TYPE" => $arProperty['USER_TYPE'],
							"LIST_TYPE" => $arProperty['LIST_TYPE'],
							"LINK_IBLOCK_ID" => $arProperty['LINK_IBLOCK_ID'],
						);

						if (in_array($arProperty['TYPE'], array('L', 'E', 'G'))) {
							$arPropMoreValue['ITEMS'] = array('');
						}

						$arDefaultFields[$field] = array_merge_recursive($arDefaultFields[$field],
							$arPropMoreValue);
					}

					$arDefaultFields[$field]['ORIGINAL_TITLE'] = '[' . $arProperty['ID'] . '] ' . $arProperty['NAME'];
				}
			}

			/**
			 * Заполним списки значениями
			 */
			if(true === $bIncludeMoreFields) {
				foreach ($arDefaultFields as $code => &$propInfo) {
					if ($code == 'IBLOCK_SECTION' && $bIncludeMoreFields) {
						$arSections = array();
						$rsIBlockSectionList = CIBlockSection::GetTreeList(array(
							"ACTIVE" => "Y",
							"IBLOCK_ID" => $iblockID
						),array('ID','NAME','DEPTH_LEVEL'));
						while ($arS = $rsIBlockSectionList->fetch()) {
							$arS["NAME"] = str_repeat(" . ", $arS["DEPTH_LEVEL"]) . $arS["NAME"];
							$arSections[$arS["ID"]] = array(
								"ID" => $arS['ID'],
								"VALUE" => $arS["NAME"]
							);
						}

						$propInfo['ITEMS'] = Array(
								Array(
									'ID' => 0,
									'VALUE' => Loc::getMessage("CP_IBADDFORM_F_IBLOCK_FROM_LIST")
								)
							) + $arSections;
						continue;
					}

					/**
					 * если свойство имеет тип список, то получим значения списка
					 */
					if ($propInfo["TYPE"] == "L" && $code != 'ACTIVE') {
						$arProperty = array();
						$rsPropertyEnum = CIBlockProperty::GetPropertyEnum($propInfo["PROPERTY_ID"]);
						while ($arPropertyEnum = $rsPropertyEnum->GetNext()) {
							$arProperty[$arPropertyEnum["ID"]] = $arPropertyEnum;
						}

						if (empty($arProperty) && !empty($propInfo['ENUM'])) {
							$arProperty = $propInfo['ENUM'];
						}

						if ($propInfo['LIST_TYPE'] == 'C') {
							$propInfo['ITEMS'] = $arProperty;
						}
						else {
							$propInfo['ITEMS'] = Array(
									Array(
										'ID' => 0,
										'VALUE' => Loc::getMessage("CP_IBADDFORM_F_IBLOCK_FROM_LIST")
									)
								) + $arProperty;
						}
					}

					/**
					 * если свойство имеет тип "Привязка к разделам"
					 */
					if ($propInfo["TYPE"] == "G" && $propInfo['LINK_IBLOCK_ID'] > 0) {
						$arSectionList = array();
						$rsSection = CIBlockSection::GetTreeList(array(
							"ACTIVE" => "Y",
							"GLOBAL_ACTIVE" => "Y",
							"IBLOCK_ID" => $propInfo['LINK_IBLOCK_ID']
						));
						while ($arSection = $rsSection->GetNext()) {
							$arSection["NAME"] = str_repeat(" . ", $arSection["DEPTH_LEVEL"]) . $arSection["NAME"];
							$arSectionList[$arSection["ID"]] = array(
								"ID" => $arSection["ID"],
								"VALUE" => $arSection["NAME"]
							);
						}

						if (empty($arSectionList) && !empty($propInfo['ENUM'])) {
							$arSectionList = $propInfo['ENUM'];
						}
						$propInfo['ITEMS'] = Array(
								Array(
									'ID' => 0,
									'VALUE' => Loc::getMessage("CP_IBADDFORM_F_IBLOCK_FROM_LIST")
								)
							) + $arSectionList;
					}

					/**
					 * если свойство имеет тип "Привязка к элементам инфоблока"
					 */
					if ($propInfo["TYPE"] == "E" && $propInfo['LINK_IBLOCK_ID'] > 0) {
						$arElementList = array();
						$obElement = CIBlockElement::GetList(
							Array("NAME" => "ASC"),
							Array(
								"IBLOCK_ID" => $propInfo['LINK_IBLOCK_ID'],
								"ACTIVE" => 'Y'
							),
							false,
							false,
							Array("ID", "NAME")
						);

						while ($arElement = $obElement->Fetch()) {
							$arElementList[$arElement['ID']] = array(
								"ID" => $arElement['ID'],
								"VALUE" => $arElement["NAME"],
							);
						}

						if (empty($arElementList) && !empty($propInfo['ENUM'])) {
							$arElementList = $propInfo['ENUM'];
						}
						$propInfo['ITEMS'] = Array(
								Array(
									'ID' => 0,
									'VALUE' => Loc::getMessage("CP_IBADDFORM_F_IBLOCK_FROM_LIST")
								)
							) + $arElementList;
					}
				}
			}

			$db_events = GetModuleEvents("smart.form", "onBeforeGetDefaultFieldForm");
			while ($arEvent = $db_events->Fetch()) {
				ExecuteModuleEventEx($arEvent, array(&$arDefaultFields));
			}


			$this->__arCache[$__cacheKey] = $arDefaultFields;

			return $arDefaultFields;
		}
		catch(SystemException $ex) {

		}

		return $arDeafultHLField;
	}

	protected function laodAction() {
		if(
			$this->arParams['EDIT_ELEMENT'] == "Y" && $this->arParams['ELEMENT_ID'] > 0
		) {
			$elementID = (int)$this->arParams['ELEMENT_ID'];
			$isUpdate = CIBlockElement::GetByID($elementID)->Fetch();
			if($isUpdate)
				$this->action = self::ACTION_UPDATE;
			else
				$this->action = self::ACTION_ADD;
		}
		else {
			$this->action = self::ACTION_ADD;
		}
	}
}

