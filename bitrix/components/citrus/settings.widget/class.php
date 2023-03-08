<?php

namespace Citrus\Arealty\Components;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Application;
use Bitrix\Main\Entity\Result;
use Bitrix\Main\Event;
use Bitrix\Main\HttpRequest;
use Bitrix\Main\IO\Directory;
use Bitrix\Main\IO\Path;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\NotImplementedException;
use Citrus\Arealty\Entity\CurrenciesTable;
use Citrus\Arealty\Entity\SettingsTable;
use Citrus\Arealty\Helper;
use Citrus\Arealty\Theme;
use Citrus\Arealty\ThemeBuilder;
use Citrus\Core\Components\RequiredModulesTrait;
use Citrus\Core\Components\CoreSettingsWidgetComponent;
use Bitrix\B24Connector\Connection;

Loc::loadMessages(__FILE__);

\CBitrixComponent::includeComponentClass('citrus.core:settings.widget');

class ArealtySettingsWidgetComponent extends CoreSettingsWidgetComponent
{
	use RequiredModulesTrait;

	const TEMP_THEME_HOUSES_TO_KEEP = 24;
	const EVENT_DEFINE_WIDGET_FIELDS = 'OnDefineWidgetFields';

	public $requiredModules = ['citrus.arealty'];

	public function getWidgetTabs()
	{
		return [
			[
				'title' => Loc::getMessage('CITRUS_AREALTY_SETTINGS_WIDGET_TAB_COMMON'),
				'sections' => [
					[
						'title' => Loc::getMessage('CITRUS_AREALTY_SETTINGS_WIDGET_TAB_COMMON_COLORS'),
						'code' => 'scheme',
					],
					[
						'title' => Loc::getMessage('CITRUS_AREALTY_SETTINGS_WIDGET_TAB_COMMON_HEAD'),
						'code' => 'header',
					],
				],
			],
			[
				'title' => Loc::getMessage('CITRUS_AREALTY_SETTINGS_WIDGET_TAB_BLOCKS'),
				'sections' => [
					[
						'title' => Loc::getMessage('CITRUS_AREALTY_SETTINGS_WIDGET_TAB_BLOCKS_DATA'),
						'code' => 'mp_blocks',
					],
				],
			],
			[
				'title' => Loc::getMessage('CITRUS_AREALTY_SETTINGS_WIDGET_TAB_INFO'),
				'sections' => [
					[
						'title' => Loc::getMessage('CITRUS_AREALTY_SETTINGS_WIDGET_TAB_INFO_CONTACTS'),
						'code' => 'contacts',
					],
				],
			],
			[
				'title' => Loc::getMessage('CITRUS_AREALTY_SETTINGS_WIDGET_TAB_CURRENCY'),
				'sections' => [
					[
						'title' => Loc::getMessage('CITRUS_AREALTY_SETTINGS_WIDGET_TAB_CURRENCY'),
						'code' => 'currency',
					],
				],
			],
		];
	}

	protected function getWidgetColors(Theme $theme)
	{
		$themes = array_filter($theme->getTemplateThemes(), function ($a) { return $a['isStandard'] === true; });
		$colors = array_column($themes, 'color');

		return array_map(function ($c) {
			return '#' . $c;
		}, $colors);
	}

	public function getCurrent($setting, $forDisplay = false)
	{
		return SettingsTable::getValue($setting, SITE_ID, $forDisplay);
	}

	public function getWidgetFields()
	{
		$blockImagePath = SITE_TEMPLATE_PATH . '/application/distr/img/widget/blocks/';

		$currencies = array_map(function ($row) {
			return [
				'label' => $row["NAME"],
				'value' => $row["CODE"],
				'factor' => $row["FACTOR"],
				'sign' => $row["SIGN"],
				'show_after' => $row["SHOW_AFTER"],
			];
		}, CurrenciesTable::getActiveCurrencies());

		$priceViews = array_map(function ($k, $v) {
			return [
				'label' => $v,
				'value' => $k,
			];
		}, array_keys(Helper::getAvailablePriceView()), array_values(Helper::getAvailablePriceView()));

		$theme = new Theme(SITE_ID);

		$result = [
			[
				'title' => Loc::getMessage("CITRUS_AREALTY_SETTINGS_WIDGET_COLOR_SCHEME"),
				'section' => 'scheme',
				'code' => 'SCHEME',
				'type' => static::FIELD_TYPE_COLOR,
				'values' => $this->getWidgetColors($theme),
				'value' => '#' . $theme->getColor(),
				'settings' => [],
			],
			[
				'title' => Loc::getMessage("CITRUS_AREALTY_SETTINGS_WIDGET_LOGO"),
				'section' => 'header',
				'type' => static::FIELD_TYPE_IMAGE,
				'code' => 'LOGO',
				'value' => $this->getCurrent('LOGO', true),
				'settings' => [
					'accept' => '.jpg, .jpeg, .png',
				]
			],
			[
				'title' => Loc::getMessage("CITRUS_AREALTY_SETTINGS_WIDGET_FAVICON"),
				'section' => 'header',
				'type' => static::FIELD_TYPE_IMAGE,
				'code' => 'FAVICON',
				'value' => $this->getCurrent('FAVICON', true),
				'settings' => [
					'size' => [64, 64],
					'resize' => 'EXACT',
					'accept' => '.jpg, .jpeg, .png, .ico',
				]
			],
			[
				'title' => Loc::getMessage("CITRUS_AREALTY_SETTINGS_WIDGET_SITE_NAME"),
				'section' => 'header',
				'type' => static::FIELD_TYPE_TEXT,
				'code' => 'SITE_NAME',
				'value' => $this->getCurrent("SITE_NAME"),
			],
			[
				'title' => Loc::getMessage('CITRUS_AREALTY_SETTINGS_WIDGET_SHOW_LOGO_TITLE'),
				'placeholder' => '',
				'section' => 'header',
				'type' => static::FIELD_TYPE_CHECKBOX,
				'code' => 'LOGO_SHOW_TEXT',
				'checked' => $this->getCurrent("LOGO_SHOW_TEXT") == "Y",
			],
			[
				'title' => Loc::getMessage('CITRUS_AREALTY_SETTINGS_WIDGET_CURRENCY'),
				'placeholder' => '',
				'section' => 'currency',
				'type' => static::FIELD_TYPE_SELECT,
				'code' => 'CURRENCY',
				'value' => $this->getCurrent("CURRENCY"),
				'values' => $currencies,
			],
			[
				'title' => Loc::getMessage('CITRUS_AREALTY_SETTINGS_WIDGET_PRICE_VIEW'),
				'placeholder' => '',
				'section' => 'currency',
				'type' => static::FIELD_TYPE_SELECT,
				'code' => 'CURRENCY_FACTOR',
				'value' => $this->getCurrent('CURRENCY_FACTOR'),
				'values' => $priceViews,
			],
			[
				'title' => '',
				'placeholder' => '',
				'section' => 'mp_blocks',
				'type' => static::FIELD_TYPE_BLOCKS,
				'code' => 'BLOCKS',
				'values' => [
					[
						'label' => Loc::getMessage('CITRUS_AREALTY_SETTINGS_WIDGET_BLOCK_SLIDER'),
						'value' => 'slider',
						'image' => $blockImagePath . 'slider.jpg',
						'checked' => $this->getCurrent("BLOCKS")["slider"],
					],
					[
						'label' => Loc::getMessage('CITRUS_AREALTY_SETTINGS_WIDGET_BLOCK_FILTER'),
						'value' => 'filter',
						'image' => $blockImagePath . 'filter.jpg',
						'checked' => $this->getCurrent("BLOCKS")["filter"],
					],
					[
						'label' => Loc::getMessage('CITRUS_AREALTY_SETTINGS_WIDGET_BLOCK_SALES'),
						'value' => 'quick-sale',
						'image' => $blockImagePath . 'quick-sale.jpg',
						'checked' => $this->getCurrent("BLOCKS")["quick-sale"],
					],
					[
						'label' => Loc::getMessage('CITRUS_AREALTY_SETTINGS_WIDGET_BLOCK_OFFERS'),
						'value' => 'services',
						'image' => $blockImagePath . 'services.jpg',
						'checked' => $this->getCurrent("BLOCKS")["services"],
					],
					[
						'label' => Loc::getMessage('CITRUS_AREALTY_SETTINGS_WIDGET_BLOCK_HELP'),
						'value' => 'help-block',
						'image' => $blockImagePath . 'help-block.jpg',
						'checked' => $this->getCurrent("BLOCKS")["help-block"],
					],
					[
						'label' => Loc::getMessage('CITRUS_AREALTY_SETTINGS_WIDGET_BLOCK_SEO'),
						'value' => 'seo-block',
						'image' => $blockImagePath . 'seo-block.jpg',
						'checked' => $this->getCurrent("BLOCKS")["seo-block"],
					],
					[
						'label' => Loc::getMessage('CITRUS_AREALTY_SETTINGS_WIDGET_BLOCK_ABOUT'),
						'value' => 'who-we',
						'image' => $blockImagePath . 'who-we.jpg',
						'checked' => $this->getCurrent("BLOCKS")["who-we"],
					],
					[
						'label' => Loc::getMessage('CITRUS_AREALTY_SETTINGS_WIDGET_BLOCK_NEW'),
						'value' => 'new-offers',
						'image' => $blockImagePath . 'new-offers.jpg',
						'checked' => $this->getCurrent("BLOCKS")["new-offers"],
					],
					[
						'label' => Loc::getMessage('CITRUS_AREALTY_SETTINGS_WIDGET_BLOCK_MAP'),
						'value' => 'map',
						'image' => $blockImagePath . 'map.jpg',
						'checked' => $this->getCurrent("BLOCKS")["map"],
					],
				],
			],
			[
				'title' => Loc::getMessage('CITRUS_AREALTY_SETTINGS_WIDGET_PHONE'),
				'section' => 'contacts',
				'type' => static::FIELD_TYPE_TEXT,
				'code' => 'PHONE',
				'value' => $this->getCurrent("PHONE"),
			],
			[
				'title' => Loc::getMessage('CITRUS_AREALTY_SETTINGS_WIDGET_EMAIL'),
				'section' => 'contacts',
				'type' => static::FIELD_TYPE_TEXT,
				'code' => 'EMAIL',
				'value' => $this->getCurrent("EMAIL"),
			],
			[
				'title' => Loc::getMessage('CITRUS_AREALTY_SETTINGS_WIDGET_SENDER_EMAIL'),
				'section' => 'contacts',
				'type' => static::FIELD_TYPE_TEXT,
				'code' => 'SENDER_EMAIL',
				'value' => $this->getCurrent('SENDER_EMAIL'),
			],
			[
				'title' => Loc::getMessage('CITRUS_AREALTY_SETTINGS_WIDGET_ADDRESS'),
				'section' => 'contacts',
				'type' => static::FIELD_TYPE_TEXT,
				'code' => 'ADDRESS',
				'value' => $this->getCurrent("ADDRESS"),
			],
		];

		if (!\CModule::IncludeModule("b24connector")
			|| !\CModule::IncludeModule("socialservices"))
		{
			$result[] = [
				'title' => Loc::getMessage("CITRUS_AREALTY_SETTINGS_ENTITY_BITRIX24_TITLE"),
				'section' => 'contacts',
				'type' => 'html',
				'code' => 'BITRIX24_LINKMODULE',
				'value' => Loc::getMessage('CITRUS_AREALTY_SETTINGS_ENTITY_BITRIX24_NOTINSTALLED', [
					"#URL#" => "/bitrix/admin/module_admin.php"
				]),
			];
		}
		else if (!Connection::isExist())
		{
			$result[] = [
				'title' => Loc::getMessage("CITRUS_AREALTY_SETTINGS_ENTITY_BITRIX24_TITLE"),
				'section' => 'contacts',
				'type' => 'html',
				'code' => 'BITRIX24_BTNCONNECT',
				'value' => Connection::getOptionButtonHtml(Loc::getMessage(
					'CITRUS_AREALTY_SETTINGS_ENTITY_BITRIX24_CONNECT'
				)),
			];
		}
		else
		{
			$result[] = [
				'title' => Loc::getMessage('CITRUS_AREALTY_SETTINGS_WIDGET_BITRIX24'),
				'section' => 'contacts',
				'type' => 'checkbox',
				'code' => 'BITRIX24_LEAD',
				'checked' => $this->getCurrent("BITRIX24_LEAD") == "Y",
			];
		}

		$event = new Event('citrus.arealty', static::EVENT_DEFINE_WIDGET_FIELDS, ['fields' => &$result]);
		$event->send($this);

		return $event->getParameter('fields');
	}

	protected function checkResult(Result $result)
	{
		if (!$result->isSuccess())
		{
			throw new \RuntimeException(implode(', ', $result->getErrorMessages()));
		}

		return $result;
	}

	protected function saveImage($field, $data)
	{
		$update = [$field => false];

		if ($existingFile = SettingsTable::getValue($field, SITE_ID, false))
		{
			\CFile::Delete($existingFile);
		}

		if (!empty($data))
		{
				// картинка приходит в виде url'а data:// его можно декодировать через file_get_contents()
				// http://php.net/manual/en/wrappers.data.php
				//$imageData = file_get_contents($data);
				// NOTE это не работает на некоторых хостингах (некоторые фильтры могут быть не установлены)
				$p = strpos($data, ';base64,');
				if ($p === false)
				{
					throw new \RuntimeException(sprintf('Invalid image data format %s', $field));
				}
				$data = substr($data, $p + 8);
				$imageData = base64_decode($data);

				$tempFilename = \CTempFile::GetFileName(strtolower($field) . '.png');
				CheckDirPath($tempFilename);
				file_put_contents($tempFilename, $imageData);

				$arFile = \CFile::MakeFileArray($tempFilename);
				$update[$field] = \CFile::SaveFile($arFile, "citrus.arealty");
		}

		return $update;
	}

	/**
	 * @param HttpRequest $request
	 * @param array $serializedParams
	 * @return array
	 * @throws \Exception
	 */
	public function saveAction(HttpRequest $request, array $serializedParams)
	{
		global $APPLICATION;

		if ($APPLICATION->GetUserRight('citrus.arealty') < 'W' && !$this->arParams['DEMO_MODE'])
		{
			throw new \RuntimeException(Loc::getMessage('ACCESS_DENIED'));
		}

		$fields = $this->getWidgetFields();
		$fieldsByCode = array_reduce($fields, function ($carry, $item) {
			$carry[$item['code']] = $item;

			return $carry;
		}, []);

		$changedData = (array)$request->get('fields');

		$changed = array_intersect_key($changedData, $fieldsByCode);
		$settingsUpdate = [];
		foreach ($changed as $fieldCode => $value)
		{
			$field = $fieldsByCode[$fieldCode];
			$fieldType = $field['type'];

			switch ($fieldType)
			{
				case static::FIELD_TYPE_TEXT:
					$settingsUpdate[$fieldCode] = $value;
					break;

				case static::FIELD_TYPE_CHECKBOX:
					$settingsUpdate[$fieldCode] = $value === 'Y' ? 'Y' : 'N';
					break;

				case static::FIELD_TYPE_SELECT:
					$allowedValues = array_column($field['values'], 'label', 'value');
					if (isset($allowedValues[$value]))
					{
						$settingsUpdate[$fieldCode] = $value;
					}
					else
					{
						throw new \RuntimeException(sprintf('Incorrect value for %s field: %s', $fieldCode, var_export($value, 1)));
					}
					break;

				case static::FIELD_TYPE_COLOR:
					$theme = new Theme(SITE_ID, $value);

					if ($this->arParams['DEMO_MODE'])
					{
						$themeDir = \CTempFile::GetDirectoryName(static::TEMP_THEME_HOUSES_TO_KEEP, 'citrus_arealty/theme-' . $theme->getColor() . '/');
						(new Directory($themeDir))->create();

						$theme->setPath(str_replace(Application::getDocumentRoot(), '', $themeDir));
					}

					$builder = new ThemeBuilder($theme, $theme->getPath());
					$builder->build();

					$settingsUpdate['THEME'] = $this->arParams['DEMO_MODE'] ? $theme->getPath() : $theme->getId();
					break;

				case static::FIELD_TYPE_IMAGE:
					if ($this->arParams['DEMO_MODE'])
					{
						$settingsUpdate[$fieldCode] = $value;
					}
					else
					{
						$settingsUpdate = array_merge($settingsUpdate, $this->saveImage($fieldCode, $value));
					}
					break;

				case static::FIELD_TYPE_BLOCKS:
					if (!is_array($value))
					{
						throw new \RuntimeException(sprintf('Value for %s field must be an array', $fieldCode));
					}

					$settingsUpdate[$fieldCode] = array_map(function ($v) { return $v === 'true'; }, $value);
					break;

				default:
					throw new NotImplementedException('Unsupported field type: ' . $fieldType);
			}
		}

		if (count($settingsUpdate))
		{
			if ($this->arParams['DEMO_MODE'])
			{
				$_SESSION['CITRUS_SETTINGS_WIDGET'][SITE_ID] = array_merge($_SESSION['CITRUS_SETTINGS_WIDGET'][SITE_ID] ?: [], $settingsUpdate);
			}
			else
			{
				if (array_key_exists('SITE_NAME', $settingsUpdate))
				{
					$obSite = new \CSite();
					if (!$obSite->Update(SITE_ID, [
						'NAME' => $settingsUpdate['SITE_NAME'],
						'SITE_NAME' => $settingsUpdate['SITE_NAME'],
					]))
					{
						throw new \RuntimeException($obSite->LAST_ERROR);
					}

					unset($settingsUpdate['SITE_NAME']);
				}

				if (count($settingsUpdate))
				{
					$this->checkResult(SettingsTable::setForSite($settingsUpdate, SITE_ID));
				}

				SettingsTable::clearCache();
			}
		}

		return [
			'result' => 'success',
		];
	}

	public function colorAction(HttpRequest $request, array $serializedParams)
	{
		$theme = new Theme(SITE_ID, $request->get('color'));

		$tmpPath = \CTempFile::GetDirectoryName(static::TEMP_THEME_HOUSES_TO_KEEP);
		$relativeTmpPath = Path::normalize(str_replace(Application::getDocumentRoot(), '/', $tmpPath));

		$builder = new ThemeBuilder($theme, $relativeTmpPath);

		return $builder->build();
	}

	public function execute()
	{
		if (!$this->arParams['DEMO_MODE'] && isset($_SESSION['CITRUS_SETTINGS_WIDGET'][SITE_ID]))
		{
			unset($_SESSION['CITRUS_SETTINGS_WIDGET'][SITE_ID]);
		}

		$this->arResult['fieldSettings']['SCHEME']['FILES'] = (new Theme(SITE_ID))->getFiles();

		parent::execute();

		$this->includeComponentTemplate();
	}
}
