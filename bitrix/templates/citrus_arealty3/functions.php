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
 * @param string $partCode Код фрагмента
 * @param array $params Дополнительные параметры <pre>
 * [
 *      'shown-by-default' => false // показывать фрагмент по умолчанию. если false, фрагмент показывается только если явно включен в свойствах страницы
 *      ...
 * ]
 * </pre>
 * @return bool true если показ фрагмента разрешен в свойствах страницы
 * @internal
 * @see showPart()
 */
function isPartShown($partCode, array $params = [])
{
	global $APPLICATION;

	return $APPLICATION->GetProperty('SHOW_' . strtoupper(str_replace('-', '_', $partCode)), empty($params['shown-by-default']) ? 'Y' : 'N') !== 'N';
}

/**
 * Выводит *фрагмент шаблона*
 *
 * Фрагменты — части шаблона, располагаются в папке partials шаблона сайта.
 *
 * Фрагмент можно включить или выключить {@link https://dev.1c-bitrix.ru/learning/course/index.php?COURSE_ID=43&LESSON_ID=2814#properties свойством страницы } с кодом `SHOW_<код фрагмента>` (со значениями Y или N).
 * `<код фрагмента>` это аргумент `$partCode` где дефисы заменены на символы подчеркивания (например, для header-logo — SHOW_HEADER_LOGO)
 *
 * @param string $partCode Код фрагмента.
 * @param array $params Дополнительные параметры вывода фрагмента, доступны в файле фрагмента <pre>
 * [
 *      'view-target' => '' ,       // если задан, содержимое будет передано в $APPLICATION->AddViewContent($params['view-target'])
 *      'shown-by-default' => false // показывать фрагмент по умолчанию. если false, фрагмент показывается только если явно включен в свойствах страницы
 *      ...
 * ]
 * </pre>
 * @return bool true если фрагмент подключен, false если файл фрагмента не найден или он выключен в свойствах страницы
 * @todo Добавить возможность кастомизации шаблона переопределением фрагмента пользовательским содержимом
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