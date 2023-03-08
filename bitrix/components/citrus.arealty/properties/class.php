<?php

namespace Citrus\Arealty;

use Bitrix\Main\ArgumentException;
use Citrus\Arealty\Template\Property;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

\Bitrix\Main\Loader::includeModule('citrus.core');

class PropertesComponent extends \CBitrixComponent
{
	/** @var string */
	private $cssClass;
	/** @var Property */
	private $properties;
	/** @var string[] */
	private $displayProperties;

	protected function validateParams($arParams)
	{
		if (
			(!isset($arParams['PROPERTIES']) || !is_array($arParams['PROPERTIES']))
			&& (!isset($arParams['ELEMENT']) || !is_array($arParams['ELEMENT']))
		)
		{
			throw new ArgumentException('Missing PROPERTIES parameter');
		}

		if (!isset($arParams['DISPLAY_PROPERTIES']) || !is_array($arParams['DISPLAY_PROPERTIES']))
		{
			throw new ArgumentException('Missing DISPLAY_PROPERTIES parameter');
		}
	}

	public function onPrepareComponentParams($arParams)
	{
		$this->validateParams($arParams);

		$element =
			$arParams['ELEMENT']
				?: [
					'PROPERTIES' => $arParams['PROPERTIES'],
				];

		$this->properties = new Property($element);

		$this->displayProperties = $arParams['DISPLAY_PROPERTIES'];

		$this->cssClass = empty($arParams["CSS_CLASS"]) ? '' : trim($arParams['CSS_CLASS']);

		return parent::onPrepareComponentParams($arParams);
	}

	public function executeComponent()
	{
		$this->includeComponentTemplate();
	}

	/**
	 * @return string
	 */
	public function getCssClass()
	{
		return $this->cssClass;
	}

	/**
	 * @return Property
	 */
	public function getProperties()
	{
		return $this->properties;
	}

	/**
	 * @return string[]
	 */
	public function getDisplayProperties()
	{
		return $this->displayProperties;
	}
}
