<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$frame = $this->createFrame()->begin("");?>
<a href="<?=$arParams['PATH']?>" class="realty-favourites hide"><?=GetMessage("CITRUS_REALTY_FAV_TEXT")?> (<?=intval($arResult["COUNT"])?>)</a>
<script>
window.citrusRealtyFav = <?=\Bitrix\Main\Web\Json::encode(array_keys($arResult["LIST"]))?>;
</script>