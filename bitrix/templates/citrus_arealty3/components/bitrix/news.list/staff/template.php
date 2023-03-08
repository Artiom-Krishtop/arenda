<?php

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$this->setFrameMode(true);

if ($arParams['DISPLAY_SECTION_NAME'] == 'Y')
{
	$section = reset($arResult['SECTION']['PATH']);
}
else
{
    $section = null;
}

?>

<?if($arParams["DISPLAY_TOP_PAGER"]):?>
	<div class="b-staff-list-pager b-staff-list-pager-top">
		<?=$arResult["NAV_STRING"]?>
	</div>
<?endif;?>

<div class="staff_sections">
    <?php
    if (!empty($section))
    {
        ?>
        <header class="section_header">
            <div class="h2 "><?= $section['NAME'] ?></div>
        </header>
        <?php
    }
    ?>

    <div class="row row-grid team-list">
        <?foreach($arResult["ITEMS"] ?: [] as $arItem) {
	        $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
	        $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
        	?>
            <div class="col-lg-4 col-md-6" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
                <?$APPLICATION->IncludeComponent(
                    "citrus:template",
                    "staff-item",
                    array(
                        'ITEM' => $arItem,
                    ),
                    $component,
                    array("HIDE_ICONS" => "Y")
                );?>
            </div>
        <?php } ?>

    </div>
</div>

<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
	<div class="b-staff-list-pager b-staff-list-pager-bottom">
		<?=$arResult["NAV_STRING"]?>
	</div>
<?endif;?>
