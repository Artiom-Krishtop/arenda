<?php

use Bitrix\Main\Localization\Loc;

// TODO ����������� ��� ���� ������������� ��� ����� (���������, ������ � ����������, �����-������?)

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

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

if (empty($arResult['ITEMS']))
{
	return;
}
?>

<div class="sf-duplicate hidden" id="sf-duplicate"></div><!-- .sf-duplicate -->
<section class="filter citrus-sf-wrapper">
	<form
            name="<? echo $arResult["FILTER_NAME"] . "_form" ?>"
            action="<? echo $arResult["FORM_ACTION"] ?>"
            method="get"
            class="filter-form citrus-sf row"
            id="smartfilter"
    >
		<? foreach ($arResult["HIDDEN"] as $arItem): ?>
			<input type="hidden" name="<? echo $arItem["CONTROL_NAME"] ?>" id="<? echo $arItem["CONTROL_ID"] ?>" value="<? echo $arItem["HTML_VALUE"] ?>"/>
		<? endforeach; ?>
		<div class="citrus-sf__fields-block">
            <div class="row">
			<?
            $ITEMS_COUNTER = 0;
			foreach ($arResult["ITEMS"] as $blockId => $arItem):
				$ITEMS_COUNTER++;
				?>
                <div class="col-lg-4 col-md-6 <?= $ITEMS_COUNTER > $arParams['MAX_ITEMS_COUNT']? 'citrus-sf-more' : '' ?>">
				<div
                        class="citrus-sf-field"
                        data-display-type="<?=$arItem["DISPLAY_TYPE"]?>"
                        data-property-code="<?=$arItem["CODE"];?>"
                        data-template="<?=$arItem['TEMPLATE']['NAME']?>"
                        data-combine="<?=$arItem['IS_COMBINE']?>"
                >
                    <?
                    if($arItem["TEMPLATE"]["PATH"]){
                        //set in result_modifier.php
                        include $arItem["TEMPLATE"]["PATH"].'template.php';
                    } else {
                        echo '<pre>';
                        echo 'TEMPLATE FILE NOT FOUND: <nobr><b>'.$arItem["TEMPLATE"]['NAME'].'</b></nobr>';
                        echo '</pre>';
                    }?>
                </div><!-- .citrus-sf-field -->
                </div><!-- .col-md-4 -->
            <?
            endforeach;
            ?>
            </div><!-- .row -->
		</div><!-- .col-dt-8 -->

		<div class="citrus-sf__button-block">
			<button type="submit" class="citrus-sf__button _submit" id="set_filter" name="set_filter">
                <span class="citrus-sf__button-label"><?=Loc::getMessage("CITRUS_FILTER_FILTER_BUTTON")?></span>
				<span class="citrus-sf__button__additional-label" id="modef_num"><?=(int)$arResult["ELEMENT_COUNT"]?></span>
            </button>
			<button type="submit" class="citrus-sf__button _reset" id="del_filter" name="del_filter"><?=Loc::getMessage("CITRUS_FILTER_FILTER_RESET_BUTTON")?></button>
			<? if ($arResult["SHOW_EXPANDED"]): ?>
			<a class="filter-more-link"
               href="javascript:void(0)" rel="nofollow"
               onclick="smartFilter.toggleExpanded()"
            >
                <span class="filter-more-link__open"><?=Loc::getMessage("CITRUS_FILTER_SHOW")?></span>
                <span class="filter-more-link__close"><?=Loc::getMessage("CITRUS_FILTER_HIDE")?></span>
            </a>
			<? endif ?>
		</div>
	</form>
</section>
<script>
    <?
    $arResult["JS_FILTER_PARAMS"]["LANG"] = array(
        'FROM' => Loc::getMessage('CT_BCSF_FILTER_FROM'),
	    'TO' => Loc::getMessage('CT_BCSF_FILTER_TO'),
	    'VALUES' => Loc::getMessage('CITRUS_FILTER_RESULT_VALUES')
    );
    ?>
    /**
     * smart filter object
     * @type {JCSmartFilter}
     */
	var smartFilter = new JCSmartFilter('<?echo CUtil::JSEscape($arResult["FORM_ACTION"])?>', 'vertical', <?=CUtil::PhpToJSObject($arResult["JS_FILTER_PARAMS"])?>);
</script>