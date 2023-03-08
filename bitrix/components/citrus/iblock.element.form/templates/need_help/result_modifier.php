<?
global $USER;
$arResult['OLD_VALUE']['CODE'] = $USER->GetEmail();

if($arParams['JQUERY_VALID'] == "Y") {
	foreach($arResult['ITEMS'] as $code => $arField) {
		if(strlen($arField['VALIDRULE']) <= 0) continue;
	
		$arResult['RULE']["PROPERTY[" . $code . "]"] = array(
			"RUL" => preg_replace("/(^.*?\/)|(\/.*$)/", '', $arField['VALIDRULE']),
			"CODE" => "PROPERTY[" . $code . "]",
			"VALID_FUNC" => $code, 
			"ERROR_MSG" => $arField['VALID_ERROR_MSG']
		);
	}
	
	if(isset($arResult['RULE']) && !empty($arResult['RULE'])):?>
	<script type="text/javascript">
		var $arValidateParam = <?=CUtil::PhpToJSObject($arResult['RULE'])?>;
		var $errorMsgNumFormat = <?=CUtil::PhpToJSObject(GetMessage('TPL_REG_NUM_FORM_ERROR'));?>
		var $useJquery = true
	</script>
	<?endif;?>
<?}

if($arParams['AJAX'] == "Y" && $_REQUEST['ajax_param'] && strlen($arResult["MESSAGE"]) > 0) {
	$strReplace = $arResult['ITEMS']['PROPERTY_TIME']['ENUM'][$arResult['OLD_VALUE']['PROPERTY_TIME']];
	$arResult["MESSAGE"] = str_replace("#PROPERTY_TIME#", $strReplace['VALUE'], $arResult["MESSAGE"]);
	$arResult['OLD_VALUE'] = array();
}
?>