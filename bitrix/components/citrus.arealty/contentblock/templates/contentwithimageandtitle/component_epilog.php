<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

/** @var CBitrixComponent $component ������ �� ������� ��������� ���������, ����� ������������ ��� ������ ������. */

/**
 * ���� ��������� ��������� � ������� citrus.core:include, �������� ��������� � ����������� ����
 */
if ($component->getParent() instanceof \Citrus\Core\IncludeComponent)
{
	if ($arResult['SUBTITLE'])
	{
		$component->getParent()->arParams['DESCRIPTION'] = $arResult['SUBTITLE'];
	}
}