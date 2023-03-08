<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

/** @var array $arParams */
/** @var array $arResult */

use Bitrix\Main\Localization\Loc;
$arResult['CONFIG']['FORM_ID'] = $arParams['FORM_ID'];
$config = \Bitrix\Main\Web\Json::encode($arResult['CONFIG']);
?>

<script type="text/html" data-bx-template="main-user-consent-request-loader">
    <div class="main-user-consent-request-popup">
        <div class="main-user-consent-request-popup-cont">
            <div data-bx-head="" class="main-user-consent-request-popup-header"></div>
            <div class="main-user-consent-request-popup-body">
                <div data-bx-loader="" class="main-user-consent-request-loader">
                    <svg class="main-user-consent-request-circular" viewBox="25 25 50 50">
                        <circle class="main-user-consent-request-path" cx="50" cy="50" r="20" fill="none" stroke-width="1" stroke-miterlimit="10"></circle>
                    </svg>
                </div>
                <div data-bx-content="" class="main-user-consent-request-popup-content">
                    <div class="main-user-consent-request-popup-textarea-block">
                        <textarea data-bx-textarea="" class="main-user-consent-request-popup-text" disabled></textarea>
                    </div>
                    <div class="main-user-consent-request-popup-buttons">
                        <span data-bx-btn-accept="" class="main-user-consent-request-popup-button main-user-consent-request-popup-button-acc">Y</span>
                        <span data-bx-btn-reject="" class="main-user-consent-request-popup-button main-user-consent-request-popup-button-rej">N</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</script>
<label data-bx-user-consent="<?=htmlspecialcharsbx($config)?>" class="main-user-consent-request cui-checkbox__label">
    <input class="cui-checkbox__input" type="checkbox" value="Y" <?=($arParams['IS_CHECKED'] ? 'checked' : '')?> name="<?=htmlspecialcharsbx($arParams['INPUT_NAME'])?>">
    <span class="cui-checkbox__checkmark"></span>
    <a class="cui-checkbox__label-text"><?=$arParams['AGREEMENT_LABEL_MESSAGE'] ? htmlspecialcharsbx($arParams['AGREEMENT_LABEL_MESSAGE']) : Loc::getMessage('MAIN_USER_CONSENT_REQUEST_LABEL_MESSAGE')?></a>
</label>

<?if($arParams['FORM_ID']):?>
    <script>
        ;(function(){
	        BX.UserConsent.load(document.getElementById('<?=$arParams['FORM_ID']?>'));
        }());
    </script>
<?endif;?>
