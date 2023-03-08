<?php

use Bitrix\Main;
use Bitrix\Main\Localization\Loc;
use Citrus\Arealty\Components\ArealtySettingsWidgetComponent;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

Loc::loadMessages(__FILE__);

CBitrixComponent::includeComponentClass('citrus:settings.widget');
if (class_exists(ArealtySettingsWidgetComponent::class))
{
	/**
	 * Скроем лишний блок Мы поможем Вам
	 * Добавим блок захвата
	 */
	Main\EventManager::getInstance()
		->addEventHandler('citrus.arealty', ArealtySettingsWidgetComponent::EVENT_DEFINE_WIDGET_FIELDS, function (Main\Event $event) {

			static $fieldCode = 'BLOCKS';

			$fieldsByCode = array_column($event->getParameter('fields'), null, 'code');

			if (is_array($fieldsByCode) && isset($fieldsByCode[$fieldCode]))
			{
				$blocksByCode = array_column($fieldsByCode[$fieldCode]['values'], null, 'value');
				if (!is_array($blocksByCode))
				{
					return new Main\EventResult(Main\EventResult::ERROR);
				}

				/** @var ArealtySettingsWidgetComponent $sender */
				$sender = $event->getSender();

				// Скроем лишний блок Мы поможем Вам
				unset($blocksByCode['help-block']);

				// Добавим блок захвата
				$calloutBlock = [
					'label' => Loc::getMessage("CITRUS_AREALTY3_BLOCK_CALLOUT"),
					'value' => 'callout',
					'image' => SITE_TEMPLATE_PATH . '/application/distr/img/widget/blocks/callout.png',
					'checked' => $sender->getCurrent($fieldCode)["callout"] !== false,
				];

				if (false !== ($insertIndex = array_search('who-we', array_keys($blocksByCode), true)))
				{
					array_splice($blocksByCode, $insertIndex + 1, 0, [$calloutBlock]);
				}
				else
				{
					$blocksByCode[] = $calloutBlock;
				}

				$fieldsByCode[$fieldCode]['values'] = array_values($blocksByCode);

				$event->setParameter('fields', array_values($fieldsByCode));
			}

			return new Main\EventResult(Main\EventResult::SUCCESS);
		});
}
