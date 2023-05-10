<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

// set default value for missing parameters, simple param check
$componentParams = CComponentUtil::GetComponentProps($this->getName());
if (is_array($componentParams))
{
	foreach ($componentParams["PARAMETERS"] as $paramName => $paramArray)
	{
		if (!is_set($arParams, $paramName) && is_set($paramArray, "DEFAULT"))
		{
			$arParams[$paramName] = $paramArray["DEFAULT"];
		}

		$paramArray["TYPE"] = ToUpper(is_set($paramArray, "TYPE") ? $paramArray["TYPE"] : "STRING");
		switch ($paramArray["TYPE"])
		{
			case 'INT':
				$arParams[$paramName] = IntVal($arParams[$paramName]);
				break;

			case 'LIST':
				if (!array_key_exists($arParams[$paramName], $paramArray['VALUES']))
				{
					$arParams[$paramName] = $paramArray["DEFAULT"];
				}
				break;

			case 'CHECKBOX':
				$arParams[$paramName] = ($arParams[$paramName] == (is_set($paramArray,
						'VALUE') ? $paramArray['VALUE'] : 'Y'));
				break;

			default:
				// string etc.
				break;
		}
	}
}

foreach ($arResult["ITEMS"] as $key => &$arItem)
{
	if ($arItem["PREVIEW_PICTURE"])
	{
		if (is_int($arItem["PREVIEW_PICTURE"])) $arItem["PREVIEW_PICTURE"] = array("ID" => $arItem["PREVIEW_PICTURE"]);
		$arItem["PREVIEW_PICTURE"]["MIN"] = CFile::ResizeImageGet($arItem["PREVIEW_PICTURE"],
			array('width' => 360, 'height' => 360), BX_RESIZE_IMAGE_PROPORTIONAL, true);//BX_RESIZE_IMAGE_EXACT
	}
	//FIX remove A tags
	$arItem['PREVIEW_TEXT'] = preg_replace('{<a[^>]*>|<\/a>}si', '', $arItem['PREVIEW_TEXT']);
}

if ($arParams["GROUP_BY_MONTH"] == "Y")
{
	$grouped = array();
	foreach ($arResult["ITEMS"] as $key => &$arItem)
	{
		$dateTime = ParseDateTime($arItem["ACTIVE_FROM"]);
		$grouped[$dateTime["MM"] . '.' . $dateTime['YYYY']][] = $arItem;
	}
	$arResult["GROUPED"] = $grouped;
}
else
{
	$arResult['GROUPED'] = array($arResult["ITEMS"]);
}