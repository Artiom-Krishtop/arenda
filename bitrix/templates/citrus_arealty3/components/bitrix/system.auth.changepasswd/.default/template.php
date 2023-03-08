<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

/**
 * @var $arResult
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
            <form class="citrus-form lk--form" method="post" action="<?=$arResult["AUTH_FORM"]?>" name="bform" id="changepass-form">
		        <?if (strlen($arResult["BACKURL"]) > 0): ?>
                    <input type="hidden" name="backurl" value="<?=$arResult["BACKURL"]?>" />
		        <? endif ?>
                <input type="hidden" name="AUTH_FORM" value="Y">
                <input type="hidden" name="TYPE" value="CHANGE_PWD">
                <input type="hidden" name="change_pwd" value="<?=GetMessage("AUTH_CHANGE")?>">
                <input type="hidden" name="USER_CHECKWORD" value="<?=$arResult["USER_CHECKWORD"]?>" placeholder="" maxlength="50">
                <?
                $getLogin = \Bitrix\Main\Context::getCurrent()->getRequest()->get("USER_LOGIN");
                if ($getLogin):
                ?>
                    <input type="hidden" name="USER_LOGIN" value="<?=$arResult["LAST_LOGIN"]?>" maxlength="50">
                <?else:?>
                    <div class="form-group js_material_switch_container" >
                        <div class="field-title <?=$arResult["LAST_LOGIN"] ? "_active" : ""?>">
			                <?=GetMessage("AUTH_LOGIN")?><span class="starrequired">*</span>
                        </div>
                        <div class="input-container">
			                <?$passLength = $arResult['GROUP_POLICY']['PASSWORD_LENGTH']?>
                            <input data-valid="required" class="form-control" type="text" name="USER_LOGIN" value="<?=$arResult["LAST_LOGIN"]?>" placeholder="" maxlength="50">
                            <div class="error help-block"></div>
                        </div>
                        <!-- /.input-container -->
                    </div><!-- form-group -->
                <?endif;?>



                <div class="form-group js_material_switch_container" >
                    <div class="field-title <?=$arResult["USER_PASSWORD"] ? "_active" : ""?>">
                        <?=GetMessage("AUTH_NEW_PASSWORD_REQ")?><span class="starrequired">*</span>
                    </div>
                    <div class="input-container">
                        <input data-valid="required main_password <?=$passLength > 0 ? 'length': '' ?>" data-valid-param-minlength='<?=(int) $passLength?>'  class="form-control" type="password" name="USER_PASSWORD" value="<?=$arResult["USER_PASSWORD"]?>" placeholder="" maxlength="50">
                        <div class="error help-block"></div>
                    </div>
                    <!-- /.input-container -->
                </div><!-- form-group -->


                <div class="form-group js_material_switch_container" >
                    <div class="field-title <?=$arResult["USER_CONFIRM_PASSWORD"] ? "_active" : ""?>">
                        <?=GetMessage("AUTH_NEW_PASSWORD_CONFIRM")?><span class="starrequired">*</span>
                    </div>
                    <div class="input-container">
                        <input data-valid="required confirm_password"  class="form-control" type="password" name="USER_CONFIRM_PASSWORD" value="<?=$arResult["USER_CONFIRM_PASSWORD"]?>" placeholder="" maxlength="50" autocomplete="off">
                        <div class="error help-block"></div>
                    </div>
                    <!-- /.input-container -->
                </div><!-- form-group -->

                <?if($arResult["USE_CAPTCHA"]):?>

                    <div class="field-compare">
                        <div class="form-group captcha-image-group js_material_switch_container">
                            <input type="hidden" name="captcha_sid" value="<?echo $arResult["CAPTCHA_CODE"]?>" />
                            <img class="captcha-image" src="/bitrix/tools/captcha.php?captcha_sid=<?echo $arResult["CAPTCHA_CODE"]?>" width="180" height="40" alt="CAPTCHA" />
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

            <div class="form-group form-group-btn js_material_switch_container">
                <div class="form-group-btn__description">
                    <a href="<?=$arResult["AUTH_AUTH_URL"]?>" rel="nofollow"><?=Loc::getMessage("AUTH_AUTH")?></a>
                </div>
                <div class="button-position-right">
                    <button class="btn btn-border btn-transparent" name="iblock_submit"><?=Loc::getMessage("AUTH_CHANGE")?></button>
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
					"PATH" => SITE_DIR . "include/lk/auth.php",
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

<div class="changepass-form-wrapper">

</div>

<? CJSCore::Init('citrus_validator')?>
<script type="text/javascript">
	;(function () {
		var form = new BX.Citrus.Form('changepass-form', {
		}, {
			recaptcha: false,
			agreement: false,
			antispam: false,
			ajax: false,
		});
		form.labelSwitch();

		document.bform.USER_LOGIN.focus();
	})();
</script>
