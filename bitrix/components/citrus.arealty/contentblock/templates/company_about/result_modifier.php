<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use function \Citrus\Core\array_get;

/** @var array $arParams ���������, ������, ���������. �� ����������� ����������� ���� ����������, �� ��������� ��� ������ �� $arParams � ����� template.php. */
/** @var array $arResult ���������, ������/���������. ����������� ����������� ���� ������ ����������. */
/** @var CBitrixComponentTemplate $this ������� ������ (������, ����������� ������) */

$this->getComponent()->setResultCacheKeys(['TITLE', 'SUBTITLE', 'CONTACT']);

$arResult['TITLE'] = array_get($arResult, 'DISPLAY_PROPERTIES.title.VALUE', $arResult['NAME']);
$arResult['SUBTITLE'] = array_get($arResult, 'DISPLAY_PROPERTIES.subtitle.VALUE');
$arResult['CONTACT_ID'] = array_get($arResult, 'PROPERTIES.contact.VALUE');
