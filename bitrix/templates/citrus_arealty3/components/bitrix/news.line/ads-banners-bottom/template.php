<?php

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

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
$this->setFrameMode(true);?>

<div class="ads">
    <div class="ads-content">
        <div class="swiper-container swiper-container-horizontal">
            <div class="swiper-wrapper">
				<? foreach ($arResult["ITEMS"] as $key => $item): ?>
                    <? if (empty($item['PREVIEW_PICTURE'])) continue; ?>
                    <div class="swiper-slide">
                        <? if (!empty($item['PROPERTIES']['LINK_TO_ADS_OFFER']['VALUE'])):?>
                            <a class="swiper-slide__ads-link" href="<?= $item['PROPERTIES']['LINK_TO_ADS_OFFER']['VALUE']?>"></a>
                        <? endif; ?>
                        <img class="swiper-slide__ads-banner" src="<?= $item['PREVIEW_PICTURE']['SRC']?>" alt="<? $item['PREVIEW_PICTURE']['ALT'] ?>">
                    </div><!-- .swiper-slide -->
				<? endforeach; ?>
            </div><!-- .swiper-wrapper -->
        </div><!-- .swiper-container -->
    </div>

    <? if ($arParams['SHOW_SLIDER_NAV'] == 'Y'):?>
        <div class="p__swiper ads-nav-sllider">
            <div class="swiper-container">
                <div class="swiper-wrapper">
                    <? foreach ($arResult["ITEMS"] as $key => $item): ?>
                        <? if (empty($item['PREVIEW_PICTURE'])) continue; ?>
                        <a class="swiper-slide ads-item <? if (!$key): ?>is-active<? endif; ?>"
                            href="javascript: void(0);"
                            rel="nofollow">
                            <span class="ads-item-circle"></span>
                        </a>
                    <? endforeach; ?>
                </div>
            </div><!-- .swiper-container -->
            <div class="swiper-button-next swiper-button--template1">
                <span class="fa fa-angle-right"></span>
            </div>
            <div class="swiper-button-prev swiper-button--template1 swiper-button-disabled">
                <span class="fa fa-angle-left"></span>
            </div>
        </div><!-- .ads-nav-sllider -->
    <? endif; ?>
</div><!-- .about -->
