<?php

namespace Citrus\Core\Components;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

if (!\Bitrix\Main\Loader::includeModule('citrus.core'))
{
	return;
}

class CoreSettingsWidgetComponentTemplate extends AbstractComponent
{
	protected function execute()
	{
		$this->arResult['DATA'] = $this->arParams['DATA'];
		$this->arResult['LANG'] = $this->arParams['LANG'];

		$this->includeComponentTemplate();
	}
}
