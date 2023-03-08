<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

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

$viewIcons = array(
	"CARDS" => "icon-view_cards",
	"LIST" => "icon-view_list",
	"TABLE" => "icon-view_table"
);
$sortIcons = array(
    'ASC' => 'icon-sort-hight',
    'DESC' => 'icon-sort-low'
);
?>

<div class="content-panel">
    <div class="sorting">
	    <?if($arResult['VIEW'] !== 'TABLE'):?>
        <div class="sorting-label">
            <span><?=Loc::getMessage("CITRUS_TEMPLATE_SORT_NAME")?>:</span>
        </div>
        <div class="sorting-list">
            <?foreach ( $arResult["SORT_ITEMS"] as $key => $arItem):?>
                <a href="<?=$arItem['REVERSE_SORTING_LINK']?>" class="sorting-item <?=$arItem['SELECTED'] ? '_selected' : ''?>">
                    <div class="sorting-item-label"><?=$arItem["NAME"]?></div>
                    <?if($arItem['SELECTED']):?>
                        <span class="sorting-item-icon <?=$sortIcons[$arResult['ORDER']]?>" style="transform: unset;"></span>
                    <?endif;?>
                </a>
            <?endforeach;?>
        </div>
	    <?endif;?>
    </div>

    <div class="views">
        <div class="views-list">
            <?foreach ( $arResult["VIEW_ITEMS"] as $arView):?>
                <div class="views-item">
                    <a href="<?=$arView["HREF"]?>" rel="nofollow" class="views-btn <?if ($arView["SELECTED"]):?>is-active<?endif;?>">
                        <span class="btn-icon fa fa-fw <?=$viewIcons[$arView["CODE"]]?>"></span>
                    </a>
                </div>
            <?endforeach;?>
        </div>
    </div>
</div>
