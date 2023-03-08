<?php

use Bitrix\Main\Localization\Loc;

/** @var CBitrixComponent $component Текущий вызванный компонент */
/** @var CBitrixComponentTemplate $this Текущий шаблон (объект, описывающий шаблон) */
/** @var array $arResult Массив результатов работы компонента */
/** @var array $arParams Массив входящих параметров компонента */
/** @var CMain $APPLICATION */

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$APPLICATION->SetTitle(Loc::getMessage('CITRUS_FZ152_AGREEMENT_TITLE'));

include $arResult['FILE'];
