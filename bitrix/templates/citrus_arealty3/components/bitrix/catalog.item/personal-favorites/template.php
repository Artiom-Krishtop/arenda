<?php

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$this->setFrameMode(true);

use Bitrix\Main\Localization\Loc,
	Bitrix\Main\Web\Json;

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
/** @var CBitrixComponent $component */
$this->setFrameMode(true);

if (isset($arResult['ITEM']))
{
	$item = $arResult['ITEM'];
	$areaId = $arResult['AREA_ID'];

	$productTitle = $item['NAME'];

	$imgTitle = isset($item['IPROPERTY_VALUES']['ELEMENT_PREVIEW_PICTURE_FILE_TITLE']) && $item['IPROPERTY_VALUES']['ELEMENT_PREVIEW_PICTURE_FILE_TITLE'] != ''
		? $item['IPROPERTY_VALUES']['ELEMENT_PREVIEW_PICTURE_FILE_TITLE']
		: $item['NAME'];

	$skuProps = array();

	$haveOffers = !empty($item['OFFERS']);
	if ($haveOffers)
	{
		$actualItem = isset($item['OFFERS'][$item['OFFERS_SELECTED']])
			? $item['OFFERS'][$item['OFFERS_SELECTED']]
			: reset($item['OFFERS']);
	}
	else
	{
		$actualItem = $item;
	}

	$showSlider = is_array($morePhoto) && count($morePhoto) > 1;
	$itemHasDetailUrl = isset($item['DETAIL_PAGE_URL']) && $item['DETAIL_PAGE_URL'] != '';

    $previewPhoto = '';

    if(!empty($actualItem['PREVIEW_PICTURE'])){
        $previewPhoto = $actualItem['PREVIEW_PICTURE'];
    }else {
        $previewPhoto = $actualItem['DETAIL_PICTURE'];
    }
	?>

    <div class="slider__item" id="<?= $areaId?>">
        <div class="slider__views">
            <img class="slider__views-icon" src="<?= $templateFolder ?>/images/views.svg" alt="Icon">
            <? if (!empty($actualItem['SHOW_COUNTER'])):?>
                <span class="slider__views-count"><?= (int)$actualItem['SHOW_COUNTER']?></span>
            <? endif;?>
        </div>
        <? if (!empty($previewPhoto)):?>
            <img class="slider__img" src="<?= $previewPhoto['SRC']?>" alt="<?= $imgTitle?>">
        <? else: ?>
            <img class="slider__img" src="<?= SITE_TEMPLATE_PATH?>/images/no-foto.jpg" alt="<?= $imgTitle?>">
        <? endif; ?>
        <div class="slider__info">
            <h2 class="slider__title">
                <a href="<?= !empty($actualItem['DETAIL_PAGE_URL']) ? $actualItem['DETAIL_PAGE_URL'] : 'javascript:void(0)'?>"><?= $productTitle?></a>
            </h2>
            <? if(!empty($actualItem['PROPERTIES']['geodata']['VALUE']) && $actualItem['PROPERTIES']['geodata']['VALUE'] instanceof Citrus\Yandex\Geo\GeoObject):?>
                <p class="slider__location">
                    <?= (string)$actualItem['PROPERTIES']['geodata']['VALUE'] ?>
                </p>
            <? endif;?>
            <div class="slider__descr">
                <? if(!empty($actualItem['PROPERTIES']['rooms']['VALUE'])):?>
                    <span class="slider__descr-text"><?= $actualItem['PROPERTIES']['rooms']['VALUE'] ?><?= GetMessage('PA_ROOM_TXT')?></span>
                <? endif; ?>
                <? if(!empty($actualItem['PROPERTIES']['common_area']['VALUE'])):?>
                    <span class="slider__descr-text"><?= $actualItem['PROPERTIES']['common_area']['VALUE'] ?> <?= GetMessage('PA_AREA_TXT')?></span>
                <? endif; ?>
                <? if(!empty($actualItem['PROPERTIES']['NEW_FLOOR']['VALUE'])):?>
                    <span class="slider__descr-text"><?= $actualItem['PROPERTIES']['NEW_FLOOR']['VALUE'] ?> <?= GetMessage('PA_FLOOR_TXT')?></span>
                <? endif; ?>
            </div>
            <div class="slider__block">
                <div class="slider__column">
                    <? if(!empty($actualItem['PROPERTIES']['cost']['VALUE'])):?>
                        <span class="slider__price">
                            <span><?= round($actualItem['PROPERTIES']['cost']['VALUE']) ?></span> <?= GetMessage('PA_BYN_TXT')?>/<?= GetMessage('PA_MONTH_TXT')?>
                        </span>
                    <? endif; ?>
                    <? if(!empty($actualItem['PROPERTIES']['price_for_meter']['VALUE'])):?>
                        <span class="slider__for_area">
                            <span><?= round($actualItem['PROPERTIES']['price_for_meter']['VALUE']) ?></span> <?= GetMessage('PA_BYN_TXT')?>/<?= GetMessage('PA_AREA_TXT')?>
                        </span>
                    <? endif; ?>
                </div>
                <div class="slider__column">
                    <span class="slider__id">
                        ID <?= $actualItem['ID']?>
                    </span>
                    <? if (!empty($actualItem['DATE_ACTIVE_FROM'])):?>
                        <span class="slider__date">
                            <?= $actualItem['DATE_ACTIVE_FROM'] ?>
                        </span>
                    <? endif; ?>
                </div>
            </div>
            <div class="slider__btns">
                <button class="slider__btn add2favourites added" data-id="<?= $actualItem['ID']?>"><?= GetMessage('PA_REMOVE_BTN')?></button>
            </div>
        </div>
    </div>  
<?}?>