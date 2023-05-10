<?php

/** @var CBitrixComponent $this */
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use Citrus\Arealty\Entity\CurrenciesTable;
use Citrus\Arealty\Helper;
use Citrus\Arealty\Entity\SettingsTable;

$arParams["CURRENT"] = Helper::getSelectedCurrency();
$arParams['BASE'] = SettingsTable::getValue('CURRENCY');
$arParams["CURRENT_CURRENCY_FACTOR"] = SettingsTable::getValue('CURRENCY_FACTOR');
$arResult['FACTORS'] = Helper::getFactorArray();
$arResult['ITEMS'] = array();

if ($this->StartResultCache())
{
	$arCurrencies = CurrenciesTable::getActiveCurrencies();

	if (!count($arCurrencies))
	{
		$defaultCurrencyList = CurrenciesTable::getDefaultCurrencyList();
		$arCurrencies[] = $defaultCurrencyList[0];
	}

	foreach ($arCurrencies as $i => &$currency)
	{
		$currency["SELECTED"] = $arParams["CURRENT"] === $currency["CODE"];
		if ($currency["SELECTED"])
		{
			$currency['FACTOR'] = $arParams["CURRENT_CURRENCY_FACTOR"];
		}

		$arResult['ITEMS'][$currency['CODE']] = $currency;
	}

	$this->IncludeComponentTemplate();
}
