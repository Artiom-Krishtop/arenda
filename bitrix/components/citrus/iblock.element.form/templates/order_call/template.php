<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$frame = $this->createFrame()->begin('');
?>

<?/*if($arParams['SUB_TEXT']):?>
	<div><?=htmlspecialchars_decode($arParams['SUB_TEXT'])?></div><br/>
<?endif;*/?>

<div class="<?=($arParams['AJAX'] == "Y" ? 'bx-js-update-block':'')?> md-content">

<?if($arParams['FORM_TITLE']):?>
	<div class="call-header"><?=htmlspecialchars_decode($arParams['FORM_TITLE'])?></div>
<?endif;?>
<?if($arParams['SUB_TEXT']):?>
	<p class="call-text"><?=htmlspecialchars_decode($arParams['SUB_TEXT'])?></p>
<?endif;?>


<form id="<?=$arResult["FORM_ID"]?>" name="<?=$arResult["FORM_ID"]?>" action="<?=$arParams['URL_CUR_PAGE']?>" method="post" enctype="multipart/form-data" autocomplete="off" class="call-form">



<?if($arParams['AJAX'] == "Y"):?>
	<input class="bx-js-cur-page-url" value="<?=$arParams['URL_CUR_PAGE']?><?
		if($arParams['EDIT_ELEMENT'] == "Y" && $arParams['ELEMENT_ID'] > 0)
			echo "?ID=" . $arParams['ELEMENT_ID'];
	?>" type="hidden" />
<?endif;?>
<?
if (!empty($arResult["ERRORS"])):?>
<div class="b-form-error-wrapp">
	<?if(strlen($arParams["ERROR_LIST_MESSAGE"]) > 0):?>
	<div class="b-form-error-notice"><?ShowNote($arParams["ERROR_LIST_MESSAGE"]);?></div>
	<?endif;?>
	<?ShowError($arResult["ERRORS"]);?>
</div>
<?endif;?>

<?if (!empty($arResult["MESSAGE"])):?>
<div class="b-form-success-wrapp"><?ShowNote($arResult["MESSAGE"]);?></div>
<?endif;?>

<?
echo bitrix_sessid_post();

$count_group_fields = 0;//счетчик груп
foreach($arResult["ITEMS"] as $code => $fieldInfo):?>
    <?	//группы полей
    	if(strpos($code, "GROUP_FIELD") !== false){
    		echo $count_group_fields ? '</div>' : "" ;
    		echo "<div class='{$fieldInfo['TITLE']}'>";
    		$count_group_fields++;
    		continue;
    	}

    $fieldId = substr($arResult["FORM_ID"], 6) . '_' . $code;
    $name = ($fieldInfo['MULTIPLE'] == "N" && $fieldInfo['PROPERTY_TYPE'] != "F" ? "PROPERTY[" . $code . "]" : "PROPERTY[" . $code . "][]");

    if($fieldInfo['HIDE_FIELD']):?>
        <input type="hidden" name="<?=$name?>" value="<?=$arResult['OLD_VALUE'][$code]?>" id="<?=$fieldId?>" />
           <?continue;?>
    <?endif;?>

    <div class="field <?if($code !== "PROPERTY_ITEMS"):?>inline<?endif;?> ciee-<?=strtolower($code)?> ciee-field-<?=strtolower($fieldInfo['PROPERTY_TYPE'])?> "><?

	$inputNum = 1;
	if ($fieldInfo['MULTIPLE'] == "Y"
		&& $fieldInfo['PROPERTY_TYPE'] != 'L'
		&& $fieldInfo['PROPERTY_TYPE'] != 'E'
		&& $fieldInfo['PROPERTY_TYPE'] != 'G'
	) {
		$inputNum += $fieldInfo["MULTIPLE_CNT"];
	}

	if(strlen($fieldInfo['NAME']) > 0):?>
		<?if($fieldInfo['PROPERTY_TYPE'] == "T"):?>

		<?elseif($fieldInfo['PROPERTY_TYPE'] == "E")://для полей типа список?>
			<?$fieldInfo["ENUM"][0]["VALUE"] = $fieldInfo['NAME']; ?>
		<?else:?>
		<label for="<?=$fieldId?>" class="field-label"><?
		echo $fieldInfo['NAME'] . ':';
			if($fieldInfo['IS_REQUIRED'] == "Y")
		{
			?><span class="mark">*</span><?
		}
		?>
		</label>
		<?endif;?>
	<?
	endif;?>


	<?if($fieldInfo['PROPERTY_TYPE'] == "L" && $fieldInfo["MULTIPLE"] == "Y" && $fieldInfo['LIST_TYPE'] == 'C'):?><div class="field-form-name"></div>
		<div class="field-checks">
	<?else:?>
		<div class="field-input">
	<?endif;?>

	<?
	$arFieldClass = array("f-line-input-val");

	for($i = 0;$i < $inputNum;$i++)
	{
		$value = (is_array($arResult['OLD_VALUE'][$code]) ? $arResult['OLD_VALUE'][$code][$i] : $arResult['OLD_VALUE'][$code]);

		switch($fieldInfo['PROPERTY_TYPE']):
			case 'TAGS':
				$APPLICATION->IncludeComponent(
					"bitrix:search.tags.input",
					"",
					array(
						"VALUE" => $arResult["ELEMENT"][$propertyID],
						"NAME" => $name,
						"TEXT" => 'size="'.$fieldInfo["COL_COUNT"].'"',
					), null, array("HIDE_ICONS"=>"Y")
				);
				break;

			case 'T':
				?>
				<textarea class="<?=implode(" ",$arFieldClass)?>" cols="<?=$fieldInfo["COL_COUNT"]?>" rows="<?=$fieldInfo["ROW_COUNT"]?>" name="<?=$name?>" id="<?=$fieldId?>"><?=$value?></textarea>
				<?
				break;

			case "S":
			case "N":
				switch($fieldInfo['USER_TYPE']):
					case 'DateTime':?>
						<input type="text" name="<?=$name?>" value="<?=$value?>" id="<?=$fieldId?>" />&nbsp;
						<?$APPLICATION->IncludeComponent(
							'bitrix:main.calendar',
							'',
							array(
								'FORM_NAME' => $arResult['FORM_ID'],
								'INPUT_NAME' => $name,
								'INPUT_VALUE' => $value,
							),
							null,
							array('HIDE_ICONS' => 'Y')
						);
					?><small><?=GetMessage("IBLOCK_FORM_DATE_FORMAT")?><?=FORMAT_DATE?></small>
				  <?break;
				  case 'HTML':?>
						<textarea class="<?=implode(" ",$arFieldClass)?>" cols="10" rows="5" name="<?=$name?>" id="<?=$fieldId?>"><?=$value?></textarea>
				  <?break;

				  default:?>
				  	<?if($code=="PROPERTY_CONTACTS" || $code=="PROPERTY_phone"):?>
						<input class="mask_phone <?=implode(" ",$arFieldClass)?>" type="tel" name="<?=$name?>" value="<?=$value?>" id="<?=$fieldId?>" <?if($fieldInfo["IS_REQUIRED"]):?>required="required"<?endif;?> />
				  	<?else:?>
					<input class="<?=implode(" ",$arFieldClass)?>" type="text" name="<?=$name?>" value="<?=$value?>" id="<?=$fieldId?>" <?if($fieldInfo["IS_REQUIRED"]):?>required="required"<?endif;?>/>
					<?endif;?>
				 <?endswitch;
			break;

			case 'F':
				?>
				<input class="<?=implode(" ",$arFieldClass)?>" type="file" name="<?=$name?>" value="<?=$value?>" id="<?=$fieldId?>" />
				<?
				break;

			case "E":
			case "G":
			case 'L':
				if(!empty($fieldInfo['ENUM'])):
					if($fieldInfo['LIST_TYPE'] == 'C'):
						foreach($fieldInfo['ENUM'] as $propID => $info):
							$uniq = uniqid($this->getComponent()->getTemplateName());
							?>

							<input class="<?=implode(" ",$arFieldClass)?>" class="pull-left" type="<?=($fieldInfo["MULTIPLE"] == "Y" ? "checkbox" : "radio")?>" value="<?=$propID?>" id="<?=$uniq?>" <?
								if(is_array($arResult['OLD_VALUE'][$code]) && in_array($propID,$arResult['OLD_VALUE'][$code]))
								{
									echo ' checked="checked" ';
								}
								elseif($propID == $arResult['OLD_VALUE'][$code])
								{
									echo ' checked="checked" ';
								}
							?> name="<?=$name?>" /><label class="pull-left lh-0 gray-6t" for="<?=$uniq?>"><?=$info['VALUE']?></label>
							<?
						endforeach;

					else:
						?>
						<select class="<?=implode(" ",$arFieldClass)?> field-select" <?=$fieldInfo["MULTIPLE"] == "Y" ? 'multiple="multiple"' : ''?> name="<?=$name?>" data-placeholder="<?=$fieldInfo['NAME']?>"  id="<?=$fieldId?>" <?if($fieldInfo["IS_REQUIRED"]):?>required="required"<?endif;?>>

							<?
							foreach($fieldInfo['ENUM'] as $propID => $info):
								?>
								<option value="<?=$propID?>" <?
									if(is_array($arResult['OLD_VALUE'][$code]) && in_array($info['ID'],$arResult['OLD_VALUE'][$code]))
									{
										echo ' selected="selected" ';
									}
									elseif($info['ID'] == $arResult['OLD_VALUE'][$code])
									{
										echo ' selected="selected" ';
									}?> ><?=$info['VALUE']?></option>
								<?
							endforeach;
							?>
						</select>
						<?
					endif;
				endif;
				break;

			case 'CAPTCHA':
				?>
				<input type="text" name="captcha_word" maxlength="50" value="" class="ciee-captcha-input">
				<img src="/bitrix/tools/captcha.php?captcha_sid=<?=$arResult["CAPTCHA_CODE"]?>" width="180" height="40" alt="CAPTCHA" class="ciee-captcha-image" />
				<input type="hidden" name="captcha_sid" value="<?=$arResult["CAPTCHA_CODE"]?>" /><br/>
				<?
				break;
		endswitch;
	}?>
    <?if (strlen($fieldInfo['TOOLTIP']) > 0):?>
       <small class="ciee-field-tooltip"><?=$fieldInfo['TOOLTIP']?></small>
    <?endif;?>
	</div><!-- .field-input -->
	</div><!-- .field -->


	<?
endforeach;
echo $count_group_fields ? "</div>" : ""; //закроем группу
?>
	<?/* Текст про обязательные поля
	<div class="tooltip-block">
		<span class="required-fields">*</span>
		<span><?=GetMessage('REQUIRED_MESSAGE_LABLE')?></span>
	</div>*/?>

	<div class="field">
		<button type="submit" class="md-button <?=($arParams['AJAX'] == "Y" ? 'bx-js-update-button' : '')?>" name="iblock_submit" value="<?=$arParams['SUBMIT_TEXT']?>"><?=$arParams['SUBMIT_TEXT']?></button>
	</div>



	<div style="display:none">
		<?if(isset($arResult['USER_FIELDS'])):?>
			<?=$arResult['USER_FIELDS']?>
		<?endif;?>
	</div>

	<?if(strlen($arParams['ADDITIONAL_TEXT']) > 0):?>
		<div class="b-additional-text"><?=htmlspecialchars_decode($arParams['ADDITIONAL_TEXT'])?></div>
	<?endif;?>
	<input type="hidden" value="<?=($arResult["FORM_ID"])?>" name="FORM_ID" />
</form>
</div>
<?
$frame->end();
?>
