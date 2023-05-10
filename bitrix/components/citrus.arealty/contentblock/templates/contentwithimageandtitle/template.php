<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$this->setFrameMode(true);

$this->AddEditAction($arResult['ID'], $arResult['EDIT_LINK'], \CIBlock::GetArrayByID($arResult['IBLOCK_ID'], 'ELEMENT_EDIT'));

?>
<div class="section__content_img">
	<img src="<?= $arResult['DETAIL_PICTURE']['SRC'] ?>" alt="" width="400"/>
</div>
<div class="section__content_text" id="<?=$this->GetEditAreaId($arResult['ID'])?>">
	<div class="title"><?= $arResult['NAME'] ?></div>
	<?= $arResult['DETAIL_TEXT'] ?>
	<?php if (!empty($arParams['BTN_TITLE'])) { ?>
		<div class="btn_block">
			<a href="<?= $arResult['DETAIL_PAGE_URL'] ?>" class="btn btn-secondary print-hidden"><?= $arParams['BTN_TITLE'] ?></a>
		</div>
	<?php } ?>
</div>
