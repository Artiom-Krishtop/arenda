<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

use Citrus\Arealty\Object\Address;
use Citrus\Arealty;
use Citrus\Arealty\Object\GeoProperty;
use function \Citrus\Core\array_get;

/** @var CBitrixComponentTemplate $this */
/** @var array $arParams */
/** @var array $arResult */

$this->__component->setResultCacheKeys(["DEAL_TYPE", "CONTACT", 'COMPLEX', 'OFFERS', 'OFFERS_FIELDS']);

$contactId = array_get($arResult, 'PROPERTIES.contact.VALUE');
$arResult["CONTACT"] = null;
if ($contactId)
{
	$contactDataset = CIBlockElement::GetList([], ['IBLOCK_ID' => \Citrus\Arealty\Helper::getIblock('staff'), '=ID' => $contactId])
		->GetNextElement(true, false);
	if ($arResult["CONTACT"] = $contactDataset->GetFields())
	{
		$arResult["CONTACT"]["PROPERTIES"] = $contactDataset->GetProperties();
	}
}

if ($dealType = array_get($arResult, 'PROPERTIES.deal_type.VALUE'))
{
	$arResult['DEAL_TYPE'] = is_array($dealType) ? reset($dealType) : $dealType;
}

$arResult['ADDRESS'] = $address = Address::createFromFields($arResult);

// ������ �������� ������� �� ������� � ������� (������� "�������� ���������")
$displayProperties = array_column(\CIBlockSectionPropertyLink::GetArray(
	$arResult['IBLOCK_ID'],
	$arResult['IBLOCK_SECTION_ID']
), null, 'PROPERTY_ID');
foreach ($arResult['DISPLAY_PROPERTIES'] as $k => $v)
{
	if (!isset($displayProperties[$v['ID']]))
	{
		unset($arResult['DISPLAY_PROPERTIES'][$k]);
	}
}

$arResult['NAME'] = trim(str_replace(GeoProperty::getSeoValue($address->getGeo()), '', $arResult['NAME']), ', ');

if (!empty($arParams['IS_JK']) && $arParams['IS_JK'] == 'Y')
{
	try
	{
		$complexService = new Arealty\ComplexService(Arealty\Helper::getIblock("offers", SITE_ID));
		$arResult['OFFERS_FIELDS'] = reset($complexService->getOfferFields(array($arResult['XML_ID'])));
	}
	catch (\Exception $e)
	{
		ShowError($e->getMessage());
		$arResult['OFFERS_FIELDS'] = null;
	}
}
else
{
	$arResult['COMPLEX'] = array_get($arResult, 'PROPERTIES.complex.VALUE');
}

if(!empty($arResult['PROPERTIES']['RENTAL_COMPANY']['VALUE'])){
	$dbRes = CIBlockElement::GetByID(intval($arResult['PROPERTIES']['RENTAL_COMPANY']['VALUE']));

	while ($company = $dbRes->fetch()) {
		$arResult['PROPERTIES']['RENTAL_COMPANY']['VALUE_TXT'] = $company["NAME"];
	}
}

$arResult['RENT_EMAIL'] = '';

if(!empty($arResult['CREATED_BY'])){
	$userDb = $USER->GetById(intval($arResult['CREATED_BY']));

	if($user = $userDb->fetch()){
		$arResult['RENT_EMAIL'] = $user['EMAIL'];
	}
}
