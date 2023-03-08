<?php

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Web\Json;

/** @var $USER CUser */
/** @var $APPLICATION CMain */

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
{
	define('PUBLIC_AJAX_MODE', true);
	if (!defined('SITE_ID') && isset($_POST['site_id']))
	{
		define('SITE_ID', htmlspecialchars(trim($_POST['site_id'])));
	}
	require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';
}

if (!isset($arParams))
{
	$arParams = array('JS_KEY' => md5(LICENSE_KEY));
}

$post = $_POST;

if ($post['citrus_subscribe'] == 'Y' && strlen(trim($post['citrus_email'])) &&
	$post['citrus_key'] == md5(
		$arParams['JS_KEY'] .
		(isset($post['citrus_not_confirm']) ? $post['citrus_not_confirm'] : '')
	) &&
	check_bitrix_sessid()
)
{
	$arReturn = array();
	Loc::loadMessages(__FILE__);

	if ($post['fz152'] != 'Y')
	{
		$arReturn = array('message' => Loc::getMessage("CITRUS_AREALTY_SUBSCRIBE_FZ152_REQ"), 'status' => 'error');
	}
	elseif (CModule::IncludeModule('subscribe'))
	{
		$rubricIds = \Citrus\Arealty\Cache::remember('SUBSCRIBE_RUB_IDS', 60, function () {
			$result = array();
			if (CModule::IncludeModule('subscribe'))
			{
				$rubricIterator = CRubric::GetList(array(), array('ACTIVE' => 'Y', 'LID' => SITE_ID));
				while ($rubric = $rubricIterator->GetNext())
				{
					$result[$rubric['ID']] = 1;
				}
			}
			return array_keys($result);
		});

		$email = trim($post['citrus_email']);
		$charset = $post['charset'];
		$format = trim($post['citrus_format']);

		$arFields = Array(
			'USER_ID' => $USER->GetID(),
			'SEND_CONFIRM' => $post['citrus_not_confirm'] == 'Y' ? 'N' : 'Y',
			'EMAIL' => $email,
			'FORMAT' => $format,
			'ACTIVE' => 'Y',
			'RUB_ID' => $rubricIds,
			'CONFIRMED' => $post['citrus_not_confirm'] == 'Y' ? 'Y' : 'N',
		);
		$subscription = new CSubscription();
		if ($newID = $subscription->Add($arFields))
		{
			$arReturn = array('message' => Loc::getMessage('ASD_CMP_SUCCESS' . ($post['citrus_not_confirm'] == 'Y' ? '_NC' : '')), 'status' => 'ok');
		}
		elseif ($ex = $APPLICATION->GetException())
		{
			$arReturn = array('message' => $ex->GetString(), 'status' => 'error');
		}
	}
	else
	{
		$arReturn = array('message' => Loc::getMessage('ASD_CMP_NOT_INSTALLED'), 'status' => 'error');
	}

	if (defined('PUBLIC_AJAX_MODE') && PUBLIC_AJAX_MODE === true)
	{
		$arReturn['message'] = strip_tags($arReturn['message']);
		header('Content-type: application/json; charset=' . SITE_CHARSET);
		echo Json::encode($arReturn);
	}
	else
	{
		return $arReturn;
	}
}

if (defined('PUBLIC_AJAX_MODE') && PUBLIC_AJAX_MODE === true)
{
	require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_after.php';
}