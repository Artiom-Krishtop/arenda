<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use \Bitrix\Main\Localization\Loc;

if (empty($arResult["ITEMS"]))
{
	return;
}

$this->setFrameMode(true);

?>

<div class="p__swiper  partner-list partner-list-sw _nav-offset _pagination-hide-nav">
    <div class="swiper-container" id="slide-partner">
        <div class="swiper-wrapper">
			<? foreach ($arResult["ITEMS"] as $arItem): ?>
				<?
				$this->AddEditAction('partner_' . $arItem['ID'], $arItem['EDIT_LINK'],
					CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
				$this->AddDeleteAction('partner_' . $arItem['ID'], $arItem['DELETE_LINK'],
					CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"),
					array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));

				$arPicture = $arItem["PREVIEW_PICTURE"] ? $arItem["PREVIEW_PICTURE"] : $arItem["DETAIL_PICTURE"];
				if ($arPicture)
				{
					$arSmallPicture = CFile::ResizeImageGet(
						$arPicture,
						array(
							'width' => intval($arParams['RESIZE_IMAGE_WIDTH']) <= 0 ? 250 : intval($arParams['RESIZE_IMAGE_WIDTH']),
							'height' => intval($arParams['RESIZE_IMAGE_HEIGHT']) <= 0 ? 250 : intval($arParams['RESIZE_IMAGE_HEIGHT']),
						),
						BX_RESIZE_IMAGE_PROPORTIONAL,
						$bInitSizes = true
					);
				}
				?>
				<? if ($arPicture): ?>
                    <div class="swiper-slide">
                        <div class="item_wr" id="<?=$this->GetEditAreaId('partner_' . $arItem['ID']);?>">
                            <img src="<?=$arSmallPicture["src"]?>"
                                 alt="<?=($arPicture["ALT"]) ? $arPicture["ALT"] : $arItem["NAME"]?>"
                                 title="<?=($arPicture["TITLE"]) ? $arPicture["TITLE"] : $arItem["NAME"]?>">
                        </div>
                    </div>
				<? endif; ?>
			<? endforeach; ?>
        </div><!-- .swiper-wrapper -->
    </div><!-- .swiper-container -->
    <div class="swiper-pagination"></div>
    <div class="swiper-button-prev swiper-button--template1">
        <i class="icon-arrow-left"></i>
    </div>
    <div class="swiper-button-next swiper-button--template1">
        <i class="icon-arrow-right"></i>
    </div>
</div><!-- .partner-list -->

<script>
    ;(function () {
        new Swiper(".partner-list .swiper-container", {
            navigation: {
                nextEl: '.partner-list .swiper-button-next',
                prevEl: '.partner-list .swiper-button-prev',
            },
            pagination: {
                el: '.partner-list .swiper-pagination',
                clickable: true,
            },
            loop: true,
            slidesPerView: 5,
            spaceBetween: 30,
            breakpoints: {
                479: {
                    slidesPerView: 1
                },
                767: {
                    slidesPerView: 2
                },
                1023: {
                    slidesPerView: 3
                },
                1279: {
                    slidesPerView: 4
                }
            },
        });
    })();
</script>

