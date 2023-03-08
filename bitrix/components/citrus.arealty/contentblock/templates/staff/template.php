<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$this->setFrameMode(true);

$this->AddEditAction($arResult['ID'], $arResult['EDIT_LINK'], \CIBlock::GetArrayByID($arResult['IBLOCK_ID'], 'ELEMENT_EDIT'));

if (!\Bitrix\Main\Loader::includeModule('citrus.arealty'))
{
	return;
}

?>

<div id="<?=$this->GetEditAreaId($arResult['ID'])?>">
	<? $APPLICATION->IncludeComponent(
		"citrus:template",
		"staff-item",
		array(
			'ITEM' => $arResult,
		),
		$component,
		array("HIDE_ICONS" => "Y")
	); ?>
</div>
