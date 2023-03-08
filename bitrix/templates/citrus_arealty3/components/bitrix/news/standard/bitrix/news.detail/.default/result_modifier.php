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
		[
			'IBLOCK_ID' => \Citrus\Arealty\Helper::getIblock('staff'),
			[
				'LOGIC' => 'OR',
				'=XML_ID' => $contactId,
				'=ID' => $contactId,
			]
		]
	)->GetNextElement(true, false);

	if ($contactDataset && ($arResult["CONTACT"] = $contactDataset->GetFields()))
	{
		$arResult["CONTACT"]["PROPERTIES"] = $contactDataset->GetProperties();
	}
}

$arResult['PAGE_SUBHEADER'] = $arResult["DISPLAY_ACTIVE_FROM"];
$this->getComponent()->setResultCacheKeys(['CONTACT', 'PAGE_SUBHEADER']);

if ($arResult['DISPLAY_PROPERTIES']['GALLERY']['FILE_VALUE'] && !$arResult['DISPLAY_PROPERTIES']['GALLERY']['FILE_VALUE'][0])
{
	$arResult['DISPLAY_PROPERTIES']['GALLERY']['FILE_VALUE'] = [$arResult['DISPLAY_PROPERTIES']['GALLERY']['FILE_VALUE']];
}

if ($arResult['DETAIL_PICTURE']['SRC'])
{
	$arResult['DETAIL_PICTURE']['MIN'] = CFile::ResizeImageGet(
		$arResult['DETAIL_PICTURE']["ID"],
		['width' => 1250, 'height' => 600],
		BX_RESIZE_IMAGE_PROPORTIONAL,
		true
	);
}

if (is_array($arResult['DISPLAY_PROPERTIES']['GALLERY']['FILE_VALUE']))
{
	foreach ($arResult['DISPLAY_PROPERTIES']['GALLERY']['FILE_VALUE'] as &$galleryItem)
	{
		$galleryItem['MIN'] = CFile::ResizeImageGet(
			$galleryItem["ID"],
			['width' => 500, 'height' => 500],
			BX_RESIZE_IMAGE_PROPORTIONAL,
			true
		);
	}
}
