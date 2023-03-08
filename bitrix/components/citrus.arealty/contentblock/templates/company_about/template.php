<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

$this->setFrameMode(true);

$this->AddEditAction($arResult['ID'], $arResult['EDIT_LINK'], \CIBlock::GetArrayByID($arResult['IBLOCK_ID'], 'ELEMENT_EDIT'));

if (!$component->getParent() instanceof \Citrus\Core\IncludeComponent)
{
	?><div class="h1"><?=$arResult['TITLE']?></div><?php

	if ($arResult['SUBTITLE'])
	{
		?><div class="section-description"><?=$arResult['SUBTITLE']?></div><?
	}
}

?>
<div class="row row-grid" id="<?=$this->GetEditAreaId($arResult['ID'])?>">
    <div class="col-lg-5 col-dt-4 about_personal">
        <? $APPLICATION->IncludeComponent(
            "citrus.arealty:contentblock",
            "staff",
            array(
	            "IBLOCK_ID" => \Citrus\Arealty\Helper::getIblock('staff'),
	            "ELEMENT_ID" => $arResult['CONTACT_ID'],
            ),
            $component
        ); ?>
    </div>
    <div class="col-lg-7 col-dt-8 text_about">
        <?=$arResult['PREVIEW_TEXT']?>
        <?php

        if ($arParams['SHOW_DETAIL_LINK'] == 'Y')
        {
            ?>
            <div class="section-footer">
                <a href="<?=$arResult['DETAIL_PAGE_URL']?>" class="btn btn-secondary btn-big btn-stretch">
                    <?= Loc::getMessage("CITRUS_CONTENT_BLOCK_DETAIL_LINK") ?>
                </a>
            </div>
            <?php
        }

        ?>
    </div>
</div>
<?php

