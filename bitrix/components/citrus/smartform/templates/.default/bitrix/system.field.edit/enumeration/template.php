<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/**
 * Bitrix Framework
 * @package bitrix
 * @subpackage main
 * @copyright 2001-2013 Bitrix
 */

/**
 * Bitrix vars
 *
 * @var array $arParams
 * @var array $arResult
 */

//echo "<pre>";print_r($arParams["arUserField"]);echo "</pre>";
//echo "<pre>";print_r($arResult);echo "</pre>";

$arFieldList = array();
foreach ($arParams["arUserField"]["USER_TYPE"]["FIELDS"] as $key => $val) {
	$key = IntVal($key);
	$arFieldList[$key] = $val;
}

if($arParams["arUserField"]["IS_REQUIRED"] && array_key_exists(0,$arFieldList)) {
	unset($arFieldList[0]);
}

$bWasSelect = false;
?><input type="hidden" name="<?=$arParams["arUserField"]["FIELD_NAME"]?>" value=""><?
if ($arParams["arUserField"]["SETTINGS"]["DISPLAY"] == "CHECKBOX")
{
	if(empty($arResult["VALUE"]) && array_key_exists(0,$arFieldList))
		$arResult["VALUE"] = array("0");

	foreach ($arFieldList as $key => $val)
	{
		$bSelected = in_array($key, $arResult["VALUE"]) && (
				(!$bWasSelect) ||
				($arParams["arUserField"]["MULTIPLE"] == "Y")
			);
		$bWasSelect = $bWasSelect || $bSelected;

		?><?if($arParams["arUserField"]["MULTIPLE"]=="Y"):?>
		<label><input
					type="checkbox"
					value="<?echo $key?>"
					name="<?echo $arParams["arUserField"]["FIELD_NAME"]?>"
				<?echo ($bSelected? "checked" : "")?>
			><?=$val?></label><br />
	<?else:?>
		<label><input
					type="radio"
					value="<?echo $key?>"
					name="<?echo $arParams["arUserField"]["FIELD_NAME"]?>"
				<?echo ($bSelected? "checked" : "")?>
			><?=$val?></label><br />
	<?endif;?><?
	}
}
else
{
	if(empty($arResult["VALUE"]) && array_key_exists(0,$arFieldList))
		$arResult["VALUE"] = array("0");
	?><select
	class="bx-user-field-enum"
	name="<?=$arParams["arUserField"]["FIELD_NAME"]?>"
	<?if($arParams["arUserField"]["SETTINGS"]["LIST_HEIGHT"] > 1):?>
	size="<?=$arParams["arUserField"]["SETTINGS"]["LIST_HEIGHT"]?>"
<?endif;?>
	<?if ($arParams["arUserField"]["MULTIPLE"]=="Y"):?>
	multiple="multiple"
<?endif;?>
	>
	<?
	foreach ($arFieldList as $key => $val)
	{
		$bSelected = in_array(strval($key), $arResult["VALUE"], true) && (
				(!$bWasSelect) ||
				($arParams["arUserField"]["MULTIPLE"] == "Y")
			);
		$bWasSelect = $bWasSelect || $bSelected;

		?><option value="<?echo $key?>"<?echo ($bSelected? " selected" : "")?>><?echo $val?></option><?
	}
	?></select><?
}
