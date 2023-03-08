<?

namespace Citrus\Arealty;

use Citrus\Arealty\Object\Address;
use Citrus\Arealty\Yamap,
	Bitrix\Main\Web\Json;

/** @var array $arParams ���������, ������, ���������. �� ����������� ����������� ���� ����������, �� ��������� ��� ������ ��  � ����� template.php. */
/** @var array $arResult ���������, ������/���������. ����������� ����������� ���� ������ ����������. */
/** @var \CBitrixComponentTemplate $this ������� ������ (������, ����������� ������) */

$obEnum = new \CUserFieldEnum();
if ($arResult["UF_TYPE"] && ($enum = $obEnum->GetList(array(), array("ID" => $arResult["UF_TYPE"]))->Fetch()))
	$arResult["UF_TYPE_XML_ID"] = $enum["XML_ID"];
else
	$arResult["UF_TYPE_XML_ID"] = false;

// ������� ������� ��� �����������
$displayProperties = new DisplayProperties($arResult['IBLOCK_ID']);
$displayPropertiesByXmlId = array();
foreach ($arResult["ITEMS"] as &$item)
{
	$isDefault = false;
	if (!empty($arParams['IS_JK']) && $arParams['IS_JK'] == 'Y')
	{
		$item['DISPLAY_COLUMNS'] = $displayProperties->getForElement($item['ID'], $isDefault);
	}
	else
	{
		$item['DISPLAY_COLUMNS'] = $displayProperties->getForElement($item['ID'], $item['DISPLAY_COLUMNS_DEFAULT']);
	}
	$displayPropertiesByXmlId[$item['XML_ID']] = array_merge(['IS_DEFAULT' => $isDefault], $item['DISPLAY_COLUMNS'] ?: []);
	$item['ADDRESS'] = Address::createFromFields($item);
	if (!empty($arParams['IS_JK']) && $arParams['IS_JK'] == 'Y')
	{
		$item['DISPLAY_COLUMNS_DEFAULT'] = false;
	}
}
if (isset($item))
{
	unset($item);
}

$arResult['DISPLAY_COLUMNS'] = $displayProperties->getForSection($arResult['ID'], $arResult['DISPLAY_COLUMNS_DEFAULT']);

$arResult["MAP_ID"] = uniqid('citrus-objects-map-');

if (!empty($arParams['IS_JK']) && $arParams['IS_JK'] == 'Y')
{
	$complexIds = array_unique(array_reduce($arResult['ITEMS'], function ($carry, $item) {
		$carry[] = $item['XML_ID'];
		return $carry;
	}, array()));
	try
	{
		$complexService = new ComplexService(Helper::getIblock("offers", SITE_ID));
		$complexService->setDefaultPropLinks($arParams['PROP_LINK']);
		$arResult['OFFERS_FIELDS'] = $complexService->getOfferFields($complexIds, array(), $displayPropertiesByXmlId);
	}
	catch (\Exception $e)
	{
		ShowError($e->getMessage());
		$arResult['OFFERS_FIELDS'] = null;
	}
}