<?php

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$this->setFrameMode(true);

$modal = strlen($arResult['PROPERTIES']['btn_form']['VALUE_XML_ID']);
$link =  $modal
    ? SITE_DIR . $arResult['PROPERTIES']['btn_form']['VALUE_XML_ID']
    : $arResult['PROPERTIES']['btn_link']['VALUE'];

/** @var $this CBitrixComponentTemplate */
if ($this->__component->getParent())
{
	$this->AddEditAction($arResult['ID'], $arResult['PANEL']['EDIT_LINK'], CIBlock::GetArrayByID($arResult["IBLOCK_ID"], "ELEMENT_EDIT"));
}

?>
<div class="callout print-hidden" id="<?=$this->GetEditAreaId($arResult["ID"])?>">
	<div class="c-side">
		<div class="row-ib row-grid">
			<div class="col-xs-12 col-lg-8 col-dt-9 vam tac tal-lg tal-dt">
				<div class="callout-title tc-white">
					<?=$arResult['NAME']?>
				</div>
				<div class="callout-text tc-white">
					<?=$arResult['PREVIEW_TEXT']?>
				</div>
			</div>
			<div class="col-xs-12 col-lg-4 col-dt-3 vam tac">
				<a class="btn btn-primary btn-big" rel="nofollow" href="<?=$link?>"<?=($modal ? ' data-toggle="modal"' : '')?>>
					<span class="btn-label"><?=$arResult['PROPERTIES']['btn_text']['VALUE']?></span>
				</a>
			</div>
		</div>
	</div>
</div>

