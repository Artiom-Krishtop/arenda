<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

/** @var array $arParams Параметры, чтение, изменение. Не затрагивает одноименный член компонента, но изменения тут влияют на $arParams в файле template.php. */
/** @var array $arResult Результат, чтение/изменение. Затрагивает одноименный член класса компонента. */
/** @var CBitrixComponentTemplate $this Текущий шаблон (объект, описывающий шаблон) */

$this->getComponent()->setResultCacheKeys(['PAGE_SUBHEADER']);

$meta = [];
foreach ($arParams['PROPERTY_CODE'] as $property)
{
	if ($arResult["PROPERTIES"][$property]["VALUE"])
	{
		$meta[] = $arResult["PROPERTIES"][$property]["VALUE"];
	}
}

$arResult['PAGE_SUBHEADER'] = implode(', ', array_filter($meta));
