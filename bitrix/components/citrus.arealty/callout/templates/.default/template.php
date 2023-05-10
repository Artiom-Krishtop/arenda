<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

$this->setFrameMode(true);

$modal = strlen($arResult['PROPERTIES']['btn_form']['VALUE_XML_ID']);
$link = $modal
	? SITE_DIR . $arResult['PROPERTIES']['btn_form']['VALUE_XML_ID']
	: $arResult['PROPERTIES']['btn_link']['VALUE'];

$this->AddEditAction($arResult['ID'], $arResult['EDIT_LINK'], CIBlock::GetArrayByID($arResult["IBLOCK_ID"], "ELEMENT_EDIT"));
?>

<section
	class="section section-color-site print-hidden theme--bg-color"
	id="<?=$this->GetEditAreaId($arResult['ID']);?>">
	<div class="w">
		<div class="footer-snap-point">
			<div class="footer-snap-point__text">
				<div class="footer-snap-point__text-1">
					<?=$arResult['NAME']?>
				</div>
				
				<?if($arResult['PREVIEW_TEXT']):?>
					<div class="footer-snap-point__text-2">
						<?=$arResult['PREVIEW_TEXT']?>
					</div>
				<?endif;?>
			</div>
			<div class="footer-snap-point__btn">
				<?//$modal?>
				<a href="<?=$link?>" data-toggle="modal" class="btn btn-big btn-transparent btn-stretch"><?=$arResult['PROPERTIES']['btn_text']['VALUE']?></a>
			</div>
		</div>
	</div>
</section>