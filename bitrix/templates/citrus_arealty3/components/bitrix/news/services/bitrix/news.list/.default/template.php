<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

$this->setFrameMode(true);

?>

<div class="tac">
    <a class="btn btn-primary js_link_form_review" rel="nofollow" href="#">
        <span class="btn-label"><?=Loc::getMessage("CITRUS_REVIEWS_ADD_NEW")?></span>
    </a>
</div>

<div class="article">
    <div class="article-list">
		<? foreach ($arResult["ITEMS"] as $arItem): ?>
			<?
			$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'],
				CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
			$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'],
				CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"),
				array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
			?>
            <div class="article-item" id="<?=$this->GetEditAreaId($arItem['ID'])?>">
                <div class="article-body">
                    <div class="article-user">
                        <div class="article-user-ava">
	                        <? if ($arParams["DISPLAY_PICTURE"] != "N" && $arItem["PREVIEW_PICTURE"]):
		                        $arSmallPicture = CFile::ResizeImageGet(
			                        $arItem["PREVIEW_PICTURE"]["ID"],
			                        array(
				                        'width' => intval($arParams['RESIZE_IMAGE_WIDTH']) <= 0 ? 170 : intval($arParams['RESIZE_IMAGE_WIDTH']),
				                        'height' => intval($arParams['RESIZE_IMAGE_HEIGHT']) <= 0 ? 170 : intval($arParams['RESIZE_IMAGE_HEIGHT']),
			                        ),
			                        BX_RESIZE_IMAGE_EXACT,
			                        $bInitSizes = true
		                        );
		                        ?><span style="background-image: url('<?=$arSmallPicture["src"]?>');"></span>
	                        <? endif ?>
                        </div>
                        <div class="article-user-body">
							<? if ($arParams["DISPLAY_NAME"] != "N" && $arItem["NAME"]): ?>
                                <a class="article-user-name"
                                   href="<?=$arItem["DETAIL_PAGE_URL"]?>"><? echo $arItem["NAME"] ?></a>
							<? endif; ?>

							<? if ($arParams["DISPLAY_PREVIEW_TEXT"] != "N" && $arItem["PREVIEW_TEXT"]): ?>
                                <div class="article-desc">
									<?=$arItem["PREVIEW_TEXT"]?>
                                </div>
							<? endif; ?>
                            <a href="<?=$arItem['DETAIL_PAGE_URL']?>"><?=Loc::getMessage("CITRUS_AREALTY_READ_FULL_REVIEW")?></a>
                        </div>
                    </div><!-- /article-user -->

                </div>
            </div>
		<? endforeach; ?>
    </div><!-- .article-list -->
</div><!-- .article -->

<? if ($arParams["DISPLAY_BOTTOM_PAGER"]): ?>
    <div class="b-reviews-list-pager b-reviews-list-pager-bottom">
		<?=$arResult["NAV_STRING"]?>
    </div>
<? endif; ?>
