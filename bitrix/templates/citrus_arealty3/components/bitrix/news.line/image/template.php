<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$this->setFrameMode(true);

?>
<div class="p__swiper license-swiper _nav-offset _pagination-hide-nav">
    <div class="swiper-container">
        <div class="swiper-wrapper">
			<? foreach ($arResult["ITEMS"] as $arItem): ?>
				<?
				$bIncludeAreas = $APPLICATION->GetShowIncludeAreas();
				if ($bIncludeAreas)
				{
					$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'],
						CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
					$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'],
						CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"),
						array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
				}

				if (is_array($arItem["DETAIL_PICTURE"]) && count($arItem["DETAIL_PICTURE"]) > 0)
				{
					$arPicture = $arItem["DETAIL_PICTURE"];
				}
                elseif (is_array($arItem["PREVIEW_PICTURE"]) && count($arItem["PREVIEW_PICTURE"]) > 0)
				{
					$arPicture = $arItem["PREVIEW_PICTURE"];
				}
				$arSmallPicture = CFile::ResizeImageGet(
					$arPicture,
					array(
						'width' => intval($arParams['RESIZE_IMAGE_WIDTH']) <= 0 ? 150 : intval($arParams['RESIZE_IMAGE_WIDTH']),
						'height' => intval($arParams['RESIZE_IMAGE_HEIGHT']) <= 0 ? 150 : intval($arParams['RESIZE_IMAGE_HEIGHT']),
					),
					BX_RESIZE_IMAGE_PROPORTIONAL,
					true
				);
				?>
                <div class="swiper-slide" <?=($bIncludeAreas ? 'id="' . $this->GetEditAreaId($arItem['ID']) . '"' : '')?>>
                    <a href="<?=$arPicture["SRC"]?>" rel="nofollow" target="_blank"
                       data-size="<?=$arPicture["WIDTH"]?>x<?=$arPicture["HEIGHT"]?>" class="gallery-swipe"
                       title="<?=$arItem["NAME"]?>">
                        <img src="<?=$arSmallPicture['src']?>" alt="<?=$arItem["NAME"]?>"
                             width="<?=$arSmallPicture['width']?>" height="<?=$arSmallPicture['height']?>"
                             class="gallery-image">
                        <span class="gallery-title"><?=$arItem["NAME"]?></span>
						<? if ($arItem["PREVIEW_TEXT"]): ?>
                            <span class="gallery-text"><?=$arItem["PREVIEW_TEXT"]?></span>
						<? endif; ?>
                    </a>
                </div>
			<? endforeach; ?>
        </div>
    </div>
    <div class="swiper-pagination"></div>
    <div class="swiper-button-prev swiper-button--template1">
        <i class="icon-arrow-left"></i>
    </div>
    <div class="swiper-button-next swiper-button--template1">
        <i class="icon-arrow-right"></i>
    </div>
</div>
