<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$this->setFrameMode(true);

?>
<div class="article-detail">
    <div class="article-user-ava">
	    <?
	    $picture = is_array($arResult["PREVIEW_PICTURE"]) ? $arResult["PREVIEW_PICTURE"] : $arResult["DETAIL_PICTURE"];
	    $width = (int)$arParams['RESIZE_IMAGE_WIDTH'] <= 0 ? 170 : (int)$arParams['RESIZE_IMAGE_WIDTH'];
	    if ($arParams["DISPLAY_PICTURE"] != "N" && is_array($picture)):
		    $arSmallPicture = CFile::ResizeImageGet(
			    $picture["ID"],
			    array(
				    'width' => $width,
				    'height' => (int)$arParams['RESIZE_IMAGE_HEIGHT'] <= 0 ? 170 : (int)$arParams['RESIZE_IMAGE_HEIGHT'],
			    ),
			    BX_RESIZE_IMAGE_EXACT,
			    $bInitSizes = true
		    );
		    ?>
		    <span style="min-width: <?=$width?>px; background-image: url('<?= $arSmallPicture["src"] ?>');"></span>
	    <? endif ?>
    </div><!-- .article-user-ava -->

    <div class="article-body">
        <?php
        if ($arParams["DISPLAY_DATE"] == 'Y')
        {
            ?>
            <span class="article-meta"><?=$arResult["DISPLAY_ACTIVE_FROM"]?></span>
            <?php
        }

        ?>
        <div class="article-desc">
            <?=$arResult["DETAIL_TEXT"]?>

            <?if(array_key_exists("USE_SHARE", $arParams) && $arParams["USE_SHARE"] == "Y")
            {
	            ?>
                <div class="news-detail-share">
                    <noindex>
			            <?
			            $APPLICATION->IncludeComponent("bitrix:main.share", "", array(
				            "HANDLERS" => $arParams["SHARE_HANDLERS"],
				            "PAGE_URL" => $arResult["~DETAIL_PAGE_URL"],
				            "PAGE_TITLE" => $arResult["~NAME"],
				            "SHORTEN_URL_LOGIN" => $arParams["SHARE_SHORTEN_URL_LOGIN"],
				            "SHORTEN_URL_KEY" => $arParams["SHARE_SHORTEN_URL_KEY"],
				            "HIDE" => $arParams["SHARE_HIDE"],
			            ),
				            $component,
				            array("HIDE_ICONS" => "Y")
			            );
			            ?>
                    </noindex>
                </div>
	            <?
            }?>
        </div>
    </div><!-- .article-body -->
</div><!-- .article-detail -->

