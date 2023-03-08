<?php

namespace Citrus\Arealty\Template;

use Citrus\Arealty\Entity\SettingsTable;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

function isActiveBlock($blockCode)
{
	return SettingsTable::getValue('BLOCKS', SITE_ID, false)[$blockCode];
}

function isShowBlock($blockCode)
{
	global $USER;
	return isActiveBlock($blockCode) || $USER->IsAdmin();
}

function hiddenCss($blockCode= '')
{
	return isActiveBlock($blockCode) ? '' : 'style="display: none;"';
}

function includeFormStyles()
{
	global $APPLICATION;

	$APPLICATION->SetAdditionalCSS(getLocalPath('components/citrus.forms/base/templates/simple/style.css'));
}

/**
 * @param string $partCode ��� ���������
 * @param array $params �������������� ��������� <pre>
 * [
 *      'shown-by-default' => false // ���������� �������� �� ���������. ���� false, �������� ������������ ������ ���� ���� ������� � ��������� ��������
 *      ...
 * ]
 * </pre>
 * @return bool true ���� ����� ��������� �������� � ��������� ��������
 * @internal
 * @see showPart()
 */
function isPartShown($partCode, array $params = [])
{
	global $APPLICATION;

	return $APPLICATION->GetProperty('SHOW_' . strtoupper(str_replace('-', '_', $partCode)), empty($params['shown-by-default']) ? 'Y' : 'N') !== 'N';
}

/**
 * ������� *�������� �������*
 *
 * ��������� � ����� �������, ������������� � ����� partials ������� �����.
 *
 * �������� ����� �������� ��� ��������� {@link https://dev.1c-bitrix.ru/learning/course/index.php?COURSE_ID=43&LESSON_ID=2814#properties ��������� �������� } � ����� `SHOW_<��� ���������>` (�� ���������� Y ��� N).
 * `<��� ���������>` ��� �������� `$partCode` ��� ������ �������� �� ������� ������������� (��������, ��� header-logo � SHOW_HEADER_LOGO)
 *
 * @param string $partCode ��� ���������.
 * @param array $params �������������� ��������� ������ ���������, �������� � ����� ��������� <pre>
 * [
 *      'view-target' => '' ,       // ���� �����, ���������� ����� �������� � $APPLICATION->AddViewContent($params['view-target'])
 *      'shown-by-default' => false // ���������� �������� �� ���������. ���� false, �������� ������������ ������ ���� ���� ������� � ��������� ��������
 *      ...
 * ]
 * </pre>
 * @return bool true ���� �������� ���������, false ���� ���� ��������� �� ������ ��� �� �������� � ��������� ��������
 * @todo �������� ����������� ������������ ������� ���������������� ��������� ���������������� ����������
 */
function showPart($partCode, array $params = [])
{
	global $APPLICATION;

	$partCode = preg_replace('#[^a-z0-9_.-]#im', '', $partCode);
	$partPath = $_SERVER['DOCUMENT_ROOT'] . SITE_TEMPLATE_PATH . '/partials/' . $partCode . '.php';

	if (!file_exists($partPath) || !is_readable($partPath) || !isPartShown($partCode, $params))
	{
		return false;
	}

	$getContents = function() use ($partPath, $params) {
		global /** @noinspection PhpUnusedLocalVariableInspection */ $APPLICATION, $USER;

		extract($params, EXTR_OVERWRITE);

		ob_start();
		/** @noinspection PhpIncludeInspection */
		include $partPath;
		return ob_get_clean();
	};

	if (!empty($params['view-target']))
	{
		$APPLICATION->AddViewContent($params['view-target'], $getContents());
	}
	else
	{
		echo $getContents();
	}

	return true;
}

function demoNotice()
{
	include $_SERVER["DOCUMENT_ROOT"] . BX_ROOT . "/modules/citrus.arealty/install/demo.php";

	/** @noinspection IssetArgumentExistenceInspection */
	if (isset($checkCitrusDemo) && \is_callable($checkCitrusDemo))
	{
		if (IsModuleInstalled("citrus.arealtypro"))
		{
			$checkCitrusDemo("citrus.arealtypro");
		}
		$checkCitrusDemo();
	}
}