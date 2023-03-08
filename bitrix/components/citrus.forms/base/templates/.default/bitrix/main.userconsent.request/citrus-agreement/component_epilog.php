<?php

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

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__DIR__.'/user_consent.php');

\CJSCore::RegisterExt('main_user_consent', Array(
	'js' => $templateFolder . '/user_consent.js',
	'css' => $templateFolder . '/user_consent.css',
	'lang' => $templateFolder . '/user_consent.php',
	'rel' =>   array('popup', 'ajax')
));
CUtil::InitJSCore(array('main_user_consent'));
