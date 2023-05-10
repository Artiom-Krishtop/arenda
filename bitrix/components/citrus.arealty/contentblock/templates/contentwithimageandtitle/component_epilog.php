<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

/** @var CBitrixComponent $component Ссылка на текущий вызванный компонент, можно использовать все методы класса. */

/**
 * Если компонент подключен в составе citrus.core:include, заполним заголовок и подзаголвок блоа
 */
if ($component->getParent() instanceof \Citrus\Core\IncludeComponent)
{
	if ($arResult['SUBTITLE'])
	{
		$component->getParent()->arParams['DESCRIPTION'] = $arResult['SUBTITLE'];
	}
}