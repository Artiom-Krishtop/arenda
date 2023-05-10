<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Main\Localization\Loc;

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

Loc::loadMessages(__FILE__);

$this->setFrameMode(true);

?>
<div class="service">
	<div class="row row-grid">
	<?foreach($arResult["SECTIONS"] as $arSection):?>
		<?$this->AddEditAction($arSection['ID'], $arSection['EDIT_LINK'], CIBlock::GetArrayByID($arSection["IBLOCK_ID"], "SECTION_EDIT"));
		$this->AddDeleteAction($arSection['ID'], $arSection['DELETE_LINK'], CIBlock::GetArrayByID($arSection["IBLOCK_ID"], "SECTION_DELETE"), array("CONFIRM" => GetMessage('CT_BCSL_ELEMENT_DELETE_CONFIRM')));?>
		<div class="col-lg-4">
            <div class="service-section <?=($arParams["LINK_SHOW_ALL"]) ? 'btn_show_all' : ''?>" id="<?=$this->GetEditAreaId($arSection['ID']);?>">
                <div class="service-section__image-w default-picture-background">
                    <span class="service-section__image"
                          style="background-image: url('<?=$arSection["PICTURE"]["SRC"]?>');"></span>
                </div>
                <div class="service-section__body">
                    <div class="service-section__title">
			            <?=$arSection["NAME"]?>
                    </div>
                    <div class="service__item-list">
			            <?
			            if (is_array($arSection["ITEMS"]))
			            {
				            foreach ($arSection["ITEMS"] as $arItem):?>
                                <a
                                        class="service__item"
                                        href="<?=$arItem["DETAIL_PAGE_URL"]?>">
						            <?=$arItem["NAME"]?>
                                </a>
				            <?endforeach;
			            }
			            ?>
                    </div>
                    <?
                    if ($arParams["LINK_SHOW_ALL"]):
                        ?>
                        <div class="service_link_more">
                            <a href="<?=$arSection["SECTION_PAGE_URL"]?>" class="service__footer">
                                <span><?= Loc::getMessage("CITRUS_AREALTY_SERVICES_SECTION_PAGE_LINK") ?></span>
                                <span class="icon-arrow-right"></span>
                            </a>
                        </div>
                        <?
                    endif;
                    ?>
                </div>
            </div><!-- .service-item -->
		</div><!-- .col -->
	<?endforeach;?>
	</div><!-- .row -->
	<? if ($arParams['SHOW_IBLOCK_DESRIPTION']): ?>
        <div class="description_block">
			<?=$arResult["DESCRIPTION"]?>
        </div>
	<? endif; ?>
</div><!-- .service-list -->