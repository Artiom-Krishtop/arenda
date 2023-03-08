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
$this->setFrameMode(true);

?>

<div class="about">
    <div class="p__swiper history-results-nav-sllider">
        <div class="swiper-container">
            <div class="swiper-wrapper">
				<? foreach ($arResult["ITEMS"] as $key => $item): ?>
                    <a class="swiper-slide about-item <? if (!$key): ?>is-active<? endif; ?>"
                       href="javascript: void(0);"
                       rel="nofollow">
                        <span class="about-item-circle"></span>
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
    </div><!-- .history-results-nav-sllider -->
    <div class="history-results-content">
        <div class="swiper-container swiper-container-horizontal">
            <div class="swiper-wrapper">
				<? foreach ($arResult["ITEMS"] as $key => $item): ?>
                    <div class="swiper-slide">
                        <div class="about-item-body">
                            <div class="about-item-date">
								<? $dateTs = MakeTimeStamp($item["ACTIVE_FROM"]); ?>
                                <div class="year-number"><?=date('Y',
										$dateTs)?> <?=GetMessage("CITRUS_TIMELINE_YEAR")?></div>
                                <time datetime="<?=date('Y-m-d', $dateTs)?>"><?=FormatDate('f',
										$dateTs)?></time>
                            </div>
                            <div class="about-item-title"><?=$item["NAME"]?></div>
                            <div class="about-item-text">
								<?=$item["PREVIEW_TEXT"]?>
                            </div>
                        </div><!-- .about-item-body -->
                    </div><!-- .swiper-slide -->
				<? endforeach; ?>
            </div><!-- .swiper-wrapper -->
        </div><!-- .swiper-container -->
    </div>
</div><!-- .about -->
