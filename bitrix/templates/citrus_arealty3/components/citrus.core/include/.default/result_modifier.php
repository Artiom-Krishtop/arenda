<?php

use Citrus\Arealty\Components\ArealtySettingsWidgetComponent;
use Citrus\Arealty\Entity\SettingsTable;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

CBitrixComponent::includeComponentClass('citrus:settings.widget');

$arResult['ACTIVE'] = true;
$arResult['CAN_EDIT'] = $APPLICATION->GetUserRight('citrus.arealty') >= 'W' || ArealtySettingsWidgetComponent::$wasIncluded;

if ($arParams['WIDGET_REL'])
{
    $blocksSetting = SettingsTable::getValue('BLOCKS', SITE_ID, false);

    /**
     * Кастомные блоки (добавленные в шаблоне, например), будут отсутствовать в значении по умолчанию.
     * Блоаки нужно по умолчанию показывать
     *
     * Выключенные блоки будут === false, новые кастомные — null
     */
	$arResult['ACTIVE'] = $blocksSetting[$arParams['WIDGET_REL']] !== false;
}
