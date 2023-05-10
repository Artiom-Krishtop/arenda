<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

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

Loc::loadMessages(__FILE__);

?>
<div class="p__swiper recommendation-list _nav-offset _pagination-hide-nav">
    <div class="swiper-container">
        <div class="swiper-wrapper">
			<? foreach ($arResult["ITEMS"] as $arItem): ?>
				<?
				$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'],
					CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
				$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'],
					CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"),
					array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
				?>
                <div class="swiper-slide" id="<?=$this->GetEditAreaId($arItem["ID"])?>">
                    <div class="recommendation-item">
                        <div class="recommendation-item-header">
                            <?php
                            $picture = is_array($arItem["PREVIEW_PICTURE"]) ? $arItem["PREVIEW_PICTURE"] : $arItem["DETAIL_PICTURE"];
                            if ($arParams["DISPLAY_PICTURE"] != "N" && is_array($picture))
                            {
	                            $arSmallPicture = CFile::ResizeImageGet(
		                            $picture["ID"],
		                            array(
			                            'width' => intval($arParams['RESIZE_IMAGE_WIDTH']) <= 0 ? 200 : intval($arParams['RESIZE_IMAGE_WIDTH']),
			                            'height' => intval($arParams['RESIZE_IMAGE_HEIGHT']) <= 0 ? 200 : intval($arParams['RESIZE_IMAGE_HEIGHT']),
		                            ),
		                            BX_RESIZE_IMAGE_EXACT,
		                            $bInitSizes = true
	                            );
	                            ?>
                                <div class="recommendation-item-ava">
                                    <span style="background-image: url('<?=$arSmallPicture["src"]?>');"></span>
                                </div>
	                            <?php
                            }
                            ?>
                            <div class="recommendation-item-info">
                                <?php
                                if ($arParams['DISPLAY_NAME'] !== 'N')
                                {
                                    ?>
                                    <span class="recommendation-item-name"><?=$arItem["NAME"]?></span>
                                    <?php
                                }

                                $meta = [];
                                if ($arParams['DISPLAY_DATE'] !== 'N')
                                {
                                    $meta[] = ToLower($arItem["DISPLAY_ACTIVE_FROM"]);
                                }
                                foreach ($arParams['PROPERTY_CODE'] as $property)
                                {
                                    if ($arItem["PROPERTIES"][$property]["VALUE"])
                                    {
                                        $meta[] = $arItem["DISPLAY_PROPERTIES"][$property]["VALUE"];
                                    }
                                }

                                $meta = array_filter($meta);
                                if (count($meta))
                                {
                                    ?>
                                    <span class="recommendation-item-meta">
	                                    <?=implode(', ', $meta)?>
                                    </span>
                                    <?
                                }
                                ?>
                            </div>
                        </div>
                        <div class="recommendation-item-body">
                            <?php
                            if ($arParams['DISPLAY_PREVIEW_TEXT'] !== 'N')
                            {
                                $text = $arItem["DETAIL_TEXT"] ?: $arItem['PREVIEW_TEXT'];
                                if (strlen(trim($text)))
                                {
                                    ?><div class="recommendation-item-text"><?=$text?></div><?php
                                }
                            }
                            ?>
                        </div>
                    </div>
                </div><!-- .swiper-slide -->
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
</div><!-- .recommendation-list -->

<? if ($arParams["LINK_SHOW_ALL"]): ?>
    <div class="section-footer">
        <a href="<?=str_replace('//', '/', CComponentEngine::makePathFromTemplate($arResult['LIST_PAGE_URL']))?>" class="btn btn-secondary"><?= Loc::getMessage("CITRUS_AREALTY_REVIEWS_READ_ALL") ?></a>
    </div>
<? endif; ?>