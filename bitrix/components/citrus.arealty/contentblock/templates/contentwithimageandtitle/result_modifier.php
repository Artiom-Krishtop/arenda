<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/** @var array $arParams ���������, ������, ���������. �� ����������� ����������� ���� ����������, �� ��������� ��� ������ �� $arParams � ����� template.php. */
/** @var array $arResult ���������, ������/���������. ����������� ����������� ���� ������ ����������. */
/** @var CBitrixComponentTemplate $this ������� ������ (������, ����������� ������) */

$this->getComponent()->setResultCacheKeys(['SUBTITLE']);
$arResult['SUBTITLE'] = $arResult['DISPLAY_PROPERTIES']['subtitle']['VALUE'];
