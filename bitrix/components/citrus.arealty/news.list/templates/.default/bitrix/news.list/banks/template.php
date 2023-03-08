<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
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

use Bitrix\Main\Localization\Loc;
?>

<?if(!empty($arResult["ITEMS"])):?>

<?php

$mainButtons = CIBlock::GetPanelButtons(
	$arResult["ITEMS"][0]["IBLOCK_ID"],
	0,
	$arResult["ITEMS"][0]["IBLOCK_SECTION_ID"],
	array("SECTION_BUTTONS" => false, "SESSID" => false)
);
$mainButtonsAreaId = "banks_slider_" . $arResult["ITEMS"][0]["IBLOCK_SECTION_ID"];

?>

<div class="p__swiper banks-slider _nav-offset _pagination-hide-nav"
		id="<?= $this->GetEditAreaID($mainButtonsAreaId) ?>">
	<?php
	$this->AddEditAction(
		$mainButtonsAreaId,
		$mainButtons["edit"]["add_element"]["ACTION_URL"],
		$mainButtons["edit"]["add_element"]["TITLE"]
	);
	?>
    <div class="swiper-container">
        <div class="swiper-wrapper">
            <?foreach($arResult["ITEMS"] as $arItem):?>
                <?
                $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
                $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
                ?>
                <div class="swiper-slide banks-slider__slide">
                    <div class="banks-slider__card" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
                        <div class="banks-slider__image-container">
                            <img class="banks-slider__image"
                                 src="<?=$arItem['PREVIEW_PICTURE']['SRC']?>"
                                 alt="<?=$arItem['PREVIEW_PICTURE']['ALT']?>"
                                 title="<?=$arItem['PREVIEW_PICTURE']['TITLE']?>">
                        </div>
                        <div class="banks-slider__content">
	                        <div class="banks-slider__properties">
		                        <?foreach ( array('CREDIT_PERIOD', 'RATE', 'INITIAL_PAYMENT') as $propertyCode):?>
		                            <?$arProperty = $arItem['PROPERTIES'][$propertyCode]?>
		                            <?if($arProperty['VALUE']):?>
			                        <div class="banks-slider__property">
				                        <div class="banks-slider__property-name">
					                        <?=$arProperty['NAME']?>
				                        </div>
				                        <div class="banks-slider__property-value">
					                        <?=$arProperty['VALUE']?> <?=$arProperty['HINT']?>
				                        </div>
			                        </div>
			                        <?endif;?>
		                        <?endforeach;?>
	                        </div>

	                        <?if($arItem['PROPERTIES']['CREDIT_TYPE']['VALUE']):?>
		                        <div class="banks-slider__credit-type">
			                        <?=$arItem['PROPERTIES']['CREDIT_TYPE']['VALUE']?>
		                        </div>
	                        <?endif;?>
                        </div>
                    </div>
                </div>
            <? endforeach;?>
        </div>
    </div><!-- .swiper-container -->


	<div class="swiper-button-prev"><i class="<?=$arParams['SWIPER_ICON_PREFIX']?>left"></i></div>
	<div class="swiper-button-next"><i class="<?=$arParams['SWIPER_ICON_PREFIX']?>right"></i></div>

    <div class="swiper-pagination swiper-pagination--lines"></div>
    <script>
       ;(function(){
           var equalHeight = function () {
	           $('.banks-slider .banks-slider__properties').responsiveEqualHeightGrid();
           };
           // http://idangero.us/swiper/api/
           new Swiper('.banks-slider .swiper-container', {
	           // pagination
	           pagination: {
		           el: '.banks-slider .swiper-pagination',
		           clickable: true,
		           renderBullet: swiperRenderBullets,
	           },
	           watchOverflow: true,

	           // Navigation arrows
	           navigation: {
		           nextEl: '.banks-slider .swiper-button-next',
		           prevEl: '.banks-slider .swiper-button-prev',
	           },

	           slidesPerView: 'auto',
	           spaceBetween: 30,

	           breakpoints: {
		           479: {
			           slidesPerView: 'auto',
			           spaceBetweenSlides: 30
		           }
	           },

	           on: {
		           init: equalHeight,
		           resize: equalHeight,
	           },
           });
       }());
    </script>
</div><!-- .benefits -->

<?endif;?>
