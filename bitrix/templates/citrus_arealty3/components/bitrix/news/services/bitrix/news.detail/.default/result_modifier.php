<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

use function \Citrus\Core\array_get;

/** @var CBitrixComponentTemplate $this */
/** @var array $arParams */
/** @var array $arResult */

if (!\Bitrix\Main\Loader::includeModule('citrus.arealty'))
{
	return;
}

$contactId = array_get($arResult, 'PROPERTIES.contact.VALUE');
$arResult["CONTACT"] = null;
if ($contactId)
{
	$contactDataset = CIBlockElement::GetList(
		[],
		['IBLOCK_ID' => \Citrus\Arealty\Helper::getIblock('staff'), '=ID' => $contactId]
	)->GetNextElement(true, false);

	if ($contactDataset && ($arResult["CONTACT"] = $contactDataset->GetFields()))
	{
		$arResult["CONTACT"]["PROPERTIES"] = $contactDataset->GetProperties();
	}
}

if (strlen($arResult["DETAIL_TEXT"]))
{
	$arResult["DETAIL_TEXT"] = preg_replace('#(<a[^>]*?href=")/#i', '$1' . SITE_DIR, $arResult["DETAIL_TEXT"]);
}

if (strlen($arResult["PREVIEW_TEXT"]))
{
	$arResult["PREVIEW_TEXT"] = preg_replace('#(<a[^>]*?href=")/#i', '$1' . SITE_DIR, $arResult["DETAIL_TEXT"]);
}

$arResult['OFFERS'] = $arResult["PROPERTIES"]["offers"]["VALUE"];

$this->__component->setResultCacheKeys(array(
	"OFFERS",
	"CONTACT",
));
