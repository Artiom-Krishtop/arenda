<?php

use Bitrix\Main;
use Bitrix\Main\Localization\Loc;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

Loc::loadMessages(__FILE__);

Main\EventManager::getInstance()
	->addEventHandler('citrus.arealtypro', 'onManageObjectsFormShow', function (Main\Event $event) {

		$component = $event->getParameter('component');

		/**
		 * Уберем кнопку выхода из ЛК (она уже есть в шапке)
		 */
		$component->arResult['TOOLBAR_BUTTONS'] = array_filter($component->arResult['TOOLBAR_BUTTONS'], function ($v) {
			return strpos($v['LINK'], 'logout=yes') === false;
		});

		return new Main\EventResult(Main\EventResult::SUCCESS);
	});

