<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

/** @var CitrusArealtyCatalogSectionComponent $component Текущий вызванный компонент */
/** @var CBitrixComponentTemplate $this Текущий шаблон (объект, описывающий шаблон) */
/** @var array $arResult Массив результатов работы компонента */
/** @var array $arParams Массив входящих параметров компонента, может использоваться для учета заданных параметров при выводе шаблона (например, отображении детальных изображений или ссылок). */
/** @var string $templateFile Путь к шаблону относительно корня сайта, например /bitrix/components/bitrix/iblock.list/templates/.default/template.php) */
/** @var string $templateName Имя шаблона компонента (например: .dеfault) */
/** @var string $templateFolder Путь к папке с шаблоном от DOCUMENT_ROOT (например /bitrix/components/bitrix/iblock.list/templates/.default) */
/** @var array $templateData Массив для записи, обратите внимание, таким образом можно передать данные из template.php в файл component_epilog.php, причем эти данные попадают в кеш, т.к. файл component_epilog.php исполняется на каждом хите */
/** @var string $parentTemplateFolder Папка родительского шаблона. Для подключения дополнительных изображений или скриптов (ресурсов) удобно использовать эту переменную. Ее нужно вставлять для формирования полного пути относительно папки шаблона */
/** @var string $componentPath Путь к папке с компонентом от DOCUMENT_ROOT (напр. /bitrix/components/bitrix/iblock.list) */

$this->setFrameMode(true);

if (!empty($arParams['VIEW_TARGET']))
{
	$this->SetViewTarget($arParams['VIEW_TARGET']);
}

$APPLICATION->IncludeComponent(
	'bitrix:catalog.section.list',
	$templateName,
	$arParams,
	$component
);
