<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

$this->setFrameMode(true);
?>
<? if (!empty($arResult["ITEMS"])): ?>
    <div class="news-standard<?=($arParams['DISPLAY_PREVIEW_TEXT'] !== 'N' ? ' _with-text' : '')?>">
        <div class="row">
            <? foreach ($arResult["ITEMS"] as $arItem): ?>
                <?
                $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'],
                    CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
                $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'],
                    CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"),
                    array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
                ?>
                <div class="col-lg-4 col-sm-6">
                    <a href="<?=$arItem['DETAIL_PAGE_URL']?>" class="news-standard__item"
                       id="<?=$this->GetEditAreaId($arItem['ID']);?>">
	                    <?
	                    if ($arParams['DISPLAY_PICTURE'] !== 'N')
	                    {
	                    	?>
		                    <span class="news-standard__img-container img-placeholder">
	                            <span class="news-standard__img"
		                            <? if ($arItem['PREVIEW_PICTURE']['SRC']): ?>
			                            style="background-image: url('<?=$arItem['PREVIEW_PICTURE']['SRC']?>');"
		                            <? endif; ?>>
	                            </span>
	                        </span>
		                    <?
	                    }
	                    ?>
                        <span class="news-standard__content">
	                        <?
	                        if ($arParams['DISPLAY_NAME'] !== 'N')
	                        {
	                        	?>
		                        <span class="news-standard__name"><?=$arItem['NAME']?></span>
		                        <?php
	                        }
                            if ($arParams['DISPLAY_DATE'] !== 'N')
                            {
                                ?><span class="news-standard__date"><?=$arItem['DISPLAY_ACTIVE_FROM']?></span><?
                            }
	                        if ($arParams['DISPLAY_PREVIEW_TEXT'] !== 'N' && $arItem['PREVIEW_TEXT'])
	                        {
		                        ?><span class="news-standard__text"><?=$arItem['PREVIEW_TEXT']?></span><?
	                        }

	                        if (count($arItem["DISPLAY_PROPERTIES"]) > 0)
	                        {
		                        ?><?$APPLICATION->IncludeComponent(
			                        'citrus.arealty:properties',
			                        '',
			                        [
				                        'PROPERTIES' => $arItem['DISPLAY_PROPERTIES'],
				                        'DISPLAY_PROPERTIES' => array_keys($arItem["DISPLAY_PROPERTIES"]),
				                        'CSS_CLASS' => 'news-standard__props',
				                        'SHOW_HEADINGS' => 'Y',
			                        ],
			                        $component,
			                        ['HIDE_ICONS' => 'Y']
		                        );?><?php
	                        }

                            ?>
                        </span>
                    </a><!-- .news-standard__item -->
                </div>
            <? endforeach; ?>
        </div><!-- .row -->
    </div><!-- .news-standard -->

    <script>
        ;(function () {
            $('.news-standard').each(function () {
                $(this).find('.news-standard__content').responsiveEqualHeightGrid();
            });
        }());
    </script>
<? endif; ?>

<? if ($arParams["DISPLAY_BOTTOM_PAGER"]): ?>
    <div class="b-news-list-pager b-news-list-pager-bottom">
        <?=$arResult["NAV_STRING"]?>
    </div>
<? endif; ?>
