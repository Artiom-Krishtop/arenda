<?php

use Citrus\Arealty\Cache;
use Bitrix\Main\Localization\Loc;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

Loc::loadMessages(__DIR__ . '/fields.php');

if (!function_exists('htmlspecialcharsbx'))
{
	function htmlspecialcharsbx($string, $flags = ENT_COMPAT)
	{
		//shitty function for php 5.4 where default encoding is UTF-8
		return htmlspecialchars($string, $flags, (defined("BX_UTF") ? "UTF-8" : "ISO-8859-1"));
	}
}

// TODO настройки дл€ конкретного инфоблока (об€зательность полей)
// TODO можно оформить в виде ф-ию, чтобы можно было передавать ID инфоблока и добавл€ть еще и массив свойств этого инфоблока
// TODO кеш дл€ значений списочних свойств и списка разделов

/*
$arDefaultFields = Array(
	"код пол€" => Array(
			"ACTIVE" => true, // выводить ли поле на форме, по-умолчанию true
			"TITLE" => "Ќазвание пол€ (подпись по-умолчанию)",
			"ORIGINAL_TITLE" => "ќригинальное название пол€ или свойства",
			"IS_REQUIRED" => false, // по-умолчанию false
			"READ_ONLY" => false, // по-умолчанию false
//			"DEFAULT_VALUE" => array(),
			"TOOLTIP" => "", // подсказка по заполнению
			"PROPERTY_ID" => "", // дл€ свойств указываетс€ ID свойства
		)
);
*/

if (!function_exists('CIEE_GetDefaultFields')):

	function CIEE_htmlspecialchars($mixed, $quote_style = ENT_QUOTES, $charset = 'UTF-8')
	{
		if (is_array($mixed))
		{
			foreach ($mixed as $key => $value)
			{
				$mixed[$key] = CIEE_htmlspecialchars($value, $quote_style, $charset);
			}
		}
		elseif (is_string($mixed))
		{
			$mixed = htmlspecialcharsbx(htmlspecialchars_decode($mixed, $quote_style), $quote_style);
		}

		return $mixed;
	}

	function CIEE_GetDefaultFields($iblockID = false, $bIncludeProperties = false, $bIncludeMoreFields = false)
	{
		$cacheKey = array($iblockID, $bIncludeProperties, $bIncludeMoreFields);
		$arDefaultFields = Cache::remember($cacheKey, 24*30*60, function () use ($iblockID, $bIncludeProperties, $bIncludeMoreFields) {

			if ($iblockID)
			{
				Cache::registerIblockCacheTag($iblockID);
			}

			$arDefaultFields = Array(
				//"ID" => Array("READ_ONLY" => true,"DEFAULT_SETINGS" => array('PROPERTY_TYPE' => 'N','MULTIPLE' => 'N')),
				"CODE" => Array(),
				"NAME" => Array("IS_REQUIRED" => true),
				"IBLOCK_SECTION" => Array(),
				"ACTIVE" => Array(),
				"ACTIVE_FROM" => Array(),
				"ACTIVE_TO" => Array(),
				"SORT" => Array(),
				"PREVIEW_PICTURE" => Array(),
				"PREVIEW_TEXT" => Array(),
				"DETAIL_PICTURE" => Array(),
				"DETAIL_TEXT" => Array(),
				"TAGS" => Array(),
				"CAPTCHA" => Array(
					"IS_REQUIRED" => true,
					"ORIGINAL_TITLE" => GetMessage("CIEE_F_CAPTCHA"),
					"TITLE" => GetMessage("CIEE_F_CAPTCHA"),
					"TOOLTIP" => GetMessage("CIEE_F_CAPTCHA_TOOLTIP")
				),
				"GIFT" => array(
					"HIDE_FIELD" => true,
					"IS_REQUIRED" => false,
					"ORIGINAL_TITLE" => GetMessage("CIEE_F_GIFT"),
					"TITLE" => GetMessage("CIEE_F_GIFT"),
					"VALUE" => "",
					"MULTIPLE" => "N",
				),
				"FZ152" => array(
					'PROPERTY_TYPE' => 'L',
					"IS_REQUIRED" => true,
					"ORIGINAL_TITLE" => GetMessage("CIEE_F_FZ152_AGREEMENT"),
					"TITLE" => GetMessage("CIEE_F_FZ152_AGREEMENT"),
					"PLACEHOLDER" => GetMessage("CIEE_F_FZ152_AGREEMENT"),
					"VALUE" => "Y",
					"DEFAULT_VALUE" => \Bitrix\Main\Config\Option::get('citrus.arealty', 'fz152_default', 'N'),
					"MULTIPLE" => "Y",
					"LIST_TYPE" => "C",
					"ENUM" => array(
						'Y' => array(
							'VALUE' => GetMessage("CIEE_F_FZ152_AGREEMENT_TEXT", array('#URL#' => CBCitrusIBAddFormComponent::getFz152Url())),
						)
					),
				),
			);

			if ($bIncludeMoreFields)
			{
				$arMoreFieldsValue = Array(
					//"ID" => Array("READ_ONLY" => true,"DEFAULT_SETINGS" => array('PROPERTY_TYPE' => 'N','MULTIPLE' => 'N')),
					"CODE" => Array('PROPERTY_TYPE' => 'S', 'MULTIPLE' => 'N','ACTIVE' => 'Y'),
					"NAME" => Array('PROPERTY_TYPE' => 'S', 'MULTIPLE' => 'N','ACTIVE' => 'Y'),
					"IBLOCK_SECTION" => Array('PROPERTY_TYPE' => 'L', 'MULTIPLE' => 'N','ACTIVE' => 'Y'),
					"ACTIVE" => Array(
						'PROPERTY_TYPE' => 'L',
						'LIST_TYPE' => 'C',
						'MULTIPLE' => 'Y',
						'ENUM' => array("Y" => ''),
						'ACTIVE' => 'Y'
					),
					"ACTIVE_FROM" => Array('PROPERTY_TYPE' => 'S', 'MULTIPLE' => 'N', 'USER_TYPE' => 'DateTime','ACTIVE' => 'Y'),
					"ACTIVE_TO" => Array('PROPERTY_TYPE' => 'S', 'MULTIPLE' => 'N', 'USER_TYPE' => 'DateTime','ACTIVE' => 'Y'),
					"SORT" => Array('PROPERTY_TYPE' => 'N', 'MULTIPLE' => 'N','ACTIVE' => 'Y'),
					"PREVIEW_PICTURE" => Array(
						'ACTIVE' => 'Y',
						'PROPERTY_TYPE' => 'F',
						'FILE_TYPE' => 'jpg, gif, bmp, png, jpeg',
						'MULTIPLE' => 'N'
					),
					"PREVIEW_TEXT" => Array(
						'ACTIVE' => 'Y',
						'PROPERTY_TYPE' => "T",
						'MULTIPLE' => 'N',
						'ROW_COUNT' => 5,
						'COL_COUNT' => 30
					),
					"DETAIL_PICTURE" => Array(
						'ACTIVE' => 'Y',
						'PROPERTY_TYPE' => 'F',
						'FILE_TYPE' => 'jpg, gif, bmp, png, jpeg',
						'MULTIPLE' => 'N'
					),
					"DETAIL_TEXT" => Array(
						'ACTIVE' => 'Y',
						'PROPERTY_TYPE' => "T",
						'MULTIPLE' => 'N',
						'ROW_COUNT' => 5,
						'COL_COUNT' => 30
					),
					//"DATE_CREATE" => Array("READ_ONLY" => true,),
					//"CREATED_BY" => Array("READ_ONLY" => true,),
					//"TIMESTAMP_X" => Array("READ_ONLY" => true,),
					//"MODIFIED_BY" => Array("READ_ONLY" => true,),
					//"SHOW_COUNTER" => Array("READ_ONLY" => true,),
					//"SHOW_COUNTER_START" => Array("READ_ONLY" => true,),
					"TAGS" => Array('PROPERTY_TYPE' => 'S', 'MULTIPLE' => 'N','ACTIVE' => 'Y'),
					"CAPTCHA" => Array('PROPERTY_TYPE' => 'CAPTCHA', 'MULTIPLE' => 'N','ACTIVE' => 'Y'),
				);
 
				$arDefaultFields = array_merge_recursive($arDefaultFields, $arMoreFieldsValue);
			}

			if (CModule::IncludeModule('iblock'))
			{
				// заполнить подписи к пол€м
				foreach ($arDefaultFields as $code => &$arField)
				{
					if ($code == 'CAPTCHA')
					{
						continue;
					}
					$arField["ORIGINAL_TITLE"] = GetMessage("IBLOCK_FIELD_" . $code);
					if (!array_key_exists("TITLE", $arField) && strlen($code) > 0)
					{
						$arField["TITLE"] = $arField["ORIGINAL_TITLE"];
					}
				}

				$iblockID = (int)$iblockID;
				if ($bIncludeProperties && $iblockID > 0)
				{
					$rsProperties = CIBlockProperty::GetList(Array("SORT" => "ASC"), Array("IBLOCK_ID" => $iblockID));
					while ($arProperty = $rsProperties->GetNext(false, false))
					{
						$field = "PROPERTY_" . (empty($arProperty["CODE"]) ? $arProperty['ID'] : $arProperty["CODE"]);
						$arDefaultFields[$field] = $arFields[$field] = Array(
							"TITLE" => $arProperty["NAME"],
							"IS_REQUIRED" => $arProperty["IS_REQUIRED"],
						);

						if ($bIncludeMoreFields)
						{
							$arPropMoreValue = array(
								"PROPERTY_ID" => $arProperty['ID'],
								"PROPERTY_TYPE" => $arProperty['PROPERTY_TYPE'],
								"LIST_TYPE" => $arProperty['LIST_TYPE'],
								"MULTIPLE" => $arProperty['MULTIPLE'],
								"LINK_IBLOCK_ID" => $arProperty['LINK_IBLOCK_ID'],
								"USER_TYPE" => $arProperty['USER_TYPE']
							);

							if (in_array($arProperty['PROPERTY_TYPE'], array('L', 'E', 'G')))
							{
								$arPropMoreValue['ENUM'] = array('');
							}

							$arDefaultFields[$field] = array_merge_recursive($arDefaultFields[$field],
								$arPropMoreValue);
						}

						$arDefaultFields[$field]['ORIGINAL_TITLE'] = '[' . $arProperty['ID'] . '] ' . $arProperty['NAME'];
					}
				}

				// заполнить списки
				foreach ($arDefaultFields as $code => &$propInfo)
				{
					if ($code == 'IBLOCK_SECTION')
					{
						$arSections = array();
						$rsIBlockSectionList = CIBlockSection::GetTreeList(array(
							"ACTIVE" => "Y",
							"GLOBAL_ACTIVE" => "Y",
							"IBLOCK_ID" => $iblockID
						));
						while ($arSection = $rsIBlockSectionList->GetNext())
						{
							$arSection["NAME"] = str_repeat(" . ", $arSection["DEPTH_LEVEL"]) . $arSection["NAME"];
							$arSections[$arSection["ID"]] = array(
								"ID" => $arSection['ID'],
								"VALUE" => $arSection["NAME"]
							);
						}

						$propInfo['ENUM'] = Array(
								Array(
									'ID' => 0,
									'VALUE' => GetMessage("CIEE_IBLOCK_FROM_LIST")
								)
							) + $arSections;
					}

					// если свойство имеет тип список, то получим значени€ списка
					if ($propInfo["PROPERTY_TYPE"] == "L")
					{
						$arProperty = array();
						$rsPropertyEnum = CIBlockProperty::GetPropertyEnum($propInfo["PROPERTY_ID"]);
						while ($arPropertyEnum = $rsPropertyEnum->GetNext())
						{
							$arProperty[$arPropertyEnum["ID"]] = $arPropertyEnum;
						}

						if (empty($arProperty) && !empty($propInfo['ENUM']))
						{
							$arProperty = $propInfo['ENUM'];
						}

						if ($propInfo['LIST_TYPE'] == 'C')
						{
							$propInfo['ENUM'] = $arProperty;
						}
						else
						{
							$propInfo['ENUM'] = Array(
									Array(
										'ID' => 0,
										'VALUE' => GetMessage("CIEE_IBLOCK_FROM_LIST")
									)
								) + $arProperty;
						}
					}

					// если свойство имеет тип "ѕрив€зка к разделам"
					if ($propInfo["PROPERTY_TYPE"] == "G" && $propInfo['LINK_IBLOCK_ID'] > 0)
					{
						$arSectionList = array();
						$rsSection = CIBlockSection::GetTreeList(array(
							"ACTIVE" => "Y",
							"GLOBAL_ACTIVE" => "Y",
							"IBLOCK_ID" => $propInfo['LINK_IBLOCK_ID']
						));
						while ($arSection = $rsSection->GetNext())
						{
							$arSection["NAME"] = str_repeat(" . ", $arSection["DEPTH_LEVEL"]) . $arSection["NAME"];
							$arSectionList[$arSection["ID"]] = array(
								"ID" => $arSection["ID"],
								"VALUE" => $arSection["NAME"]
							);
						}

						if (empty($arSectionList) && !empty($propInfo['ENUM']))
						{
							$arSectionList = $propInfo['ENUM'];
						}
						$propInfo['ENUM'] = Array(
								Array(
									'ID' => 0,
									'VALUE' => GetMessage("CIEE_IBLOCK_FROM_LIST")
								)
							) + $arSectionList;
					}

					// если свойство имеет тип "ѕрив€зка к элементам инфоблока"
					if ($propInfo["PROPERTY_TYPE"] == "E" && $propInfo['LINK_IBLOCK_ID'] > 0)
					{
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

						while ($arElement = $obElement->Fetch())
						{
							$arElementList[$arElement['ID']] = array(
								"ID" => $arElement['ID'],
								"VALUE" => $arElement["NAME"],
							);
						}

						if (empty($arElementList) && !empty($propInfo['ENUM']))
						{
							$arElementList = $propInfo['ENUM'];
						}
						$propInfo['ENUM'] = Array(
								Array(
									'ID' => 0,
									'VALUE' => GetMessage("CIEE_IBLOCK_FROM_LIST")
								)
							) + $arElementList;
					}
				}

				$db_events = GetModuleEvents("citrus.tools", "OnBeforeGetDefaultValueForm");
				while ($arEvent = $db_events->Fetch())
				{
					ExecuteModuleEventEx($arEvent, array(&$arDefaultFields));
				}
			}
			return $arDefaultFields;
		}, __FUNCTION__ . '_' . (int)$iblockID);

		return $arDefaultFields;
	}
endif;

