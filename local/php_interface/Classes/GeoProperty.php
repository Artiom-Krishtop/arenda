<?
namespace ITG\Custom;

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Text\Encoding;
use Bitrix\Main\Web\Json;
use Citrus\Arealty\Helper;
use Citrus\Arealty\Polyfill;
use Citrus\Yandex\Geo\Api as YandexApi;
use Citrus\Yandex\Geo\GeoObject;
use Bitrix\Main\Page\Asset;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Loader;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

Loc::loadMessages($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/citrus.arealty/lib/object/geoproperty.php');

/* 
Кастомный класс GeoProperty для отображения карты в форме редактирования объявления. Оригинал класса лежит здесь
/bitrix/modules/citrus.arealty/lib/object/geoproperty.php
*/
class GeoProperty extends Polyfill
{
	/** @var string ���������� ��� �������� � �� �������� �������� */
	const PROPERTY_NAME = 'geodata';

	protected static function getHtml($arProperty, $value, $strHTMLControlName)
	{
		$value = static::normalizeValue($value);
		return '<span class="js-citrus-map-select-object-title">'
			. static::formatAddress($value)
			. '</span>'
			. '<input type="hidden" name="' . $strHTMLControlName['VALUE']
			. '" value="' . base64_encode(serialize($value))
			. '" class="js-citrus-map-selected-object-input">';
	}

	/**
	 * @param mixed $value
	 * @return GeoObject|null
	 */
	protected static function normalizeValue($value)
	{
		$getRawValue = function () use ($value) {
			if (is_array($value))
			{
				if (isset($value['DEFAULT_VALUE']))
				{
					unset($value['DEFAULT_VALUE']);
				}
				if (array_keys($value) == array('VALUE') || array_keys($value) == array('VALUE', 'DESCRIPTION'))
				{
					return $value['VALUE'];
				}

				return $value;
			}

			return $value;
		};

		$rawValue = $getRawValue();

		if ($rawValue instanceof GeoObject)
		{
			$value = $rawValue;
		}
		elseif (static::valueIsJson($rawValue))
		{
			$value = static::valueFromJson($rawValue);
		}
		elseif ($value)
		{
			$value = static::unserialize($rawValue);
			if ($value === false)
			{ // check is address string
				if (trim($rawValue) != "")
				{
					$value = static::getByAddress($rawValue);
				}
			}
		}
		else
		{
			$value = null;
		}
		if ($value instanceof GeoObject)
		{
			$value = static::withUserAddress($value);
		}

		return $value;
	}

	private static function valueIsJson($value)
	{
		return !empty($value)
			&& is_string($value)
			&& substr($value, 0, 1) == "{";
	}

	/**
	 * @param mixed $value
	 * @return GeoObject
	 */
	private static function valueFromJson($value)
	{
		if (substr($value, 0, 7) == "{&quot;")
		{
			$value = htmlspecialcharsback($value);
		}

		return new GeoObject(
			Json::decode(
				Encoding::convertEncoding($value, SITE_CHARSET, 'utf-8')
			)
		);
	}

	/**
	 * @param string $value
	 * @return mixed
	 */
	protected static function unserialize($value)
	{
		return unserialize(base64_decode($value));
	}

	/**
	 * @deprecated geocoding performed on client browser
	 * @param string $address
	 * @return GeoObject|false
	 */
	protected static function getByAddress($address)
	{
		$yandex = new YandexApi();
		$yandex->setQuery($address)->load();
		$response = $yandex->getResponse();
		$result = reset($response->getList());
		if ($result instanceof GeoObject)
		{
			return $result;
		}

		return false;
	}

	private static function withUserAddress(GeoObject $geoObj)
	{
		// fix address with address input values
		if (!empty($_POST["js_citrus_map_fix_address"]) && $geoObj instanceof GeoObject)
		{
			$tmp = $_POST["js_citrus_map_fix_address"];
			$tmp["Address"] = ""; // for user address
			$geoObj->setData($tmp);
		}

		return $geoObj;
	}

	/**
	 * ����������� ����������� �������� � ������ ��� ����������� ������������
	 *
	 * @param string $location
	 * @return string
	 */
	public static function formatAddress($location, $placeholder = true)
	{
		if (!$location instanceof GeoObject)
		{
			return $placeholder ?
				Loc::getMessage('CITRUS_AREALTY_IBLOCK_TYPE_GEODATA_EMPTY')
				: "";
		}

		return str_replace(Loc::getMessage("CITRUS_AREALTY_GEO_STRIP_PREFIX"), '', (string)$location);
	}

	public static function GetMapPropertyHtml($arProperty, $value, $strHTMLControlName)
	{
		global $APPLICATION;

		/**
		 * ����������� ����������� ���������� $APPLICATION->AddHeadScript()
		 * ���������� ����� Asset::addJs() �� �������� ������ ����������� � ������ AJAX
		 *
		 * #47389
		 */
		$APPLICATION->AddHeadScript("https://api-maps.yandex.ru/2.1/?lang=ru_RU&apikey=" . YANDEX_MAP_API_KEY);
		$APPLICATION->AddHeadScript("/bitrix/templates/citrus_arealty3/js/geodata.js");
		$value = static::normalizeValue($value);
		$data = array();
		if ($value && $value instanceof GeoObject) {
			$data = $value->getData();
			$data['Latitude'] = $value->getLatitude();
			$data['Longitude'] = $value->getLongitude();
		}

		$jsonGeoData = Json::encode($data);
		$jsonSettings = Json::encode(static::prepareSettings($arProperty));

		$addressInputs = array();
		foreach (array(
			         'CountryName' => Loc::getMessage('CITRUS_AREALTY_ADDR_COUNTRYNAME'),
			         'AdministrativeAreaName' => Loc::getMessage('CITRUS_AREALTY_ADDR_ADMINISTRATIVEAREANAME'),
			         'SubAdministrativeAreaName' => Loc::getMessage('CITRUS_AREALTY_ADDR_SUBADMINISTRATIVEAREANAME'),
			         'LocalityName' => Loc::getMessage('CITRUS_AREALTY_ADDR_LOCALITYNAME'),
			         'DependentLocalityName' => Loc::getMessage('CITRUS_AREALTY_ADDR_DEPENDENTLOCALITYNAME'),
			         'ThoroughfareName' => Loc::getMessage('CITRUS_AREALTY_ADDR_THOROUGHFARENAME'),
			         'PremiseNumber' => Loc::getMessage('CITRUS_AREALTY_ADDR_PREMISENUMBER'),
		         ) as $input => $title)
		{
			$addressInputs[$input] = '<input name="js_citrus_map_fix_address['
				. $input . ']" value="'
				. (!empty($data[$input]) ?
					htmlspecialcharsex($data[$input]) : "") . '" '
				. ' type="hidden" placeholder="' . htmlspecialcharsex($title)
				. '" title="' . htmlspecialcharsex($title)
				. '" class="adm-input js-citrus-map-address-' . $input
				. '" style="width:45%;margin:4px 4px;">';
		}
		foreach (array(
			         "mapbounds0",
			         "mapbounds1",
			         "mapbounds2",
			         "mapbounds3",
			         "mapzoom",
					 "polygon",
		         ) as $input)
		{
			$addressInputs[$input] =
				'<input type="hidden" name="js_citrus_map_fix_address['
				. $input . ']" value="'
				. (!empty($data[$input]) ?
					htmlspecialcharsex($data[$input]) : "")
				. '" class="js-citrus-map-' . $input . '">';
		}
		ob_start();
		?>
		<div class="js-citrus-map-select-object" id="geodata-block-<?=$arProperty['ID']?>">
			<div class="js-citrus-map-address-fields" style="display:none;"><?=implode("\n", $addressInputs)?></div>
			<div class="citrus-map-address-title" style="margin:5px 5px;">
				<?=static::getHtml($arProperty, $value, $strHTMLControlName)?> &nbsp;
			</div>
			<div style="height: 400px;" class="js-citrus-map-select-object-map"></div>
		</div>

		<script>
			BX.message(
				<?=Json::encode(array(
					'GEODATA_BTN_TITLE' => Loc::getMessage("GEODATA_BTN_TITLE"),
					'GEODATA_BTN_CONTENT' => Loc::getMessage("GEODATA_BTN_CONTENT"),
					'GEODATA_BTN_CONTENT_END' => Loc::getMessage("GEODATA_BTN_CONTENT_END")
				))?>
			);
			new GeoProperty("geodata-block-<?=$arProperty['ID']?>", <?=$jsonGeoData?>, <?=$jsonSettings?>)
		</script>
		<?
		return ob_get_clean();
	}

	public static function prepareSettings($property)
	{
		$result = array(
			'allow_polygon' => false
		);
		if (is_array($property["USER_TYPE_SETTINGS"]) && isset($property["USER_TYPE_SETTINGS"]['allow_polygon']))
		{
			$result['allow_polygon'] = in_array($property["USER_TYPE_SETTINGS"]['allow_polygon'], array('Y', true), true);
		}

		return $result;
	}
}
