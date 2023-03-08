<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
//***********************************
//status and unsubscription/activation section
//***********************************
?>

<form action="<?=$arResult["FORM_ACTION"]?>" method="get">
<?if($arResult["SUBSCRIPTION"]["ACTIVE"] == "Y"):?>
	<input class="button inline" type="submit" name="unsubscribe" value="<?echo GetMessage("subscr_unsubscr")?>" />
	<input type="hidden" name="action" value="unsubscribe" />
<?else:?>
	<input class="button inline" type="submit" name="activate" value="<?echo GetMessage("subscr_activate")?>" />
	<input type="hidden" name="action" value="activate" />
<?endif;?>
<input type="hidden" name="ID" value="<?echo $arResult["SUBSCRIPTION"]["ID"];?>" />
<?echo bitrix_sessid_post();?>
</form>
<br />