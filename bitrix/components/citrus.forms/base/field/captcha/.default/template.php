<?

use Bitrix\Main\Page\Asset;

if('Y' === $arParams['USE_GOOGLE_RECAPTCHA']):
	Asset::getInstance()->addJs('//www.google.com/recaptcha/api.js?render=explicit&hl='.LANGUAGE_ID);
	?>
    <input type="hidden" name="<?= $fieldInfo['CODE'] ?>" />
	<div id="<?= $fieldInfo['ID'] ?>" class="citrus-form__recaptcha-container"></div>

	<script>
		$(function() {
			/**
             * API гугл рекапчи https://developers.google.com/recaptcha/docs/display
             * @var (object) grecaptcha - глобальный объект рекапчи
			 */
			setTimeout(function () {
				if (typeof grecaptcha == 'undefined')
					return false;
				var $hiddenRecaptcha = $('[name="<?=$fieldInfo['CODE']?>"]');

				var widgetId = grecaptcha.render('<?=$fieldInfo['ID']?>', {
					'sitekey': '<?=($arParams['GOOGLE_RECAPTCHA_PUBLIC_KEY'])?>',
                    'size': window.innerWidth > 479 ? 'normal' : 'compact',
					'callback': function () {
						$hiddenRecaptcha.trigger('validate');
					}
				});
				$hiddenRecaptcha.data('widget-id', widgetId);
				$hiddenRecaptcha.on('reset', function () {
					grecaptcha.reset(widgetId);
				});
			}, 200);
		});
	</script>
<? else:?>
	<img src="/bitrix/tools/captcha.php?captcha_sid=<?= $arResult["CAPTCHA_CODE"] ?>" width="180" height="40" alt="CAPTCHA" class="ciee-captcha-image js-update-captcha-image"/>
	<input class="form-control" type="text" name="<?= $fieldInfo["CODE"] ?>" maxlength="50" value="" class="ciee-captcha-input" placeholder="<?= $fieldInfo['PLACEHOLDER'] ?>">
	<input type="hidden" name="captcha_sid" value="<?= $arResult["CAPTCHA_CODE"] ?>" class="js-update-captcha-code"/>
<? endif; ?>