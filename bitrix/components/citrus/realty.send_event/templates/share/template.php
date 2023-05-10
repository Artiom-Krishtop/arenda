<?

use Bitrix\Main\Localization\Loc;

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

?>
<div class="form_order_detail">
	<div class="call-header"><?=Loc::getMessage("CITRUS_REALTY_SEND_TO_EMAIL_TITLE")?></div>
<form id="<?=$arResult["FORM_ID"]?>" name="<?=$arResult["FORM_ID"]?>" action="<?=POST_FORM_ACTION_URI?>" method="post" enctype="multipart/form-data" class="call-form">

<?if (!empty($arResult["ERRORS"])):?>
	<div class="b-form-error-wrapp">
		<?if(strlen($arParams["ERROR_LIST_MESSAGE"]) > 0):?>
			<div class="b-form-error-notice"><?ShowNote($arParams["ERROR_LIST_MESSAGE"]);?></div>
		<?endif;?>
		<?ShowError($arResult["ERRORS"]);?>
	</div>
<?endif;?>

<?if (!empty($arResult["MESSAGE"])):?>
	<div class="b-form-success-wrapp"><?ShowNote($arResult["MESSAGE"]);?></div>
<?endif;?>
<?

echo bitrix_sessid_post();
echo '<input type="hidden" name="cse_hash" value="' . $arResult['FORM_HASH'] . '">';

foreach ($arResult["ITEMS"] as $code => $fieldInfo):

	?><div class="field cse-<?=strtolower($code)?>"><?

	$name = "FIELD[" . $code . "]";?>

	<label for="<?=$code?>" class="field-label"><?
	echo $fieldInfo['NAME'] . ':';
	if ($fieldInfo['IS_REQUIRED'])
	{
		?><span class="starrequired mark">*</span><?
	}
	?></label>

	<div class="field-input"><?
	
		$value = $arResult['OLD_VALUE'][$code];

		if ($code == '__CAPTCHA__'):?>
			<input type="hidden" name="captcha_sid" value="<?=$arResult["CAPTCHA_CODE"]?>" />
			<img src="/bitrix/tools/captcha.php?captcha_sid=<?=$arResult["CAPTCHA_CODE"]?>" width="180" height="40" alt="CAPTCHA" class="cse-captcha-image" align="left"/>
			<input type="text" name="captcha_word" maxlength="50" value="" class="cse-captcha-input"><?
		elseif ($fieldInfo['IS_EMAIL']):?>
			<input type="text" name="<?=$name?>" size="25" value="<?=$value?>" id="<?=$code?>"<?=(strlen($fieldInfo['TOOLTIP']) ? ' placeholder="' . $fieldInfo['TOOLTIP'] . '"' : '')?>><?
		else:?>
			<textarea cols="25" rows="5" name="<?=$name?>" id="<?=$code?>"><?=strlen($value) ? $value : $_GET["from"]?></textarea><?

			if (strlen($fieldInfo['TOOLTIP']))
			{

				?><small class="ciee-field-tooltip"><?=$fieldInfo['TOOLTIP']?></small><?
			}

		endif;

		?></div><?
	
	?></div><?

endforeach;
?>
    <div class="fz152">
        <label class="field-label">&nbsp;</label>
        <div class="field-input">
        <div class="field-checks">
            <input class="f-line-input-val" type="checkbox" value="Y" id="<?=$arResult["FORM_ID"]?>_fz152" name="fz152">
            <label class="pull-left lh-0 gray-6t" for="<?=$arResult["FORM_ID"]?>_fz152"><?=GetMessage('CITRUS_SUBSCRIBE_FZ152_CHECKBOX', array('#URL#' => '/bitrix/components/citrus/iblock.element.form/agreement.php?site=' . SITE_ID))?></label>
        </div>
        </div>
    </div>

    <div class="field">
		<div class="tooltip-block">
			<span class="required-fields mark">*</span>
			<span><?=GetMessage('CITRUS_REALTY_REQUIRED_MESSAGE_LABEL')?></span>
		</div>
	</div>
	<div class="field">
		<button type="submit" name="send_submit" value="<?=GetMessage('CITRUS_REALTY_SEND')?>" class="md-button"><?=GetMessage('CITRUS_REALTY_SEND')?></button>
	</div>
</form></div>