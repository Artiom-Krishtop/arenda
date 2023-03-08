<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

/** @var CBCitrusIBAddFormComponent $component Текущий вызванный компонент */
/** @var CBitrixComponentTemplate $this Текущий шаблон (объект, описывающий шаблон) */
/** @var array $arResult Массив результатов работы компонента */
/** @var array $arParams Массив входящих параметров компонента, может использоваться для учета заданных параметров при выводе шаблона (например, отображении детальных изображений или ссылок). */

use Bitrix\Main\Localization\Loc;

/**
 * @var $templateFolder
 */

$this->createFrame()->begin("Loading...");

$arParams['SUBMIT_TEXT'] = $arParams['SUBMIT_TEXT'] ? $arParams['SUBMIT_TEXT'] : Loc::getMessage("IBLOCK_FORM_SUBMIT_BTN");
$arResult["SUCCESS_MESSAGE"] = $arParams['SUCCESS_TEXT'] ? $arParams['SUCCESS_TEXT'] : Loc::getMessage("TPL_URF_OK_MESSAGE");
$depthLevel = false;
?>
<form
    id="<?=$arResult["FORM_ID"]?>"
    name="<?=$arResult["FORM_ID"]?>"
    action="<?=htmlspecialcharsback($arResult["FORM_ACTIONS"])?>"
    data-ajax-action="<?=$componentPath?>/ajax.php"
    method="post" enctype="multipart/form-data"
    autocomplete="off"
    class="citrus-form citrus-form__style-<?=strtolower($arParams["FORM_STYLE"])?>"
>
	<?=bitrix_sessid_post();?>
	<input type="hidden" value="<?=$arResult["FORM_ID"]?>" name="FORM_ID"/>
	<input type="hidden" value="<?=($this->__component->getName())?>" name="component"/>
	
	<input type="hidden" name="FORM_PREVIEW_IMAGE_FILE" class="js-citrus-smartform-preview-image-file">

    <?if($arParams["HIDDEN_ANTI_SPAM"] !== "N"):?>
        <input type="hidden" name="GIFT" value="Y">
    <?endif;?>

	<? if ($arParams['FORM_TITLE']): ?>
		<div class="h3 citrus-form-title">
			<?=htmlspecialchars_decode($arParams['FORM_TITLE'])?>
		</div>
	<? endif; ?>

	<? if ($arParams['SUB_TEXT']): ?>
		<div class="citrus-form-description">
			<div class="citrus-form-description__text">
				<?=htmlspecialchars_decode($arParams['SUB_TEXT'])?>
			</div>
		</div>
	<? endif; ?>

	<?	//MESSAGES
	$isMessage = $arResult["ERRORS"] || !empty($arResult['SUCCESS_RESULT']); ?>
	<div class="citrus-form__message-block <?=!$isMessage ? 'hidden': ''?>" cui-form="message-block">
	<?if ($isMessage):?>
			<? if (count($arResult["ERRORS"])):?>
				<div class="message-block bg-danger">
					<?if($arResult["ERRORS"]):?>
                        <div class="message-block-icon"></div>
                        <div class="message-block-txt">
                            <?foreach ( $arResult["ERRORS"] as $key => $error):?>
                                <p><?=$error?></p>
                            <?endforeach;?>
                        </div>
					<?endif;?>
				</div>
			<? endif; ?>

			<? if ($arResult['SUCCESS_MESSAGE']):?>
				<div class="message-block bg-success">
                    <div class="message-block-icon"></div>
                    <div class="message-block-icon-txt">
					    <p><?=$arResult['SUCCESS_MESSAGE']?></p>
                    </div>
				</div>
			<? endif; ?>
	<? endif; ?>
	</div><!-- .form-message-block -->

	<div class="citrus-form__fields">
	<?
    $i = 0;
	foreach ($arResult["ITEMS"] as $code => &$fieldInfo):?>
		<?
		/**
		 * Группы полей
		 */
		if ($fieldInfo['GROUP_FIELD'] === "Y" && $fieldInfo['DEPTH_LAVEL'] !== false) {
			if (false !== $depthLevel && $depthLevel >= $fieldInfo['DEPTH_LAVEL'])
				echo str_repeat('</div>', $depthLevel - ($fieldInfo['DEPTH_LAVEL'] - 1));
			echo "<div class='{$fieldInfo["CLASS"]} field-group dept_{$fieldInfo['DEPTH_LAVEL']}'>";
			$depthLevel = $fieldInfo['DEPTH_LAVEL'];
			continue;
		}
		?>
		<? //Скрытые поля
		if (isset($fieldInfo['HIDE_FIELD']) && $fieldInfo['HIDE_FIELD'] == "Y"):?>
			<input type="hidden" name="<?=$fieldInfo["CODE"]?>" value="<?=$fieldInfo['OLD_VALUE']?>"/>
			<? continue; ?>
		<?endif; ?>

		<?
		$material_switch_enable = (in_array(strtolower($fieldInfo['TEMPLATE']['TYPE']), array("text", "html", "date", "number"))) && !$fieldInfo['PLACEHOLDER'];
		$isTitleActive = !$material_switch_enable || $fieldInfo["OLD_VALUE"] || $fieldInfo['PLACEHOLDER'];

		$inputGroupClasses = array('form-group');
		if ( $material_switch_enable ) $inputGroupClasses[] = 'js_material_switch_container';
		if ($fieldInfo['FIRST_GROUP_FIELD']) $inputGroupClasses[] = 'first-group-field';

		if ($fieldInfo["CLASS"]) $inputGroupClasses = array_merge($inputGroupClasses, explode(" ", $fieldInfo["CLASS"]))
		?>
		<div class="<?=implode($inputGroupClasses, " ")?>"
		     data-field-code="<?=strtolower($code)?>"
		     data-field-type="<?=strtolower($fieldInfo['TYPE'])?>"
		     data-field-template="<?=strtolower($fieldInfo['TEMPLATE_ID'])?>"
		>

			<?
			$inputNum = 1;
			if ($fieldInfo['MULTIPLE'] == "Y" && $fieldInfo['TYPE'] != 'L' && $fieldInfo['TYPE'] != 'E' && $fieldInfo['TYPE'] != 'G') {
				$inputNum += $fieldInfo["MULTIPLE_CNT"];
			}
			if (strlen($fieldInfo['TITLE']) > 0):?>
				<div class="field-title <? if ($isTitleActive):?>_active<?endif; ?>">
					<?=$fieldInfo['TITLE']?><? if ('Y' == $fieldInfo['IS_REQUIRED']):?><span class="starrequired">*</span><?endif; ?>
				</div>
			<?endif; ?>

			<div class="input-container">
                <?if($fieldInfo["TEMPLATE"]):?>
                    <?$component->includeFieldTemplate($fieldInfo);?>
                <?else:?>
                    <p><?= Loc::getMessage("CITRUS_SMARTFORM_MISSING_FIELD_TEMPLATE") ?></p>
                    <pre>
TYPE: <?=$fieldInfo["TYPE"]?> <br>USER_TYPE: <?=$fieldInfo["USER_TYPE"]?>
                    </pre>
                <?endif;?>


				<? if ($arParams["JQUERY_VALID"] == "Y"):?>
					<div class="error help-block"></div>
				<?endif; ?>
			</div>
			<!-- /.input-container -->


			<? if (strlen($fieldInfo['TOOLTIP']) > 0):?>
				<p class="field-description"><?=$fieldInfo['TOOLTIP']?></p>
			<?endif; ?>

		</div><!-- .form-group -->
		<?
		$i++;
	endforeach;
	//закрываем группы
	if (false !== $depthLevel)
		echo str_repeat('</div>', $depthLevel);
	?>
	</div><!-- .citrus-form__fields -->

    <div class="citrus-form__footer">
        <div class="form-group required-message-block">
            <span class="starrequired">*</span>
            <span><?=Loc::getMessage('REQUIRED_MESSAGE_LABLE')?></span>
        </div>
        <? //agreement
        if  ($arParams["AGREEMENT_LINK"]):
            ?>
            <div class="form-group agree-block">
                <div class="cui-checkbox-group checkbox-count-1">
                    <label class="cui-checkbox__label">
                        <input type="checkbox" data-valid="required" data-valid-params='{"important":1}' name="AGREEMENT" class="cui-checkbox__input">
                        <span class="cui-checkbox__checkmark"></span>
                        <span class="cui-checkbox__label-text"><?=Loc::getMessage("CITRUS_FORM_AGREEMENT_MESSAGE", array("#LINK#" => $arParams["AGREEMENT_LINK"]))?></span>
                        <?if(Loc::getMessage("CITRUS_FORM_AGREEMENT_MESSAGE_DESCRIPTION")):?>
                            <span class="agree-description"><?=Loc::getMessage("CITRUS_FORM_AGREEMENT_MESSAGE_DESCRIPTION")?></span>
                        <?endif;?>
                    </label>
                </div>
            </div>
        <?endif;?>

        <div class="form-group form-group-btn">
            <div class="input-container button-position-<?=strtolower($arParams["BUTTON_POSITION"])?>">
                <button class="btn btn-primary feedback-btn" name="iblock_submit" ><span class="btn-label"><?=$arParams['SUBMIT_TEXT']?></span></button>
            </div>
        </div>
        <? if (strlen($arParams['ADDITIONAL_TEXT']) > 0): ?>
            <div class="b-additional-text"><?=htmlspecialchars_decode($arParams['ADDITIONAL_TEXT'])?></div>
        <? endif; ?>
    </div><!-- .citrus-form__footer -->
</form>

<script>
    ;(function () {
        new citrusForm(
            "<?=$arResult["FORM_ID"]?>",
            <?=\Bitrix\Main\Web\Json::encode($arResult["ITEMS"]);?>,
            <?=\Bitrix\Main\Web\Json::encode(array(
	            "JQUERY_VALID" => (bool) ($arParams["JQUERY_VALID"] == "Y"),
	            "AJAX" => (bool) ($arParams['AJAX'] == 'Y'),
				"HIDDEN_ANTI_SPAM" => (bool) ($arParams['HIDDEN_ANTI_SPAM'] !== "N"),
				"HIDE_INPUTS_ON_SUCCESS" => (bool) ($arParams['HIDE_INPUTS_ON_SUCCESS'] === "Y")
	        ))?>
        );
    })();
</script>