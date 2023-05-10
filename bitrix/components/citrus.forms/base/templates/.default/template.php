<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

/**
 * @var $templateFolder
 */

$this->createFrame()->begin("Loading...");

$arParams['BUTTON_TITLE'] = $arParams['BUTTON_TITLE'] ? $arParams['BUTTON_TITLE'] : Loc::getMessage("IBLOCK_FORM_SUBMIT_BTN");
$arResult["SUCCESS_MESSAGE"] = $arParams['SUCCESS_TEXT'] ? $arParams['SUCCESS_TEXT'] : Loc::getMessage("TPL_URF_OK_MESSAGE");
?>
<script>
	BX.loadCSS('<?=$templateFolder?>/assets/style.css');
</script>
<? if ('POPUP' == $arParams['FORM_PLACE_MODE']): ?>
	<a
		class="btn popup-with-form"
		id="call_form_btn_<?= $arResult["FORM_ID"] ?>"
		href="#ajax_form_<?= $arResult["FORM_ID"] ?>"
	><?= $arParams['~FORM_TITLE'] ?></a>
<? endif; ?>

<div id="ajax_form_<?= $arResult["FORM_ID"] ?>" class="<?
echo $arParams['FORM_CLASS'];

if ('POPUP' == $arParams['FORM_PLACE_MODE'])
	echo " mfp-hide white-popup-block";
?>">

	<? if ($arParams['FORM_TITLE']): ?>
		<div class="h2"><?=$arParams['~FORM_TITLE']?></div>
	<? endif; ?>

	<form
		id="<?= $arResult["FORM_ID"] ?>"
		name="<?= $arResult["FORM_ID"] ?>"
		action="<?= htmlspecialcharsback($arResult["FORM_ACTIONS"]) ?>"
		data-ajax-action="<?= $componentPath ?>/ajax.php"
		method="post" enctype="multipart/form-data"
		autocomplete="off"
		class="citrus-form citrus-form__style-<?= strtolower($arParams["FORM_STYLE"]) ?>"
	>
		<?= bitrix_sessid_post(); ?>
		<input type="hidden" value="<?=$arResult["FORM_ID"] ?>" name="FORM_ID"/>
		<input type="hidden" value="<?=$arResult['COMPONENT_NAME']?>" name="component"/>

		<? if ($arParams["HIDDEN_ANTI_SPAM"] !== "N"): ?>
			<input type="hidden" name="GIFT" value="Y">
		<? endif; ?>

		<? if ($arParams['BEFORE_FORM_TOOLTIP']): ?>
			<div class="citrus-form-description">
				<div class="citrus-form-description__text">
					<?= $arParams['~BEFORE_FORM_TOOLTIP'] ?>
				</div>
			</div>
		<? endif; ?>
	
		<? //MESSAGES
		$isMessage = $arResult["ERRORS"] || !empty($arResult['SUCCESS_RESULT']); ?>
		<div class="citrus-form__message-block <?= !$isMessage ? 'hidden' : '' ?>" data-citrus-form="message-block">
			<? if ($isMessage): ?>
				<? if (count($arResult["ERRORS"])): ?>
					<div class="message-block _error">
						<? if ($arResult["ERRORS"]): ?>
							<div class="message-block-icon"></div>
							<div class="message-block-txt">
								<? foreach ($arResult["ERRORS"] as $key => $error): ?>
									<p><?= $error ?></p>
								<? endforeach; ?>
							</div>
						<? endif; ?>
					</div>
				<? endif; ?>
				
				<? if ($arResult['SUCCESS_RESULT']): ?>
					<div class="message-block _success">
						<div class="message-block-icon"></div>
						<div class="message-block-icon-txt">
							<p><?= $arResult['SUCCESS_MESSAGE'] ?></p>
						</div>
					</div>
				<? endif; ?>
			<? endif; ?>
		</div><!-- .form-message-block -->

		<div class="citrus-form__fields">
			<?
			$depthLevel = false;
			$combineGroup = [];
			foreach ($arResult["ITEMS"] as $code => &$fieldInfo):?>
				<?
				/**
				 * Группы полей
				 */
				if ($fieldInfo['GROUP_FIELD'] === "Y" && $fieldInfo['DEPTH_LAVEL'] !== false) {

				    if ($combineGroup['DEPTH_LAVEL'] && $combineGroup['DEPTH_LAVEL'] >= $fieldInfo['DEPTH_LAVEL'])
					    $combineGroup = [];

					if (false !== $depthLevel && $depthLevel >= $fieldInfo['DEPTH_LAVEL'])
						echo str_repeat('</div>', $depthLevel - ($fieldInfo['DEPTH_LAVEL'] - 1));

						echo "<div class='{$fieldInfo["CLASS"]} field-group dept_{$fieldInfo['DEPTH_LAVEL']}'>";

                    //Группы полей в стрроку
                    if (trim($fieldInfo['CLASS']) === 'combined-fields') {
                        $combineGroup = ['DEPTH_LAVEL' => $fieldInfo['DEPTH_LAVEL'], 'COUNTER' => 0];

                        if ($fieldInfo['TITLE']):
                            $combineGroupTitle = explode('|', $fieldInfo['TITLE']);
                            ?>
                            <div class="combined-fields__title">
                                <div class="combined-fields__title-main"><?=$combineGroupTitle[0]?></div>
                                <div class="combined-fields__title-description"><?=$combineGroupTitle[1]?></div>
                            </div>
                        <?endif;?>
                    <?
                    } elseif(strlen($fieldInfo['TITLE'])) {
                        echo "<div class=\"field-group--title\">" . $fieldInfo['TITLE'] . "</div>";
                    }
					$depthLevel = $fieldInfo['DEPTH_LAVEL'];
					continue;
				}
				?>
				<? //Скрытые поля
				if (isset($fieldInfo['HIDE_FIELD']) && $fieldInfo['HIDE_FIELD'] == "Y"):?>
					<input type="hidden" name="<?= $fieldInfo["CODE"] ?>" value="<?= $fieldInfo['OLD_VALUE'] ?>"/>
					<? continue; ?>
				<? endif; ?>
                            
                <?if($combineGroup['DEPTH_LAVEL'] && $combineGroup['COUNTER']++):?>
                    <div class="combined-fields-separate">-</div>
                <?endif;?>
				<?
				$baseType = $this->__component->getFieldType($fieldInfo);
				$template = $this->__component->getTemplateType($baseType);
				
				$material_switch_enable = (in_array($template, array("text", "html", "date", "number"))) && !$fieldInfo['PLACEHOLDER'];
				$isTitleActive = !$material_switch_enable || $fieldInfo["OLD_VALUE"] || $fieldInfo['PLACEHOLDER'];

				$inputGroupClasses = array('form-group');
				if ($material_switch_enable) $inputGroupClasses[] = 'js_material_switch_container';
				if ($combineGroup['DEPTH_LAVEL']) $inputGroupClasses[] = 'combined-fields-item';
				if ($fieldInfo['FIRST_GROUP_FIELD']) $inputGroupClasses[] = 'first-group-field';
				
				if ($fieldInfo["CLASS"]) $inputGroupClasses = array_merge($inputGroupClasses, explode(" ", $fieldInfo["CLASS"]));
				?>

				<div class="<?= implode($inputGroupClasses, " ") ?>"
                     data-field-code="<?= strtolower($code) ?>"
                     data-field-type="<?= strtolower($baseType) ?>"
                     data-field-template="<?=strtolower($template) ?>"
				>
					<?
					$inputNum = 1;
					if ($fieldInfo['MULTIPLE'] == "Y" && $fieldInfo['TYPE'] != 'L' && $fieldInfo['TYPE'] != 'E' && $fieldInfo['TYPE'] != 'G') {
						$inputNum += $fieldInfo["MULTIPLE_CNT"];
					}
					if (strlen($fieldInfo['TITLE']) > 0):?>
						<div class="field-title <? if ($isTitleActive): ?>_active<? endif; ?>">
							<?= $fieldInfo['TITLE'] ?><? if ('Y' == $fieldInfo['IS_REQUIRED']): ?><span
									class="starrequired">*</span><? endif; ?>
						</div>
					<? endif; ?>

					<div class="input-container">

						<?
						/**
						 * @var \Citrus\Forms\BaseComponent $component
						 */
                        $component->includeFieldTemplate($fieldInfo, $templateData);?>
						<? if ($arParams["JQUERY_VALID"] == "Y"): ?>
							<div class="error help-block"></div>
						<? endif; ?>
					</div>
					<!-- /.input-container -->

					<?/* if (strlen($fieldInfo['TOOLTIP']) > 0): ?>
						<p class="field-description"><?= $fieldInfo['TOOLTIP'] ?></p>
					<? endif; */?>

				</div><!-- .form-group -->
				<?
			endforeach;
			//закрываем группы
			if (false !== $depthLevel)
				echo str_repeat('</div>', $depthLevel);
			?>
		</div><!-- .citrus-form__fields -->

		<div class="citrus-form__footer">

			<div class="form-group required-message-block">
				<span class="starrequired">*</span>
				<span><?= Loc::getMessage('REQUIRED_MESSAGE_LABLE') ?></span>
			</div>

			<? //agreement?>
            <?if ($arParams["USER_CONSENT"] === 'Y' && $arParams["USER_CONSENT_ID"]):?>
                <div class="form-group agree-block">
                    <div class="cui-checkbox-group checkbox-count-1">
                    <?$APPLICATION->IncludeComponent(
                        "bitrix:main.userconsent.request",
                        "citrus-agreement",
                        array(
                            "ID" => $arParams["USER_CONSENT_ID"],
                            "IS_CHECKED" => 'N',
                            "AUTO_SAVE" => "Y",
                            "IS_LOADED" => 'N',
	                        'SUBMIT_EVENT_NAME' => 'check-form-'.$arResult["FORM_ID"],
                            "REPLACE" => array(
                                'button_caption' => $arParams['BUTTON_TITLE'],
                                'fields' => $arParams['AGREEMENT_FIELDS'] ? explode(', ', $arParams['AGREEMENT_FIELDS']) : ''
                            ),
                            "FORM_ID" => $arResult['FORM_ID'],
                            'INPUT_NAME' => 'bx-agreement'
                        ),
                        $component,
	                    array('HIDE_ICONS' => 'Y')
                    );?>
                    </div>
                </div>
            <?elseif($arParams["AGREEMENT_LINK"]):?>
                <div class="form-group agree-block">
                    <div class="cui-checkbox-group checkbox-count-1">
                        <label class="cui-checkbox__label">
                            <input type="checkbox" data-valid="required" data-valid-params='{"important":1}'
                                   name="AGREEMENT" class="cui-checkbox__input">
                            <div class="cui-checkbox__checkmark"></div>
                            <div class="cui-checkbox__label-text"><?= Loc::getMessage("CITRUS_FORM_AGREEMENT_MESSAGE", array("#LINK#" => $arParams["AGREEMENT_LINK"])) ?></div>
				            <? if ($agreementDescription = Loc::getMessage("CITRUS_FORM_AGREEMENT_MESSAGE_DESCRIPTION")):?>
                                <div class="agree-description"><?= $agreementDescription ?></div>
				            <? endif; ?>
                        </label>
                    </div>
                </div>
            <?endif;?>
			
			<? if (strlen($arParams['AFTER_FORM_TOOLTIP']) > 0): ?>
				<div class="b-additional-text"><?= $arParams['~AFTER_FORM_TOOLTIP'] ?></div>
			<? endif; ?>
			
			<div class="form-group form-group-btn">
				<div class="input-container button-position-<?= strtolower($arParams["BUTTON_POSITION"]) ?>">
					<button
						type="submit"
						name="save"
						class="<?=$arParams["BUTTON_CLASS"] ? $arParams["BUTTON_CLASS"] : 'btn btn-primary'?>"
					>
						<span class="btn-label"><?= $arParams['BUTTON_TITLE'] ?></span>
					</button>
				</div>
			</div>
		</div><!-- .citrus-form__footer -->
	</form>
</div>


<script>
	;(function () {
		new BX.Citrus.Form(
			"<?=$arResult["FORM_ID"]?>",
			<?=\Bitrix\Main\Web\Json::encode($arResult["ITEMS"]);?>,
			<?=\Bitrix\Main\Web\Json::encode(array(
				"recaptcha" => (bool) ('Y' === $arParams['USE_GOOGLE_RECAPTCHA']),
				"agreement" => (bool) ($arParams["USER_CONSENT"] === 'Y' && $arParams["USER_CONSENT_ID"]),
				"antispam" => (bool) ($arParams['HIDDEN_ANTI_SPAM'] !== "N"),
			))?>
        );
	})();
</script>
