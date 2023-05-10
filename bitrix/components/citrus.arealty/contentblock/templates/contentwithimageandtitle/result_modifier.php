<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/** @var array $arParams Параметры, чтение, изменение. Не затрагивает одноименный член компонента, но изменения тут влияют на $arParams в файле template.php. */
/** @var array $arResult Результат, чтение/изменение. Затрагивает одноименный член класса компонента. */
/** @var CBitrixComponentTemplate $this Текущий шаблон (объект, описывающий шаблон) */

$this->getComponent()->setResultCacheKeys(['SUBTITLE']);
$arResult['SUBTITLE'] = $arResult['DISPLAY_PROPERTIES']['subtitle']['VALUE'];
