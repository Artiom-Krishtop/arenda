<?php

namespace Citrus\Core\Components;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\HttpRequest;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\NotImplementedException;

Loc::loadMessages(__FILE__);

if (!\Bitrix\Main\Loader::includeModule('citrus.core'))
{
	return;
}

class CoreSettingsWidgetComponent extends AbstractComponent
{
	use DefaultParamsTrait;

	const FIELD_TYPE_COLOR = 'colorScheme';
	const FIELD_TYPE_IMAGE = 'avatar';
	const FIELD_TYPE_TEXT = 'text';
	const FIELD_TYPE_CHECKBOX = 'checkbox';
	const FIELD_TYPE_SELECT = 'select';
	const FIELD_TYPE_BLOCKS = 'blocks';

	public static $wasIncluded = false;

	public function getWidgetTabs()
	{
		throw new NotImplementedException(__FUNCTION__ . '() method in ' . static::class . ' is not implemented');
	}

	public function getWidgetFields()
	{
		throw new NotImplementedException(__FUNCTION__ . '() method in ' . static::class . ' is not implemented');
	}

	public function saveAction(HttpRequest $request, array $serializedParams)
	{
		throw new NotImplementedException(__FUNCTION__ . '() method in ' . static::class . ' is not implemented');
	}

	public function colorAction(HttpRequest $request, array $serializedParams)
	{
		throw new NotImplementedException(__FUNCTION__ . '() method in ' . static::class . ' is not implemented');
	}

	protected function getAjaxPath()
	{
		return getLocalPath('components' . \CComponentEngine::MakeComponentPath('citrus.core:settings.widget') . '/ajax.php');
	}

	/**
	 * @return ParamsSerializer
	 */
	protected function getParamsSerializer()
	{
		if (!isset($this->paramsSerializer))
		{
			$this->paramsSerializer = new ParamsSerializer(null, $this->getName());
		}

		return $this->paramsSerializer;
	}

	public static function buildComponent($componentName, $params)
	{
		$parameters = (new ParamsSerializer(null, $componentName))->unserialize($params);

		if (!is_array($parameters))
		{
			throw new \RuntimeException('Unserialization failed');
		}

		if ($parameters['c'] !== $componentName)
		{
			throw new \RuntimeException('Wrong parameters');
		}

		if ($parameters['sessid'] !== bitrix_sessid())
		{
			throw new \RuntimeException(Loc::getMessage('ACCESS_DENIED'));
		}

		$component = new \CBitrixComponent;
		$component->initComponent($componentName);

		$class = \CBitrixComponent::includeComponentClass($componentName);

		// method returns nothing prior to 18.0
		if (!$class)
		{
			$class = $parameters['class'];
		}

		if (!is_subclass_of($class, static::class))
		{
			throw new \RuntimeException('Component class check failed');
		}

		/** @var \CBitrixComponent $component */
		$component = new $class();

		$component->initComponent($componentName, $parameters['template']);
		$component->onIncludeComponentLang();
		//$component->arParams = $component->onPrepareComponentParams($parameters['arParams']);
		$component->arParams = $parameters['arParams'];
		$component->__prepareComponentParams($component->arParams);

		return [
			$component,
			$parameters,
		];
	}

	public function getAjaxActionPath($action, array $params)
	{
		$params = array_filter($params, function ($v, $k) {
			return strlen($k) > 1 && $k[0] !== '~';
		}, ARRAY_FILTER_USE_BOTH);

		return $this->getAjaxPath() . '?' . http_build_query([
				'siteId' => SITE_ID,
				'c' => $this->getName(),
				'action' => $action,
				'params' => $this->getParamsSerializer()->serialize([
					'c' => $this->getName(),
					'class' => static::class,
					'action' => $action,
					'template' => $this->getTemplateName(),
					'arParams' => $params,
					'sessid' => bitrix_sessid(),
				]),
			]);
	}

	protected function getJsSettingsData()
	{
		$settingsData = [
			'tabs' => $this->getWidgetTabs(),
			'fields' => $this->getWidgetFields(),
			'arParams' => [
				'actions' => [
					'save' => $this->getAjaxActionPath('save', $this->arParams),
					'color' => $this->getAjaxActionPath('color', $this->arParams),
				],
				'fieldSettings' => $this->arResult['fieldSettings'],
			],
		];

		return $settingsData;
	}

	final protected function getTemplateComponentName()
	{
		return 'citrus.core:settings.widget.template';
	}

	final protected function includeTemplate($templatePage = '')
	{
		global $APPLICATION;

		$APPLICATION->IncludeComponent(
			$this->getTemplateComponentName(),
			$this->getTemplateName(),
			[
				'DATA' => $this->getJsSettingsData(),
				'LANG' => $this->arResult['LANG']
			],
			$this
		);
	}

	public function execute()
	{
		static::$wasIncluded = true;

		/**
		 * Запретим композит при включенном демо-режиме.
		 * Композит будет обновлять общий файл кеша с пользовательскими настроками из сессии
		 */
		if ($this->arParams['DEMO_MODE'])
		{
			\Bitrix\Main\Data\StaticHtmlCache::getInstance()->markNonCacheable();
		}

		$this->includeTemplate();
	}
}
