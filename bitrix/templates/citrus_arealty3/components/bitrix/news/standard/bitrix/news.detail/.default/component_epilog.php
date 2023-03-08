<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

/** @var array $arParams ���������, ������/��������� �� ����������� ����������� ���� ����������. */
/** @var array $arResult ���������, ������/��������� �� ����������� ����������� ���� ������ ����������. */
/** @var string $componentPath ���� � ����� � ����������� �� DOCUMENT_ROOT (�������� /bitrix/components/bitrix/iblock.list). */
/** @var CBitrixComponent $component ������ �� $this. */
/** @var CBitrixComponent $this ������ �� ������� ��������� ���������, ����� ������������ ��� ������ ������. */
/** @var string $epilogFile ���� � ����� component_epilog.php ������������ DOCUMENT_ROOT */
/** @var string $templateName ��� ������� ���������� (��������: .d�fault) */
/** @var string $templateFile ���� � ����� ������� �� DOCUMENT_ROOT (����. /bitrix/components/bitrix/iblock.list/templates/.default/template.php) */
/** @var string $templateFolder ���� � ����� � �������� �� DOCUMENT_ROOT (����. /bitrix/components/bitrix/iblock.list/templates/.default) */
/** @var array $templateData �������� ��������, ����� ������� ����� �������� ������ �� template.php � ���� component_epilog.php, ������ ��� ������ ������������ � ����� �������� � component_epilog.php �� ������ ���� */

if ($arParams['DISPLAY_DATE'] !== 'N')
{
	$APPLICATION->SetPageProperty('PAGE_SUBHEADER', $arResult['PAGE_SUBHEADER']);
}

CJSCore::Init(['swiper', 'photoSwipe']);

// ���� ��� ������������ ��������
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
