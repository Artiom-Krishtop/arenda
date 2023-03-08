<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)die();

/**
 * @global CMain $APPLICATION
 * @var array $arParams
 * @var array $arResult
 */

$this->createFrame()->begin("Loading...");

use Bitrix\Main\Localization\Loc;
?>


<div class="lk">
    <div class="lk__form-w">
		<?
		$isResult = !empty($arParams["~AUTH_RESULT"]);
		$isError = $arResult['ERROR_MESSAGE'] <> '' || $arParams["~AUTH_RESULT"]['TYPE'] == 'ERROR';
		?>
		<?if($isResult || $isError ):?>
            <div class="citrus-form__message-block">
	            <div class="message-block <?=$isError ? 'bg-danger _danger' : 'bg-success _success'?>">
                    <div class="message-block-icon"></div>
                    <div class="message-block-txt">
						<?if(!empty($arParams["~AUTH_RESULT"])):
							$text = str_replace(array("<br>", "<br />"), "\n", $arParams["~AUTH_RESULT"]["MESSAGE"]);
							?>
							<?=nl2br(htmlspecialcharsbx($text))?>
						<?endif?>

						<?if($arResult['ERROR_MESSAGE'] <> ''):
							$text = str_replace(array("<br>", "<br />"), "\n", $arResult['ERROR_MESSAGE']);
							?>
							<?=nl2br(htmlspecialcharsbx($text))?>
						<?endif?>
                    </div>
                </div>
            </div>
		<?endif;?>

        <div class="lk__inner">
            <form class="citrus-form lk--form" name="bform" method="post" target="_top" action="<?=$arResult["AUTH_URL"]?>" id="forgot-password-from">
	            <?if($arResult["BACKURL"] <> ''):?>
                    <input type="hidden" name="backurl" value="<?=$arResult["BACKURL"]?>" />
	            <?endif?>
                <input type="hidden" name="AUTH_FORM" value="Y">
                <input type="hidden" name="TYPE" value="SEND_PWD">
                <input type="hidden" name="send_account_info" value="<?=Loc::getMessage("AUTH_SEND")?>">


                <div class="form-group js_material_switch_container" >
                    <div class="field-title <?=$arResult["LAST_LOGIN"] ? "_active" : ""?>">
                        <?=Loc::getMessage("AUTH_LOGIN_EMAIL")?><span class="starrequired">*</span>
                    </div>
                    <div class="input-container">
                        <input data-valid="required ruleGroup" data-valid-params='{"ruleGroup": ["login", "email"]}' data-valid-messages='{"ruleGroup":"<?=Loc::getMessage("LK_VALIDATE_LOGIN_EMAIL")?>"}'  class="form-control" type="text" name="USER_LOGIN" value="<?=$arResult["LAST_LOGIN"]?>" placeholder="" maxlength="255">
                        <input type="hidden" name="USER_EMAIL" />
                        <div class="error help-block"></div>
                    </div>
                    <!-- /.input-container -->
                </div>

	            <?if ($arResult["USE_CAPTCHA"]):?>

                    <div class="field-compare">
                        <div class="form-group captcha-image-group js_material_switch_container">
                            <input type="hidden" name="captcha_sid" value="<?=$arResult["CAPTCHA_CODE"]?>" />
                            <img class="captcha-image" src="/bitrix/tools/captcha.php?captcha_sid=<?=$arResult["CAPTCHA_CODE"]?>" width="180" height="40" alt="CAPTCHA" />
                        </div>
                        <div class="form-group captcha-input-group js_material_switch_container">
                            <div class="field-title"><?=Loc::getMessage("system_auth_captcha")?> <span class="starrequired">*</span></div>
                            <div class="input-container">
                                <input data-valid="required" class="form-control" type="text" name="captcha_word" maxlength="50" value="" size="15" autocomplete="off"/>
                                <div class="error help-block"></div>
                            </div>
                        </div>
                    </div>
	            <?endif?>

				<?if ($arResult["STORE_PASSWORD"] == "Y"):?>
                    <input type="hidden" name="USER_REMEMBER" value="Y">
				<?endif;?>

                <div class="form-group form-group-btn js_material_switch_container">
                    <div class="form-group-btn__description">
                        <a href="<?=$arResult["AUTH_AUTH_URL"]?>" rel="nofollow"><?=Loc::getMessage("AUTH_AUTH")?></a>
                    </div>
                    <div class="button-position-right">
                        <button class="btn btn-border btn-transparent" name="iblock_submit"><?=Loc::getMessage("AUTH_SEND")?></button>
                    </div>
                </div>
            </form>
        </div>
    </div><!-- .lk__form -->
    <div class="lk__text-w">
        <div class="lk__inner">
			<? $APPLICATION->IncludeComponent(
				"bitrix:main.include",
				".default",
				array(
					"AREA_FILE_SHOW" => "file",
					"AREA_FILE_SUFFIX" => "inc",
					"EDIT_TEMPLATE" => "clear.php",
					"PATH" => SITE_DIR . "include/lk/forgot_pass.php",
					"PAGE_SECTION" => "Y",
					"TITLE" => "",
					"COMPONENT_TEMPLATE" => ".default",
					"ADDITIONAL_CLASS" => "",
					"SECTION_BORDERED" => "Y",
				),
				false
			); ?>
        </div>
    </div><!-- .lk__text -->
</div><!-- .lk -->

<style>

	.message-block.bg-danger .message-block-txt,
	.message-block.bg-danger .message-block-icon
	{
		color: red;
	}
</style>

<script type="text/javascript">
	;(function () {
		var form = new BX.Citrus.Form('forgot-password-from', {
		}, {
			recaptcha: false,
			agreement: false,
			antispam: false,
			ajax: false,
		});
		form.labelSwitch();

		document.bform.onsubmit = function(){document.bform.USER_EMAIL.value = document.bform.USER_LOGIN.value;};
		document.bform.USER_LOGIN.focus();
	})();
</script>