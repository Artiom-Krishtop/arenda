<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$frame = $this->createFrame()->begin("");?>
<a href="<?=$arParams['PATH']?>" class="realty-favourites hidden"><?=GetMessage("CITRUS_REALTY_FAV_TEXT")?> (<?=intval($arResult["COUNT"])?>)</a>
<script>
window.citrusRealtyFav = <?=\Bitrix\Main\Web\Json::encode(array_keys($arResult["LIST"]))?>;
$(function () {
	if ("object" === typeof(window.citrusRealtyFav)) {
		$('.add2favourites[data-id]').each(function() {
			var $this = $(this),
				id = $this.data('id');
			if (window.citrusRealtyFav.indexOf(id) != -1)
				window.citrusRealtyMark($this, 'add');
		});
	}
});
</script>
