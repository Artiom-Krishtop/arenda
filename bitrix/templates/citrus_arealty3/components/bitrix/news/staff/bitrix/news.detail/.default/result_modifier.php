<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

/** @var array $arParams ���������, ������, ���������. �� ����������� ����������� ���� ����������, �� ��������� ��� ������ �� $arParams � ����� template.php. */
/** @var array $arResult ���������, ������/���������. ����������� ����������� ���� ������ ����������. */
/** @var CBitrixComponentTemplate $this ������� ������ (������, ����������� ������) */

if ($arParams['DISPLAY_PICTURE'] !== 'N' && ($pictureId = \Citrus\Core\array_get($arResult, "PREVIEW_PICTURE.ID")))
{
	$arResult['SMALL_PICTURE'] = CFile::ResizeImageGet(
		$pictureId,
		array(
			'width' => 367,
			'height' => 351,
		),
		BX_RESIZE_IMAGE_EXACT,
		$bInitSizes = true
	);
}
else
{
	$gender = \Citrus\Core\array_get($arResult, 'PROPERTIES.gender.VALUE_XML_ID');
	$gender = in_array($gender, ['male', 'female']) ? $gender : 'male';

	$arResult['SMALL_PICTURE'] = [
		'src' => getLocalPath('components/citrus/template/templates/staff-item/img/' . $gender . '.jpg'),
		'width' => 367,
		'height' => 351,
	];
}

$this->__component->setResultCacheKeys(['SMALL_PICTURE']);
