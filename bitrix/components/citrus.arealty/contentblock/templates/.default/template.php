<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$this->setFrameMode(true);

$this->AddEditAction($arResult['ID'], $arResult['EDIT_LINK'], \CIBlock::GetArrayByID($arResult['IBLOCK_ID'], 'ELEMENT_EDIT'));

?>
<div id="<?=$this->GetEditAreaId($arResult['ID'])?>">
	<?php
	if (!$component->getParent() instanceof \Citrus\Core\IncludeComponent)
	{
		?><div class="h1"><?=$arResult['TITLE']?></div><?php

		if ($arResult['SUBTITLE'])
		{
			?><div class="section-description"><?=$arResult['SUBTITLE']?></div><?
		}
	}

	?><?=$arResult['PREVIEW_TEXT']?>
</div>
