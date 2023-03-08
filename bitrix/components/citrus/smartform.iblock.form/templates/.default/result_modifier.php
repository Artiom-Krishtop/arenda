<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

$arResult["VISIBLE_FIELD_COUNT"] = 0;
$firstGroupField = false;
foreach ($arResult["ITEMS"] as $code => &$fieldInfo) {
	if ($fieldInfo['GROUP_FIELD'] === "Y"){
		$firstGroupField = false;
		continue;
	}

	if (!$firstGroupField && $fieldInfo['HIDE_FIELD'] !== "Y" ) { $fieldInfo['FIRST_GROUP_FIELD'] = true; $firstGroupField = true; }

    /**
     * Формируем уникальный field_id
     */
    $fieldInfo["ID"] = str_replace( array("[", "]"), "__",$arResult["FORM_ID"]."--".$fieldInfo["CODE"]);

	/**
	 * Лимит файлов
	 */
	if ($fieldInfo["TYPE"] === "F") {
		$fieldInfo["LIMIT"] = $fieldInfo['MULTIPLE'] == 'N' ? 1 : 10;
	}

	/**
	 * Поле ADDITIONAL для валидации нужно заполнять в строку в формате 'filesize=1mb;minlength=4'
	 */
	if ($fieldInfo['ADDITIONAL']) {
		$additionalExplode = explode(';', $fieldInfo['ADDITIONAL']);
		$arAdditional = array();
		foreach ( $additionalExplode as $add) {
			$addOne = explode('=', $add);
			$arAdditional[trim($addOne[0])] = trim($addOne[1]);
		}
		$fieldInfo['ADDITIONAL'] = $arAdditional;
	}

    /**
     * проставляем old value для дефолтных значений
     */
    $fieldInfo["OLD_VALUE"] = $fieldInfo["OLD_VALUE"] ? $fieldInfo["OLD_VALUE"] : $fieldInfo["DEFAULT"];

    if ($fieldInfo["HIDE_FIELD"] !== "Y") $arResult["VISIBLE_FIELD_COUNT"]++;
}
?>


<?
/**
 * delete success in url
 */
/*if (isset($_GET["success_{$arResult["FORM_ID"]}"]) && $_GET["success_{$arResult["FORM_ID"]}"] === "true"):?>
	<script>
		//IE10+
		if (typeof window.history.pushState !== "undefined") {
			window.history.pushState(null, null, "<?=$APPLICATION->GetCurPageParam("", array("success_{$arResult["FORM_ID"]}"))?>");
		}
	</script>
<?endif;*/?>





