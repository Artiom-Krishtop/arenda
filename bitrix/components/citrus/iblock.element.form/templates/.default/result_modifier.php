<?php

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

/*foreach ($arResult["DEFAULT_VALUE"] as $CODE => $VALUE) {
    if( $VALUE ) $arResult['OLD_VALUE'][$CODE] = $arResult['OLD_VALUE'][$CODE] ? $arResult['OLD_VALUE'][$CODE] : $VALUE;
}*/

$this->getComponent()->setResultCacheKeys(array(
    "FORM_ID",
    "SUCCESS",
    "ERRORS",
    "MESSAGE",
    "debug",
    "CAPTCHA_CODE"
));
?>
<? //delete sucess in url
if (isset($_GET["success_{$arResult["FORM_ID"]}"]) && $_GET["success_{$arResult["FORM_ID"]}"] === "true"):?>
<script>
    //IE10+
    if ( typeof window.history.pushState !== "undefined") {
        window.history.pushState(null, null, "<?=$APPLICATION->GetCurPageParam("", array("success_{$arResult["FORM_ID"]}"))?>");
    }
</script>
<?endif;?>

