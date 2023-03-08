<?php

namespace Citrus\Arealty;

use Citrus\Arealty\Object\Address;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

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
	$item['DISPLAY_COLUMNS'] = $displayProperties->getForElement($item['ID'], $item['DISPLAY_COLUMNS_DEFAULT']);
	$displayPropertiesByXmlId[$item['XML_ID']] = $item['DISPLAY_COLUMNS'];
	$item['ADDRESS'] = Address::createFromFields($item);
}
if (isset($item))
{
	unset($item);
}

$arResult['DISPLAY_COLUMNS'] = $displayProperties->getForSection($arResult['ID'], $arResult['DISPLAY_COLUMNS_DEFAULT']);

$arResult["MAP_ID"] = uniqid('citrus-objects-map-');

$complexIds = array_unique(array_reduce($arResult['ITEMS'], function ($carry, $item) {
	$carry[] = $item['XML_ID'];
	return $carry;
}, array()));

try
{
	$complexService = new ComplexService(Helper::getIblock("offers", SITE_ID));
	$arResult['OFFERS_FIELDS'] = $complexService->getOfferFields($complexIds, array(), $displayPropertiesByXmlId);
}
catch (\Exception $e)
{
	ShowError($e->getMessage());
	$arResult['OFFERS_FIELDS'] = null;
}
