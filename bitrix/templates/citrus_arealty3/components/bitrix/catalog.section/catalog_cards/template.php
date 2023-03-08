<?php

use Bitrix\Main\Localization\Loc,
	Citrus\Arealty\Helper,
    Citrus\Arealty;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$this->setFrameMode(true);

/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */ ?>

<?
if ($arResult["DESCRIPTION"])
{
	?>
	<div class="catalog-section-description"><?=$arResult["DESCRIPTION"]?></div>
	<p class="indent"></p>
	<?
}

if (empty($arResult['ITEMS']))
{
	ShowNote($arParams['EMPTY_LIST_MESSAGE'] ? $arParams['EMPTY_LIST_MESSAGE'] : GetMessage("CITRUS_REALTY_NO_OFFERS"));
	return;
}
?>

<?
if ($arParams["DISPLAY_TOP_PAGER"])
{
	?><?=$arResult["NAV_STRING"]?><?
}

$strElementEdit = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_EDIT");
$strElementDelete = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_DELETE");
$arElementDeleteParams = array("CONFIRM" => GetMessage("CITRUS_REALTY_DELETE_CONFIRM"));

$containerClasses = [
    'catalog-cards',
    'row',
    'row-grid',
	/* $arParams['HEIGHT_OVERFLOW'] === 'Y' ? '_hide-overflow' : '' */
];


?>
<div class="<?=implode(' ', array_filter($containerClasses))?>">
	<?foreach ($arResult['ITEMS'] as $key => $arItem):
	$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], $strElementEdit);
	$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], $strElementDelete, $arElementDeleteParams);

	if(!empty($arItem['PROPERTIES']['photo']['VALUE'])){
		foreach ($arItem['PROPERTIES']['photo']['VALUE'] as $key => $photoID) {
			$photoData = CFile::GetFileArray($photoID);

			if(stripos($photoData['CONTENT_TYPE'], 'video') !== false) {
				unset($arItem['PROPERTIES']['photo']['VALUE'][$key]);
			}
		}
	}

	if(empty($arItem['PREVIEW_PICTURE'] && empty($arItem['DETAIL_PICTURE']) && !empty($arItem['PROPERTIES']['photo']['VALUE']))){
		$photoID = array_shift($arItem['PROPERTIES']['photo']['VALUE']);
		$photoData = CFile::GetFileArray($photoID);

		$arItem['PREVIEW_PICTURE'] = $photoData;
		$arItem['DETAIL_PICTURE'] = $photoData;
	}
	
	$arItem['OFFERS_FIELDS'] = isset($arResult['OFFERS_FIELDS'][$arItem['XML_ID']]) ? $arResult['OFFERS_FIELDS'][$arItem['XML_ID']] : null;
	if (!empty($arParams['IS_JK']) && $arParams['IS_JK'] == 'Y') {
		$arItem['IS_JK'] = 'Y';
	}
	?>

		<div class="catalog-cards__item col-xs-12 col-sm-6 col-md-4 col-lg-3" id="<?=$this->GetEditAreaId($arItem["ID"])?>">
			<?$APPLICATION->IncludeComponent(
				"citrus:template",
				"catalog-card",
				array(
					'DATA' => $arItem,
				),
				$component,
				array("HIDE_ICONS" => "Y")
			);?>
		</div>

	<?endforeach;?>
</div>

<script>
	currency.updateHtml($('.catalog-cards__item .catalog-card__price'));

	// equal height address
	equalHeightBot($('.catalog-cards__item .catalog-card__address'));

	<?if( Arealty\Helper::getModuleOption("lazyload") == "Y"):?>
		//lazyload
		if (typeof $.fn.lazyLoadInView !== 'undefined') {
			$('[data-lazyload]').lazyLoadInView();
		}
	<?endif;?>
</script>


<?if ($arParams["DISPLAY_BOTTOM_PAGER"])
{
	echo $arResult["NAV_STRING"];
}?>
