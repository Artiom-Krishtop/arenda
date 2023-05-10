<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

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
            <form class="citrus-form lk--form" name="form_auth" method="post" target="_top" action="<?=$arResult["AUTH_URL"]?>" id="auth-form">
                <input type="hidden" name="AUTH_FORM" value="Y" />
                <input type="hidden" name="TYPE" value="AUTH" />
                <input type="hidden" name="Login" value="Y">
                <?if (strlen($arResult["BACKURL"]) > 0):?>
                    <input type="hidden" name="backurl" value="<?=$arResult["BACKURL"]?>" />
                <?endif?>
                <?foreach ($arResult["POST"] as $key => $value):?>
                    <input type="hidden" name="<?=$key?>" value="<?=$value?>" />
                <?endforeach?>

                <div class="form-group js_material_switch_container ">
                    <div class="field-title"><?=Loc::getMessage("AUTH_LOGIN")?> <span class="starrequired">*</span></div>
                    <div class="input-container">
                        <input data-valid="required" class="form-control" type="text" name="USER_LOGIN" maxlength="255" value="<?=$arResult["LAST_LOGIN"]?>" />
                        <div class="error help-block"></div>
                    </div>
                </div>

                <div class="form-group js_material_switch_container">
                    <div class="field-title"><?=Loc::getMessage("AUTH_PASSWORD")?> <span class="starrequired">*</span></div>
                    <div class="input-container">
                        <input data-valid="required" class="form-control" type="password" name="USER_PASSWORD" maxlength="255" autocomplete="off" />
                        <div class="error help-block"></div>
                    </div>
                </div>

                <?if($arResult["CAPTCHA_CODE"]):?>
                <div class="field-compare">
                    <div class="form-group captcha-image-group js_material_switch_container">
                        <input type="hidden" name="captcha_sid" value="<?echo $arResult["CAPTCHA_CODE"]?>" />
                        <img class="captcha-image" src="/bitrix/tools/captcha.php?captcha_sid=<?echo $arResult["CAPTCHA_CODE"]?>" width="180" height="40" alt="CAPTCHA" />
                    </div>
                    <div class="form-group captcha-input-group js_material_switch_container">
                        <div class="field-title"><?=Loc::getMessage("AUTH_CAPTCHA_PROMT")?> <span class="starrequired">*</span></div>
                        <div class="input-container">
                            <input data-valid="required" class="form-control" type="text" name="captcha_word" maxlength="50" value="" size="15" autocomplete="off"/>
                            <div class="error help-block"></div>
                        </div>
                    </div>
                </div>
                <?endif;?>

                <?if ($arResult["STORE_PASSWORD"] == "Y"):?>
                    <input type="hidden" name="USER_REMEMBER" value="Y">
                <?endif;?>

                <div class="form-group form-group-btn js_material_switch_container">
                    <div class="form-group-btn__description">
                        <a href="<?=$arResult["AUTH_REGISTER_URL"]?>" rel="nofollow"><?=Loc::getMessage("AUTH_REGISTER")?></a>
                        <a href="<?=$arResult["AUTH_FORGOT_PASSWORD_URL"]?>" rel="nofollow"><?=Loc::getMessage("AUTH_FORGOT_PASSWORD_2")?></a>
                    </div>
                    <div class="button-position-right">
                        <button class="btn btn-border btn-transparent" name="iblock_submit"><?=Loc::getMessage("LK_AUTH_BTN_TITLE")?></button>
                    </div>
                </div>
            </form>
        </div>
    </div><!-- .lk__form -->
    <!-- <div class="lk__text-w">
       <div class="lk__inner"> -->
	       <? //$APPLICATION->IncludeComponent(
		    //    "bitrix:main.include",
		    //    ".default",
		    //    array(
			//        "AREA_FILE_SHOW" => "file",
			//        "AREA_FILE_SUFFIX" => "inc",
			//        "EDIT_TEMPLATE" => "clear.php",
			//        "PATH" => SITE_DIR . "include/lk/auth.php",
			//        "PAGE_SECTION" => "Y",
			//        "TITLE" => "",
			//        "COMPONENT_TEMPLATE" => ".default",
			//        "ADDITIONAL_CLASS" => "",
			//        "SECTION_BORDERED" => "Y",
		    //    ),
		    //    false
	       //); ?>
       <!-- </div>
    </div>.lk__text -->
</div><!-- .lk -->

<script>
    ;(function () {
	    var form = new BX.Citrus.Form('auth-form', {
	    }, {
	    	recaptcha: false,
		    agreement: false,
		    antispam: false,
		    ajax: false,
	    });
	    form.labelSwitch();

	    <?if (strlen($arResult["LAST_LOGIN"])>0):?>
	    try{document.form_auth.USER_PASSWORD.focus();}catch(e){}
	    <?else:?>
	    try{document.form_auth.USER_LOGIN.focus();}catch(e){}
	    <?endif?>
    })();
</script>


<style>

	.message-block.bg-danger .message-block-txt,
	.message-block.bg-danger .message-block-icon
	{
		color: red;
	}
</style>