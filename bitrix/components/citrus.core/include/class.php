<?php

namespace Citrus\Core;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

\Bitrix\Main\Loader::includeModule('citrus.core');

class IncludeComponent extends \CBitrixComponent
{
	public function onPrepareComponentParams($arParams)
	{
		if (!empty($arParams['CLASS']))
		{
			$arParams['CLASS'] = is_array($arParams['CLASS'])
				? implode(' ', $arParams['CLASS'])
				: $arParams['CLASS'];
		}
		else
		{
			$arParams['CLASS'] = '';
		}

		return parent::onPrepareComponentParams($arParams);
	}
}
