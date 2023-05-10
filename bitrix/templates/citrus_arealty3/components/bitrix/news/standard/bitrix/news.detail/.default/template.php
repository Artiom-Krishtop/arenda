<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
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

$text = $arResult['DETAIL_TEXT'];
if (!strlen(trim(strip_tags($text))))
{
	$text = $arResult['PREVIEW_TEXT'];
}

?>

<div class="news-detail">

    <? if ($arResult['DETAIL_PICTURE']['MIN']): ?>
        <div class="news-detail__img">
            <img src="<?=$arResult['DETAIL_PICTURE']['MIN']['src']?>" alt=""
                 title="<?=$arResult['NAME']?>">
        </div>
    <? endif; ?>

    <div class="news-detail__detail-text">
        <?=$text?>
    </div>

	<?php

	$displayProperties = array_diff_key($arResult["DISPLAY_PROPERTIES"], ['GALLERY' => 1, 'contact' => 1]);
	if (count($displayProperties) > 0)
	{
		?><?$APPLICATION->IncludeComponent(
			'citrus.arealty:properties',
			'',
			[
				'PROPERTIES' => $arResult['DISPLAY_PROPERTIES'],
				'DISPLAY_PROPERTIES' => array_keys($displayProperties),
				'SHOW_HEADINGS' => 'Y',
				'HEADINGS_CLASS' => 'h2',
			],
			$component,
			['HIDE_ICONS' => 'Y']
		);?><?php
	}
	?>

    <? if ($arResult['DISPLAY_PROPERTIES']['GALLERY']['FILE_VALUE']): ?>
        <div class="p__swiper news-galery _nav-offset _pagination-bot">
            <div class="swiper-container">
                <div class="swiper-wrapper">
                    <? foreach ($arResult['DISPLAY_PROPERTIES']['GALLERY']['FILE_VALUE'] as $galleryItem):
                        ?>
                        <div class="swiper-slide">
                            <a href="<?=$galleryItem['SRC']?>" class="js-init-photoswipe"
                               data-size="<?=$galleryItem['WIDTH']?>x<?=$galleryItem['HEIGHT']?>"><img
                                        src="<?=$galleryItem['MIN']['src']?>" alt=""></a>
                        </div>
                    <? endforeach; ?>
                </div>
            </div><!-- .swiper-container -->
            <div class="swiper-pagination swiper-pagination--lines"></div>

            <div class="swiper-button-prev"><span class="icon-arrow_left"></span></div>
            <div class="swiper-button-next"><span class="icon-arrow_right"></span></div>

            <script>
                ;(function () {
                    // http://idangero.us/swiper/api/
                    var swiper = new Swiper('.news-galery .swiper-container', {
                        watchOverflow: true,
                        slidesPerView: 'auto',
                        spaceBetween: 30,
                        // pagination
                        pagination: {
                            el: '.news-galery .swiper-pagination',
                            clickable: true,
                            renderBullet: swiperRenderBullets
                        },
                        // Navigation arrows
                        navigation: {
                            nextEl: '.news-galery .swiper-button-next',
                            prevEl: '.news-galery .swiper-button-prev'
                        },
                        watchSlidesVisibility: true,
                    });

                    $("a.js-init-photoswipe").initPhotoSwipe({
                        events: {
                            afterChange: function () {
                                var index = this.getCurrentIndex();
                                var slide = swiper.slides[index];
                                if (!$(slide).hasClass('swiper-slide-visible')) swiper.slideTo(index, 0);
                            }
                        },
                        loop: false,
                        bgOpacity: .8
                    });
                }());
            </script>
        </div><!-- .news-galery -->
    <? endif; ?>
</div><!-- .news-detail -->

<footer class="section-footer">
    <div class="btn-row btn-row--xs-center">
        <? if ($arResult['PROPERTIES']['LINK_HREF']['VALUE']):
            $linkHref = strpos(SITE_DIR, $property->getValue('LINK_HREF')) === false
                ? str_replace('//', '/', SITE_DIR . $arResult['PROPERTIES']['LINK_HREF']['VALUE'])
                : $property->getValue('LINK_HREF');
            ?>
            <a href="<?=$linkHref?>" class="btn btn-primary">
                <?=$arResult['PROPERTIES']['LINK_HREF']['VALUE'] ?: Loc::getMessage("NEWS_DETAIL_MORE_BTN")?>
            </a>
        <? endif; ?>
        <a href="<?=$arResult["LIST_PAGE_URL"]?>"
           class="btn btn-secondary"><?=Loc::getMessage("NEWS_BACK")?></a>
    </div>
</footer>
