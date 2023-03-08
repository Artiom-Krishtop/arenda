<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

//CBitrixComponent::includeComponentClass('citrus:settings.widget');

$arResult['ACTIVE'] = true;
$arResult['CAN_EDIT'] = $GLOBALS['USER']->IsAdmin();

if ($arParams['WIDGET_REL'])
{
	/**
	 * @todo Активность блока нужно получать из настроек.
	 *
	 * Например, так:
	 * $arResult['ACTIVE'] = \Citrus\Arealty\Entity\SettingsTable::getValue('BLOCKS', SITE_ID, false)[$arParams['WIDGET_REL']];
	 */
}
