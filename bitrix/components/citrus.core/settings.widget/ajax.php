<?php

define("NO_KEEP_STATISTIC", true);
define("NO_AGENT_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);

if (isset($_GET['siteId']))
{
	define('SITE_ID', $_GET['siteId']);
}
require $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php";

use Bitrix\Main\Web\Json;
use Bitrix\Main\Web\PostDecodeFilter;
use Citrus\Core\Components\CoreSettingsWidgetComponent;

CBitrixComponent::includeComponentClass('citrus.core:settings.widget');

try
{
	$request = \Bitrix\Main\Context::getCurrent()->getRequest();

	$request->addFilter(new PostDecodeFilter());

	/** @var CoreSettingsWidgetComponent $component */
	list($component, $parameters) = CoreSettingsWidgetComponent::buildComponent($request->get('c'), $request->get('params'));

	$actionName = strtolower($parameters['action']);
	if (strlen($actionName) && method_exists($component, $actionName . 'Action') && is_callable([$component, $actionName . 'Action']))
	{
		$response = call_user_func_array([$component, $actionName . 'Action'], [$request, $parameters]);
	}
	else
	{
		throw new RuntimeException('Incorrect action');
	}
}
catch (Exception $e)
{
	$response = [
		'error' => $e->getMessage(),
		'code' => $e->getCode(),
	];
	if ($e->getPrevious())
	{
		$response['details'] = $e->getPrevious()->getMessage();
	}
	CHTTP::SetStatus('503 Service Unavailable');
}

echo Json::encode($response);


