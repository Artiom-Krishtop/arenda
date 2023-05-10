<?php

/** @var CBitrixComponent $this Текущий вызванный компонент */
/** @var array $arResult Массив результатов работы компонента */
/** @var array $arParams Массив входящих параметров компонента, может использоваться для учета заданных параметров при выводе шаблона (например, отображении детальных изображений или ссылок). */

/** @var CMain $APPLICATION */

use Bitrix\Main\Localization\Loc;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

Loc::loadMessages(__FILE__);

$APPLICATION->SetTitle($arParams['TITLE'] ?: Loc::getMessage("CITRUS_FZ152_AGREEMENT_TITLE"));

$site = CSite::GetByID($_GET['site'] ?: SITE_ID)->Fetch();
$serverName =
	empty($site["SERVER_NAME"])
		? COption::GetOptionString("main", "server_name", $_SERVER['HTTP_HOST'])
		: $site["SERVER_NAME"];

$officeInfo = false;
if (!$arParams['ADDRESS'] && \Bitrix\Main\Loader::includeModule('citrus.arealty'))
{
	try
	{
		$officeInfo = \Citrus\Arealty\Helper::getOfficeInfo();
	}
	catch (Exception $e)
	{
		$officeInfo = false;
	}
}

$arResult = array(
	'SITE' => $site,
	'VIEW_VARS' => array(
		'#SITE_NAME#' => $arParams['NAME'] ?: $site['NAME'],
		'#DOMAIN#' => $arParams['DOMAIN'] ?: $site['DOMAINS'] ?: SITE_SERVER_NAME ?: $_SERVER['HTTP_HOST'] . ($site['DIR'] == '/' ? '' : $site['DIR']),
		'#SITE_URL#' => $arParams['SITE_URL'] ?: (CMain::IsHTTPS() ? "https" : "http") . '://' . $serverName . $site['DIR'],
		'#ADDRESS#' => $arParams['ADDRESS'] ?: is_array($officeInfo) ? $officeInfo["PROPERTY_ADDRESS_VALUE"] : '...',
	),
);

$relativePath = $site['DIR'] . 'include/fz152_agreement.php';
$absolutePath = \Bitrix\Main\IO\Path::convertRelativeToAbsolute($relativePath);
$defaultPath = $componentPath . '/lang/' . LANGUAGE_ID . '/text.php';
$arResult['FILE_EXISTS'] = $fileFound = file_exists($absolutePath) && is_file($absolutePath) && is_readable($absolutePath);

if ($fileFound) {
	$arResult['FILE'] = $absolutePath;
}
else {
	$arResult['FILE'] = $_SERVER['DOCUMENT_ROOT'] . $defaultPath;
}

if ($APPLICATION->GetShowIncludeAreas())
{
	//need fm_lpa for every .php file, even with no php code inside
	$bPhpFile = (!$GLOBALS["USER"]->CanDoOperation('edit_php') && in_array(GetFileExtension($relativePath),
			GetScriptFileExt()));

	$bCanEdit = $USER->CanDoFileOperation('fm_edit_existent_file',
			array(SITE_ID, $relativePath)) && (!$bPhpFile || $GLOBALS["USER"]->CanDoFileOperation('fm_lpa',
				array(SITE_ID, $relativePath)));
	$bCanAdd = $USER->CanDoFileOperation('fm_create_new_file',
			array(SITE_ID, $relativePath)) && (!$bPhpFile || $GLOBALS["USER"]->CanDoFileOperation('fm_lpa',
				array(SITE_ID, $relativePath)));

	if ($bCanEdit || $bCanAdd)
	{
		$editor = '&site=' . SITE_ID . '&back_url=' . urlencode($_SERVER['REQUEST_URI']) . '&templateID=' . urlencode(SITE_TEMPLATE_ID);

		if ($fileFound)
		{
			if ($bCanEdit)
			{
				$arMenu = array();
				$arIcons = array(
					array(
						"URL" => 'javascript:' . $APPLICATION->GetPopupLink(array(
							'URL' => "/bitrix/admin/public_file_edit.php?lang=" . LANGUAGE_ID . "&from=main.include&template=" . urlencode($defaultPath) . "&path=" . urlencode($relativePath) . $editor,
							"PARAMS" => array(
								'width' => 770,
								'height' => 570,
								'resize' => true,
							),
						)),
						"DEFAULT" => $APPLICATION->GetPublicShowMode() != 'configure',
						"ICON" => "bx-context-toolbar-edit-icon",
						"TITLE" => Loc::getMessage('CITRUS_FZ152_AGREEMENT_EDIT'),
						"ALT" => '',
						"MENU" => $arMenu,
					),
				);
			}
		}
		elseif ($bCanAdd)
		{
			// @todo Указывать свой URL. сначала копировать файл, потом его сразу редактировать!
			$arMenu = array();
			$arIcons = array(
				array(
					"URL" => 'javascript:' . $APPLICATION->GetPopupLink(array(
						'URL' => "/bitrix/admin/public_file_edit.php?lang=" . LANGUAGE_ID . "&from=main.include&path=" . urlencode($relativePath) . "&new=Y&template=" . urlencode($defaultPath) . $editor,
						"PARAMS" => array(
							'width' => 770,
							'height' => 570,
							'resize' => true,
							"dialog_type" => 'EDITOR',
							"min_width" => 700,
							"min_height" => 400,
						),
					)),
					"DEFAULT" => $APPLICATION->GetPublicShowMode() != 'configure',
					"ICON" => "bx-context-toolbar-create-icon",
					"TITLE" => Loc::getMessage('CITRUS_FZ152_AGREEMENT_CREATE'),
					"ALT" => '',
					"MENU" => $arMenu,
				),
			);
		}

		if (isset($arIcons) && is_array($arIcons) && count($arIcons) > 0)
		{
			$this->AddIncludeAreaIcons($arIcons);
		}
	}
}

ob_start();
$this->IncludeComponentTemplate();
$result = ob_get_clean();
echo str_replace(array_keys($arResult['VIEW_VARS']), array_values($arResult['VIEW_VARS']), $result);
