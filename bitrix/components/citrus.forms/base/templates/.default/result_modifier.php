<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

$firstGroupField = false;
foreach ($arResult["ITEMS"] as $code => &$fieldInfo) {
	if ($fieldInfo['GROUP_FIELD'] === "Y"){
		$firstGroupField = false;
		continue;
	}

    /**
     * Формируем уникальный field_id
     */
    $fieldInfo["ID"] = str_replace( array("[", "]"), "__",$arResult["FORM_ID"]."--".$fieldInfo["CODE"]);

	/**
	 * Лимит файлов
	 */
	if ($fieldInfo["TYPE"] === "F") {
		if ($fieldInfo['OLD_VALUE'])
			$fieldInfo['FILES'] = CFile::GetFileArray($fieldInfo['OLD_VALUE']);

		$fieldInfo["LIMIT"] = $fieldInfo['MULTIPLE'] == 'N' ? 1 : 10;
	}

    /**
     * проставляем old value для дефолтных значений
     */
    $fieldInfo["OLD_VALUE"] = $fieldInfo["OLD_VALUE"] ? $fieldInfo["OLD_VALUE"] : $fieldInfo["DEFAULT"];

	/**
	 * Поле ADDITIONAL для валидации нужно заполнять в строку в формате 'filesize=1mb;minlength=4'
	 */
	if ($fieldInfo['ADDITIONAL']) {
		$additionalExplode = explode(';', $fieldInfo['ADDITIONAL']);
		$arAdditional = [];
		foreach ( $additionalExplode as $add) {
			$addOne = explode('=', $add);
			$arAdditional[trim($addOne[0])] = trim($addOne[1]);
		}
		$fieldInfo['ADDITIONAL'] = $arAdditional;
	}

	$fieldInfo['CODE'] = $fieldInfo['MULTIPLE'] == "Y" ? $fieldInfo["CODE"] . "[]": $fieldInfo["CODE"];

	if (!$firstGroupField && $fieldInfo['HIDE_FIELD'] !== "Y" ) { $fieldInfo['FIRST_GROUP_FIELD'] = true; $firstGroupField = true; }
}
?>

