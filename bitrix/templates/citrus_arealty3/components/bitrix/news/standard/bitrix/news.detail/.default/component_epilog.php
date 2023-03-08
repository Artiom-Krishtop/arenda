<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

/** @var array $arParams Параметры, чтение/изменение не затрагивает одноименный член компонента. */
/** @var array $arResult Результат, чтение/изменение не затрагивает одноименный член класса компонента. */
/** @var string $componentPath Путь к папке с компонентом от DOCUMENT_ROOT (например /bitrix/components/bitrix/iblock.list). */
/** @var CBitrixComponent $component Ссылка на $this. */
/** @var CBitrixComponent $this Ссылка на текущий вызванный компонент, можно использовать все методы класса. */
/** @var string $epilogFile Путь к файлу component_epilog.php относительно DOCUMENT_ROOT */
/** @var string $templateName Имя шаблона компонента (например: .dеfault) */
/** @var string $templateFile Путь к файлу шаблона от DOCUMENT_ROOT (напр. /bitrix/components/bitrix/iblock.list/templates/.default/template.php) */
/** @var string $templateFolder Путь к папке с шаблоном от DOCUMENT_ROOT (напр. /bitrix/components/bitrix/iblock.list/templates/.default) */
/** @var array $templateData Обратите внимание, таким образом можно передать данные из template.php в файл component_epilog.php, причем эти данные закешируются и будут доступны в component_epilog.php на каждом хите */

if ($arParams['DISPLAY_DATE'] !== 'N')
{
	$APPLICATION->SetPageProperty('PAGE_SUBHEADER', $arResult['PAGE_SUBHEADER']);
}

CJSCore::Init(['swiper', 'photoSwipe']);

// блок ВАШ ПЕРСОНАЛЬНЫЙ МЕНЕДЖЕР
if ($arResult["CONTACT"])
{
	?><a name="personal_manager" id="personal_manager"></a><?php

	$APPLICATION->IncludeComponent(
		"citrus.core:include",
		".default",
		[
			"AREA_FILE_SHOW" => "component",
			"_COMPONENT" => "citrus:template",
			"_COMPONENT_TEMPLATE" => "staff-block",
			"ITEM" => $arResult["CONTACT"],
			"h" => ".h2",
			"TITLE" => $arParams['CONTACT_TITLE'] ?: Loc::getMessage("CITRUS_AREATLY_PERSONAL_MANAGER_BLOCK"),
			"DESCRIPTION" => $arParams['CONTACT_DESC'] ?: Loc::getMessage("CITRUS_AREATLY_PERSONAL_MANAGER_BLOCK_DESC"),
			"PAGE_SECTION" => "Y",
			"PADDING" => "Y",
		],
		$component,
		['HIDE_ICONS' => 'Y']
	);
}
