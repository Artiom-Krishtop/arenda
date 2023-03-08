<?
/* get tree of sections with 2 depth lvl*/
$arResult['SECTIONS_TREE'] = array_reduce($arResult["SECTIONS"], function($ar, $arSection) {
	if ($arSection['DEPTH_LEVEL'] == 1) {
		$ar[] = $arSection;
	} else {
		$ar[ count($ar) - 1]['SUBSECTIONS'][] = $arSection;
	}

	return $ar;
}, []);