<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);?>

<form action="<?=$arResult["FORM_ACTION"]?>" autocomplete="off" class="mobile-search">
	<input name="q" type="text" class="mobile-search__input" placeholder="<?=GetMessage("SEARCH_PLACEHOLDER");?>" maxlength="50">
    <button type="submit" class="mobile-search__button" name="s">
	    <i class="icon-search"></i>
    </button>
</form>