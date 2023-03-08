<?php

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
use Bitrix\Main\Localization\Loc;

$this->setFrameMode(true);

$arSection = null;
if (is_set($arResult, 'SECTION') && is_Set($arResult['SECTION'], 'PATH') && is_array($arResult['SECTION']['PATH']))
	$arSection = array_shift(array_values($arResult['SECTION']['PATH']));

if (count($arResult['ITEMS']) <= 0)
{
	?><p><i style="color: #94979a"><?=GetMessage("CITRUS_QUESTSION_LIST_EMPTY")?></i></p><?
	return;
}
?>
<?
if($arParams["DISPLAY_TOP_PAGER"])
{
	echo $arResult["NAV_STRING"] . '<br />';
}
?>
<div class="ask-list">
	<?
	foreach($arResult["ITEMS"] as $arItem):
        $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
        $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));?>
        <div class="ask-item" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
            <div class="ask-meta" data-toggle="ask_answer" data-id="<?=$arItem["ID"]?>" >
                <div class="ask-title">
                    <a href="javascript://" rel="nofollow" class="btn-link"><?=$arItem['PREVIEW_TEXT']?></a>
                </div>
                <span class="ask-user-name">
                    <?=$arItem['NAME']?>,
                </span>
                <span class="item-date-new"><?=$arItem["DISPLAY_ACTIVE_FROM"]?></span>
                <div class="show-menu-arrow">
                    <i class="fa fa-angle-right"></i>
                </div>
            </div>
            <div class="ask-answer" id="answer_<?=$arItem["ID"]?>">
                <div class="ask-answer-text">
                    <?=(strlen(trim($arItem["DETAIL_TEXT"])) > 0 ? $arItem["DETAIL_TEXT"] : '<strong>'.Loc::getMessage("CITRUS_NO_ANSWER").'</strong>')?>
                </div>
            </div>
        </div><!-- .ask-item -->
    <?endforeach;?>
</div>

<!--<div class="section-footer">
    <a href="javascript://" class="btn btn-stretch"><?/*=GetMessage("CITRUS_SHOW_MORE_QUESTIONS")*/?></a>
</div>-->

<?
if ($arParams["DISPLAY_BOTTOM_PAGER"])
{
	echo $arResult["NAV_STRING"] . '<br />';
}
?>

<script type="text/javascript">
    $(function () {
        function arrowResize() {
            $('.ask-meta').each(function () {
                var arrow = $(this).find(".show-menu-arrow");
                if (arrow.length <= 0)
                    return;
                var arrowPos = ($(this).outerHeight() / 2) - (arrow.outerHeight() / 2);
                arrow.css("top", arrowPos);

                console.log($(this).outerHeight());
            });
        }

        $('[data-toggle="ask_answer"]').on("click", function () {
            var id = $(this).data('id');
            $('#answer_' + id).slideToggle();
            $(this).find('.show-menu-arrow').toggleClass('open');
        });

        arrowResize();
        $(window).resize(function () {
            arrowResize();
        });
    });
</script>