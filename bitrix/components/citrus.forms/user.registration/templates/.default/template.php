<?php

/** @var \Citrus\Forms\IblockElementComponent $component Текущий вызванный компонент */

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

if (array_key_exists('saccess', $_REQUEST) && $_REQUEST['saccess'] === 'Y')
{
	if ($arParams['ACTIVE'] == "N")
	{
		if (isset($arParams['USE_MAIN_Y_OKMESSAGE']) && strlen($arParams['USE_MAIN_Y_OKMESSAGE']) > 0)
		{
			ShowNote($arParams['USE_MAIN_Y_OKMESSAGE']);
		}
		else
		{
			ShowNote(Loc::getMessage('TPL_UREGFORM_SUCCESS_NOT_NEDD_ACTIVE'));
		}
	}
	else
	{
		ShowNote(Loc::getMessage('TPL_UREGFORM_SUCCESS_NEDD_ACTIVE'));
	}
}
else
{
	\Citrus\Forms\includeFormTemplate($component);
}


