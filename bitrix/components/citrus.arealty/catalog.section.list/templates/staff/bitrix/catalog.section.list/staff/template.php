<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

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

use \Bitrix\Main\Localization\Loc;

$this->setFrameMode(true);

$strSectionEdit = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "SECTION_EDIT");
$strSectionDelete = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "SECTION_DELETE");
$arSectionDeleteParams = array("CONFIRM" => GetMessage('CT_BCSL_ELEMENT_DELETE_CONFIRM'));

if ($arResult['SECTIONS_COUNT'] == 0)
{
	return;
}

$arResult["ACTIVE_INDEX"] = -1;
foreach ($arResult['SECTIONS'] ?: [] as $idx => $arSection)
{
    if ($arParams['CURRENT_SECTION_CODE'] === $arSection['CODE'])
    {
        $arResult['ACTIVE_INDEX'] = $idx;
    }
}

?>

<div class="p__swiper nav-list-container" data-swiper-active-index="<?=($arResult['ACTIVE_INDEX']+1)?>">
    <div class="swiper-container">
        <ul class="nav-list swiper-wrapper">
            <?php $classActive = $arParams['CURRENT_SECTION_CODE'] == ''? 'active' : ''; ?>
            <li class="nav-item swiper-slide <?= $classActive ?>">
                <a href="<?= $arParams['SEF_FOLDER'] ?>" class="nav__link">
                    <span class="nav__item-text"><?= Loc::getMessage('CT_BCSL_ELEMENT_SECTION_ALL_ITEMS') ?></span>
                </a>
            </li>

            <?php

            foreach ($arResult['SECTIONS'] ?: [] as $idx => &$arSection)
            {
                $this->AddEditAction($arSection['ID'], $arSection['EDIT_LINK'], $strSectionEdit);
                $this->AddDeleteAction($arSection['ID'], $arSection['DELETE_LINK'], $strSectionDelete, $arSectionDeleteParams);
                $classActive = $idx === $arResult['ACTIVE_INDEX'] ? 'active' : '';
                ?>
                <li class="nav-item swiper-slide <?= $classActive ?>" id="<?=$this->GetEditAreaId($arSection['ID']); ?>">
                    <a href="<?= $arSection['SECTION_PAGE_URL']; ?>" class="nav__link">
                        <span class="nav__item-text"><?=$arSection['NAME']; ?></span>
                    </a>
                </li>
                <?php
            }
            ?>
        </ul>
    </div>
    <div class="swiper-button-next">
        <span class="fa fa-angle-right"></span>
    </div>
    <div class="swiper-button-prev swiper-button-disabled">
        <span class="fa fa-angle-left"></span>
    </div>
</div>