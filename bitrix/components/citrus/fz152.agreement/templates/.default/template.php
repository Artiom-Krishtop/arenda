<?php

use Bitrix\Main\Localization\Loc;

/** @var CBitrixComponent $component ������� ��������� ��������� */
/** @var CBitrixComponentTemplate $this ������� ������ (������, ����������� ������) */
/** @var array $arResult ������ ����������� ������ ���������� */
/** @var array $arParams ������ �������� ���������� ���������� */
/** @var CMain $APPLICATION */

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$APPLICATION->SetTitle(Loc::getMessage('CITRUS_FZ152_AGREEMENT_TITLE'));

include $arResult['FILE'];
