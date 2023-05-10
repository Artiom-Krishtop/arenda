<?
use Bitrix\Main\Localization\Loc;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

Loc::loadMessages(__FILE__);

class CBCitrusIBAddFormComponent extends CBitrixComponent
{
	var $bxPrefix = "PROPERTY_";
	var $LAST_ERROR = false;
	var $arParams = null;
	var $arListType = array('G', 'L');
	protected $fields;

	public function onPrepareComponentParams($arParams)
	{
		$arParams['MAILE_EVENT_TEMPLATE'] = !is_array($arParams['MAILE_EVENT_TEMPLATE']) ? array($arParams['MAILE_EVENT_TEMPLATE']) : $arParams['MAILE_EVENT_TEMPLATE'];
		$arParams['MAILE_EVENT_TEMPLATE'] = array_diff($arParams['MAILE_EVENT_TEMPLATE'], array("", "0"));
		if (empty($arParams['MAILE_EVENT_TEMPLATE']))
		{
			$arParams['MAILE_EVENT_TEMPLATE'] = array("");
		}

		$this->arParams = $arParams;

		return $arParams;
	}

	public function executeComponent()
	{
		/*DEMO CODE for "pure" class.php component
		$this->arResult["FFF"] = "ggg";
		$this->includeComponentTemplate();
		return $this->ELEMENT_ID;
		*/

		return parent::executeComponent();
	}

	public function GetOldData($arResult)
	{
		$arSelectFields = array_keys($arResult["ITEMS"]);
		$arFilterFields = array(
			"IBLOCK_ID" => $this->arParams['IBLOCK_ID'],
			"ID" => $this->arParams['ELEMENT_ID'],
		);

		$obj = CIBlockElement::GetList(array(), $arFilterFields);
		if (is_object($obj) && $objEl = $obj->GetNextElement())
		{
			$arField = $objEl->GetFields();
			$arProp = $objEl->GetProperties();

			// список старых значений полей ИБ
			$arField = array_intersect_key($arField, array_flip($arSelectFields));

			$arProperty = array();
			foreach ($arProp as $key => $value)
			{
				if (!in_array($this->bxPrefix . $key, $arSelectFields)) continue;
				$arProperty[$this->bxPrefix . $key] = in_array($value['PROPERTY_TYPE'],
					$this->arListType) ? $value['VALUE_ENUM_ID'] : $value['VALUE'];
			}

			return array_merge($arField, $arProperty);
		}

		return false;
	}

	public function GetOriginalPropCode($code)
	{
		if (strlen($code) <= 0 || !$this->IsProperty($code)) return false;

		return str_replace($this->bxPrefix, "", $code);
	}

	/*
	 * Функция позволяет проверить является ли поле свойством ИБ
	 * */
	private function IsProperty($code)
	{
		$parent = '/^' . $this->bxPrefix . '[A-z\d\[\]]+$/';

		return preg_match($parent, $code);
	}

	public function GetMailField($newID, $arUpdateFieldValues)
	{
		$arEvent = CEventType::GetList(array("TYPE_ID" => $this->arParams['MAIL_EVENT']))->Fetch();
		if (!$arEvent) return false;

		$arMailFields = array();
		// добавим возможность использования ID добавленного элемента в шаблоне почтового события
		if (isset($newID) && $newID)
		{
			$arMailFields['ID'] = $newID;
		}

		$arMailFields = array_merge($arMailFields, $arUpdateFieldValues);

		$obj = CIBlockElement::GetByID($newID)->GetNextElement(false);
		$arField = $obj->GetFields();
		$arProp = $obj->GetProperties();
		foreach ($this->arParams['FIELDS'] as $code => $info)
		{
			$propCode = $this->GetOriginalPropCode($code);
			if (
				(false !== $propCode && !array_key_exists($propCode, $arProp))
				|| (false === $propCode && !array_key_exists($code, $arField))
			)
			{
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
		foreach ($arMailFields as $msgCode => $msgValue)
		{
			if (!array_key_exists($msgCode, $fields) || strlen($msgValue) <= 0) continue;

			$item = $fields[$msgCode];
			$title = strlen($item['TITLE']) > 0 ? $item['TITLE'] : $item['ORIGINAL_TITLE'];
			$arMailFields['MESSAGE'][] = GetMessage('CIEE_ADD_FORM_MAIL_MESSAGE_TEMPLATE',
				array("#TITLE#" => $title, "#VALUE#" => $msgValue));
		}

		if (isset($arMailFields['MESSAGE']) && !empty($arMailFields['MESSAGE']))
		{
			$arMailFields['MESSAGE'] = implode("\n", $arMailFields['MESSAGE']);
		}

		$bxSendemail = true;
		$events = GetModuleEvents("main", "OnPrepeaDataBeforeSendEmail");
		while ($arEvent = $events->Fetch())
		{
			$bxSendemail = ExecuteModuleEventEx($arEvent, array(&$arMailFields));
		}

		return true === $bxSendemail || null === $bxSendemail ? $arMailFields : false;
	}

	public function GetDefaultFields($iblockId = false)
	{
		if (!function_exists('CIEE_GetDefaultFields'))
		{
			include __DIR__ . '/func.php';
		}

		return CIEE_GetDefaultFields($iblockId, $bIncludeProperties = $iblockId > 0);
	}

	public static function getFz152Url($siteId = SITE_ID)
	{
		return '/bitrix/components/citrus/iblock.element.form/agreement.php?site=' . urlencode($siteId);
	}
}

