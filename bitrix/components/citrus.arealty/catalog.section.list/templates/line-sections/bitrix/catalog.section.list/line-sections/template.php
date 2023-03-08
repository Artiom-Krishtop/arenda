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

if (empty($arResult["SECTIONS"]))
{
    return;
}

use Bitrix\Main\Localization\Loc;
?>

<div class="line-sections<?=($arParams['ALIGN_LEFT'] === 'Y' ? ' line-sections--align-left' : '')?>">
	<?
	foreach($arResult["SECTIONS"] as $arSection)
	{
		$this->AddEditAction($arSection['ID'], $arSection['EDIT_LINK'], CIBlock::GetArrayByID($arSection["IBLOCK_ID"], "SECTION_EDIT"));
		$this->AddDeleteAction($arSection['ID'], $arSection['DELETE_LINK'], CIBlock::GetArrayByID($arSection["IBLOCK_ID"], "SECTION_DELETE"), array("CONFIRM" => GetMessage('CT_BCSL_ELEMENT_DELETE_CONFIRM')));
		$isSelected = $arParams['ACTIVE_SECTION'] === $arSection['CODE'];
		?>

		<a href="<?=$arSection['SECTION_PAGE_URL']?>"
		   class="line-sections__item <?=$isSelected ? '_selected':''?>"
		   id="<?=$this->GetEditAreaId($arSection['ID']);?>">
			<div class="line-sections__item-icon-w"
			     style="color: <?=$arSection['UF_SECTION_COLOR'] ?: 'inherit'?>;">
				<img class="line-sections__item-icon"
                     src="<?=\Citrus\Core\array_get($arSection, 'PICTURE.SRC', $templateFolder . '/default-icon.png')?>"
				     alt="">
			</div>
			<div class="line-sections__item-content">
				<div class="line-sections__item-name">
					<?=$arSection['NAME']?>
				</div>
				<?if($arSection['ELEMENT_CNT']):?>
					<div class="line-sections__item-count">
						<?=(int)$arSection['ELEMENT_CNT']?> <?= \Citrus\Core\plural($arSection['ELEMENT_CNT'], explode('|', Loc::getMessage("LINE_SECTIONS_OBJECTS"))) ?>
					</div>
				<?endif;?>
			</div>
		</a>
	<?
	} ?>
</div>
<script>
	;(function(){
	  $('.line-sections__item-name').responsiveEqualHeightGrid();
	}());
</script>
