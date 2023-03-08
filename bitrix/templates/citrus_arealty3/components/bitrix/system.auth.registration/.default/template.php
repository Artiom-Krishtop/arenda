<?
/**
 * Bitrix Framework
 * @package bitrix
 * @subpackage main
 * @copyright 2001-2014 Bitrix
 */

/**
 * Bitrix vars
 * @global CMain $APPLICATION
 * @param array $arParams
 * @param array $arResult
 * @param CBitrixComponentTemplate $this
 */

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$this->createFrame()->begin("Loading...");?>

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
					    <?if(!empty($arParams["~AUTH_RESULT"]) && $arParams["~AUTH_RESULT"]['TYPE'] == 'ERROR'){
                            $text = str_replace(array("<br>", "<br />"), "\n",$arParams["~AUTH_RESULT"]["MESSAGE"]);
                            echo nl2br(htmlspecialcharsbx($text));
                        }else if(!empty($arParams["~AUTH_RESULT"]) && $arParams["~AUTH_RESULT"]['TYPE'] == 'OK') {
                            $text = GetMessage('AUTH_SUCCESS');
                            echo nl2br(htmlspecialcharsbx($text));
                        }?>

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
            <form class="citrus-form lk--form" name="form_register" method="post" target="_top" action="<?=$arResult["AUTH_URL"]?>" id="register-form">
                <input type="hidden" name="AUTH_FORM" value="Y" />
                <input type="hidden" name="TYPE" value="REGISTRATION" />

                <?if (strlen($arResult["BACKURL"]) > 0):?>
                    <input type="hidden" name="backurl" value="<?=$arResult["BACKURL"]?>" />
                <?endif?>

                <div class="form-group js_material_switch_container ">
                    <div class="field-title"><?=GetMessage("AUTH_LOGIN")?> <span class="starrequired">*</span></div>
                    <div class="input-container">
                        <input data-valid="required" class="form-control" type="text" name="USER_LOGIN" maxlength="255" value="<?=$arResult["USER_LOGIN"]?>" />
                        <div class="error help-block"></div>
                    </div>
                </div>

                <div class="form-group js_material_switch_container">
                    <div class="field-title"><?=GetMessage("AUTH_NAME")?> <span class="starrequired">*</span></div>
                    <div class="input-container">
                        <input data-valid="required" class="form-control" type="text" name="USER_NAME" maxlength="255" autocomplete="off" value="<?=$arResult["USER_NAME"]?>"/>
                        <div class="error help-block"></div>
                    </div>
                </div>

                <div class="form-group js_material_switch_container">
                    <div class="field-title"><?=GetMessage("AUTH_ORGANIZATION")?> <span class="starrequired">*</span></div>
                    <div class="input-container">
                        <input data-valid="required" class="form-control" type="text" name="USER_COMPANY" maxlength="255" autocomplete="off" value="<?=$arResult["USER_COMPANY"]?>"/>
                        <div class="error help-block"></div>
                    </div>
                </div>

                <div class="form-group js_material_switch_container">
                    <div class="field-title"><?=GetMessage("AUTH_PHONE")?> <span class="starrequired">*</span></div>
                    <div class="input-container">
                        <input data-valid="required" class="form-control js-phone-mask" type="text" name="USER_PHONE" maxlength="255" autocomplete="off" value="<?=$arResult["USER_PHONE"]?>"/>
                        <div class="error help-block"></div>
                    </div>
                </div>

                <div class="form-group js_material_switch_container">
                    <div class="field-title"><?=GetMessage("AUTH_EMAIL")?> <span class="starrequired">*</span></div>
                    <div class="input-container">
                        <input data-valid="required" class="form-control" type="text" name="USER_EMAIL" maxlength="255" autocomplete="off" value="<?=$arResult["USER_EMAIL"]?>"/>
                        <div class="error help-block"></div>
                    </div>
                </div>

                <div class="form-group js_material_switch_container">
                    <div class="field-title"><?=GetMessage("AUTH_PASSWORD")?> <span class="starrequired">*</span></div>
                    <div class="input-container">
                        <input data-valid="required" class="form-control" type="password" name="USER_PASSWORD" maxlength="255" autocomplete="off"/>
                        <div class="error help-block"></div>
                    </div>
                </div>

                <div class="form-group js_material_switch_container">
                    <div class="field-title"><?=GetMessage("AUTH_PASSWORD_CONFIRM")?> <span class="starrequired">*</span></div>
                    <div class="input-container">
                        <input data-valid="required" class="form-control" type="password" name="USER_CONFIRM_PASSWORD" maxlength="255" autocomplete="off"/>
                        <div class="error help-block"></div>
                    </div>
                </div>

                <div class="form-group concent">
                    <div class="field-title"><?=GetMessage("AUTH_CONCENT", array('#CONCENT_LINK#' => '<a class="form-politika" href="/politika/">персональных данных</a>'))?> <span class="starrequired">*</span></div>
                    <div class="input-container">
                        <input data-valid="required" class="form-control" type="checkbox" name="USER_CONCENT" value="Y"/>
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
                            <div class="field-title"><?=GetMessage("AUTH_CAPTCHA_PROMT")?> <span class="starrequired">*</span></div>
                            <div class="input-container">
                                <input data-valid="required" class="form-control" type="text" name="captcha_word" maxlength="50" value="" size="15" autocomplete="off"/value="<?=$arResult["LAST_LOGIN"]?>">
                                <div class="error help-block"></div>
                            </div>
                        </div>
                    </div>
                <?endif;?>

                <div class="form-group form-group-btn js_material_switch_container">
                    <div class="form-group-btn__description">
                        <a href="<?=$arResult["AUTH_AUTH_URL"]?>" rel="nofollow"><?=GetMessage("AUTH_AUTH")?></a>
                    </div>
                    <div class="button-position-right">
                        <button class="btn btn-border btn-transparent" name="iblock_submit"><?=GetMessage("AUTH_REGISTER")?></button>
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
			//        "PATH" => SITE_DIR . "include/lk/register.php",
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
    (function () {
	    var form = new BX.Citrus.Form('register-form', {
	    }, {
	    	recaptcha: false,
		    agreement: false,
		    antispam: false,
		    ajax: false,
	    });
	    form.labelSwitch();

	    try{document.form_register.USER_LOGIN.focus();}catch(e){}
    })();
</script>
