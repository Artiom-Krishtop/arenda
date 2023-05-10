<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

/** @var array $arParams ���������, ������, ���������. �� ����������� ����������� ���� ����������, �� ��������� ��� ������ �� $arParams � ����� template.php. */
/** @var array $arResult ���������, ������/���������. ����������� ����������� ���� ������ ����������. */
/** @var CBitrixComponentTemplate $this ������� ������ (������, ����������� ������) */

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
