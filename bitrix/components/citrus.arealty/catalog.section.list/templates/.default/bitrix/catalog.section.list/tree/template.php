<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
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

?>

<div class="catalog-sections row">
	<? foreach($arResult["SECTIONS_TREE"] as $arSection):
		$this->AddEditAction($arSection['ID'], $arSection['EDIT_LINK'], CIBlock::GetArrayByID($arSection["IBLOCK_ID"], "SECTION_EDIT"));
		$this->AddDeleteAction($arSection['ID'], $arSection['DELETE_LINK'], CIBlock::GetArrayByID($arSection["IBLOCK_ID"], "SECTION_DELETE"), array("CONFIRM" => GetMessage('CT_BCSL_ELEMENT_DELETE_CONFIRM')));?>

		<div class="catalog-section col-dt-4 col-md-6 col-xs-12" id="<?=$this->GetEditAreaId($arSection['ID']);?>">
			<div class="catalog-section__img-w"
			     style="background-color: <?=$arSection['UF_SECTION_COLOR']?>;">
				<img src="<?=\Citrus\Core\array_get($arSection, 'PICTURE.SRC', $templateFolder . '/default-icon.png')?>" alt="">
			</div>
			<div class="catalog-section__content">
				<a class="catalog-section__name" href="<?=$arSection['SECTION_PAGE_URL']?>"><?=$arSection['NAME']?></a>

				<?if(!count($arSection['SUBSECTIONS'])):?>
				    <div class="catalog-section__count"><?=$arSection['ELEMENT_CNT']?> <?= \Citrus\Core\plural($arSection['ELEMENT_CNT'], explode('|', Loc::getMessage("SECTIONS_TREE_OBJECTS"))) ?></div>
				<?else:?>
					<div class="catalog-section__subsections">
						<?foreach ( $arSection['SUBSECTIONS'] ?: [] as $subsection):
							$this->AddEditAction($subsection['ID'], $subsection['EDIT_LINK'], CIBlock::GetArrayByID($subsection["IBLOCK_ID"], "SECTION_EDIT"));
							$this->AddDeleteAction($subsection['ID'], $subsection['DELETE_LINK'], CIBlock::GetArrayByID($subsection["IBLOCK_ID"], "SECTION_DELETE"), array("CONFIRM" => GetMessage('CT_BCSL_ELEMENT_DELETE_CONFIRM')));?>

							<div class="catalog-section__subsection"
							     id="<?=$this->GetEditAreaId($subsection['ID']);?>">
								<a class="catalog-section__subsection-link"
								   href="<?=$subsection['SECTION_PAGE_URL']?>">
									<?=$subsection['NAME']?></a>

								<?if($subsection['ELEMENT_CNT']):?>
									<span class="catalog-section__subsection-counter">
									<?=$subsection['ELEMENT_CNT']?> <?= \Citrus\Core\plural($subsection['ELEMENT_CNT'], explode('|', Loc::getMessage("SECTIONS_TREE_OBJECTS"))) ?>
								</span>
								<?endif;?>
							</div><!-- .catalog-section__subsection -->

						<?endforeach;?>
					</div><!-- .catalog-section__subsections -->
				<?endif;?>

			</div><!-- catalog-section__content -->
		</div><!-- .catalog-section -->

	<?endforeach;?>
</div>