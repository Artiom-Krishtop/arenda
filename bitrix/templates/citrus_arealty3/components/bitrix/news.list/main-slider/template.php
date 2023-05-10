<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

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

use Bitrix\Main\Localization\Loc;

$this->setFrameMode(true);

?>

<?if(!empty($arResult["ITEMS"])):?>
    <div class="p__swiper main-slider <?=$arParams['SLIDER_COLOR'] ? '_color-'.$arParams['SLIDER_COLOR'] : ''?>">
        <div class="swiper-container">
            <div class="swiper-wrapper">
                <?foreach($arResult["ITEMS"] as $arItem):?>
                    <?
                    $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
                    $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
                    ?>
                <div class="swiper-slide">
                    <div class="main-slider__image" style="background-image: url(<?=$arItem['PREVIEW_PICTURE']['SRC']?>);"></div>
                    <div class="main-slider__content" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
                        <span class="main-slider__content-blur js-blur-layer" style="background-image: url(<?=$arItem['PREVIEW_PICTURE']['SRC']?>);"></span>
                        <div class="main-slider__name font-2">
                            <?=$arItem['NAME']?>
                        </div>
                        <?if($arItem['PREVIEW_TEXT']):?>
                            <div class="main-slider__description font-2">
                                <?=$arItem['PREVIEW_TEXT']?>
                            </div>
                        <?endif;?>
                        <?if($arItem['PROPERTIES']['btn_link']['VALUE']):?>
                            <a href="<?=\Citrus\Arealty\Template\TemplateHelper::makeSiteLink($arItem['PROPERTIES']['btn_link']['VALUE'])?>"
                               class="btn btn-primary _shadow btn-stretch">
	                            <?=$arItem['PROPERTIES']['btn_text']['VALUE'] ?:
		                            Loc::getMessage("MAIN_SLIDER_DEFAULT_BTN_TITLE")?>
                            </a>
                        <?endif;?>
                    </div>
                </div>
                <?endforeach;?>
            </div>
        </div><!-- .swiper-container -->

        <div class="w"><div class="swiper-pagination swiper-pagination--lines"></div></div>
        <script>
           ;(function(){
	           var resizeBlurLayer = function () {
		           $('.main-slider .swiper-slide').each(function(index, item) {
			           var $slide = $(this);
			           $slide.find('.js-blur-layer').css({
				           width: $slide.width(),
				           height: $slide.outerHeight()
			           });
		           });

                   /**
                    * Для маленьких разрешений: чтобы для слайдов с меньшей высотой (текста)
                    * не оставалось пустого места между картинкой слайда и пагинацией слайдера
                    */
                   $('.main-slider .main-slider__content').responsiveEqualHeightGrid();
	           };
	           // http://idangero.us/swiper/api/
	           new Swiper('.main-slider .swiper-container', {
		           autoplay: {
			           delay: 5000
		           },
                   // иначе вся страница «прыгает» на мобильных при переключении слайдов
		           autoHeight: false,
		           // pagination
		           pagination: {
			           el: '.main-slider .swiper-pagination',
			           clickable: true,
			           renderBullet: swiperRenderBullets,
		           },
		           on: {
			           'init': resizeBlurLayer,
			           'resize': resizeBlurLayer
		           },
		           watchOverflow: true
	           });
           }());
        </script>
    </div><!-- .main-slider -->
<?endif;?>
