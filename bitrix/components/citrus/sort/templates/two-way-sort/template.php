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
	"CARDS" => "fa-th-large",
	"LIST" => "fa-th-list",
);
?>

<div class="content-panel">
    <div class="sorting">
        <div class="sorting-label">
            <span><?= Loc::getMessage("CITRUS_TEMPLATE_SORT_NAME") ?></span>
        </div>
        <div class="sorting-list">
            <?foreach ( $arResult["SORT_ITEMS"] as $key => $arItem):?>
                <div class="sorting-item">
                    <span class="sorting-item-label"><?=$arItem["NAME"]?></span>
                    <?foreach ( $arItem["LINKS"] as $arLink):?>
                        <a href="<?=$arLink["HREF"]?>" rel="nofollow" class="btn btn-secondary sorting-btn <?if ($arLink["SELECTED"]):?>is-active<?endif;?>">
                            <span class="btn-icon fa fa-fw fa-sort-amount-<?=strtolower($arLink["ORDER"])?>"></span>
                        </a>
                    <?endforeach;?>
                </div>
            <?endforeach;?>
        </div>
    </div>

    <div class="views">
        <div class="views-label">
            <span><?= Loc::getMessage("CITRUS_TEMPLATE_VIEW_NAME") ?></span>
        </div>
        <div class="views-list">
            <?foreach ( $arResult["VIEW_ITEMS"] as $arView):?>

                <div class="views-item">
                    <a href="<?=$arView["HREF"]?>" rel="nofollow" class="btn btn-secondary views-btn <?if ($arView["SELECTED"]):?>is-active<?endif;?>">
                        <span class="btn-icon fa fa-fw <?=$viewIcons[$arView["CODE"]]?>"></span>
                    </a>
                </div>
            <?endforeach;?>
        </div>
    </div>
</div>
