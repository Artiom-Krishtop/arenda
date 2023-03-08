<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<script>
	$(function() {
		var FORM_ID = "<?=$arResult["FORM_ID"]?>";
		<?if($arParams["JQUERY_VALID"] == "Y"):?>
			var validator = new citrusValidator($("#"+FORM_ID));
		<?endif;?>
		var isValidator = typeof validator !== "undefined";
		<?if($arParams['AJAX'] == "Y"):?>
			$(document).on('submit', '#'+FORM_ID, function(event) {
				event.preventDefault();
				var form = $(this);

				form.find(":submit").prop("disabled", true);
				$('#'+FORM_ID).addClass('js-loading');
				if (isValidator) validator.isLocked = true;

				$.ajax({
					url: form.prop("action"),
					type: form.prop("method"),
					dataType: 'json',
					data: form.serialize(),
				})
				.done(function(data) {
					//reset form
					if (data.success) {
						if( isValidator ) {
							validator.callEvent("reset");
						} else {
							$("#"+FORM_ID)[0].reset();
						}
					}

					//change captcha
					if(!!data["CAPTCHA_CODE"]) {
						$("#"+FORM_ID+" .js-update-captcha-image").attr("src", "/bitrix/tools/captcha.php?captcha_sid="+data["CAPTCHA_CODE"]);
						$("#"+FORM_ID+" .js-update-captcha-code").val(data["CAPTCHA_CODE"]);
					}
					//focus on captcha
					var captcha_input = $('#'+FORM_ID+' [name = "captcha_word"]');
					if ( isValidator && !!data["message"]["CAPTCHA"]) {
						validator.callEvent("addFieldError", captcha_input, [data["message"]["CAPTCHA"]]);
						delete data["message"]["CAPTCHA"];
						captcha_input.focus();
					}
					captcha_input.val('');

					//add Messages
					if ( !!data["message"] && !$.isEmptyObject(data["message"]) ) {
						var message_block = $("#"+FORM_ID+" .js-form-message");
						message_block.addClass('_show');

						var msgClass = data.success ? "success" : "danger";
						var message = '<div class="message-block bg-'+msgClass+'">';

						if ($.isPlainObject(data.message)) {
							for (key in data.message) {
								message += '<p>'+ data.message[key] + '</p>';
							}
						} else {
							message += '<p>'+ data.message + '</p>';
						}
						message_block.html(message);

						if( $(document).scrollTop() > message_block.offset().top+message_block.outerHeight() ) {
							message_block.focus();
						}
					}
				})
				.fail(function() {
					console.log("error");
				})
				.always(function() {
					form.find(":submit").prop("disabled", false);
					form.removeClass('js-loading')
					if (isValidator) validator.isLocked = false;
				});
			});

			<?if ( (!empty($arResult["MESSAGE"]) || count($arResult["ERRORS_ARRAY"]) ) && !$arParams['AJAX'] ):?>
				$(window).load(function() {
					$('body').animate( { scrollTop: $("#"+FORM_ID).offset().top-100 }, 100 );
				});
			<?endif;?>
		<?endif;?>

		// masket input
		$("#"+FORM_ID).find('[name="PROPERTY[PROPERTY_phone]"]').mask("+7 (999) 999-99-99");
	});
</script>

<form
	id="<?=$arResult["FORM_ID"]?>"
	name="<?=$arResult["FORM_ID"]?>"
	action="<?=$arParams['URL_CUR_PAGE']?>"
	method="post" enctype="multipart/form-data"
	autocomplete="off"
	class="citrus-form"
	>

	<input type="hidden" value="<?=$arResult["FORM_ID"]?>" name="FORM_ID" />

<?if($arParams['FORM_TITLE']):?>
	<div class="form_title">
        <?=htmlspecialchars_decode($arParams['FORM_TITLE'])?>
    </div>
<?endif;?>
<?if($arParams['SUB_TEXT']):?>
	<p class="sub_text"><?=htmlspecialchars_decode($arParams['SUB_TEXT'])?></p>
<?endif;?>

<div class="form-message-block js-form-message <?if($arResult["ERRORS_ARRAY"] || $arResult["MESSAGE"]):?>_show<?endif;?>" tabindex="0">
	<?if (count($arResult["ERRORS_ARRAY"])):?>
	<div class="message-block bg-danger">
		<? foreach ($arResult["ERRORS_ARRAY"] as $key => $error): ?>
			<p><?=$error?></p>
		<? endforeach ?>
	</div>
	<?endif;?>

	<?if (!empty($arResult["MESSAGE"])):?>
		<div class="message-block bg-success">
			<p><?=$arResult["MESSAGE"]?></p>
		</div>
	<?endif;?>
</div><!-- .form-message-block -->

<?
echo bitrix_sessid_post();

$count_group_fields = 0;//счетчик груп

$depthLevel = false;

foreach($arResult["ITEMS"] as $code => $fieldInfo):?>

    <?
    if(isset($fieldInfo['GROUP_FIELD']) && $fieldInfo['DEPTH_LAVEL'] > 0) {
		if(false !== $depthLevel && $depthLevel >= $fieldInfo['DEPTH_LAVEL'])
			echo str_repeat('</div>',$depthLevel - ($fieldInfo['DEPTH_LAVEL'] - 1));

	    echo '<div class="group-f">';
	    $depthLevel = $fieldInfo['DEPTH_LAVEL'];
		continue;
	}
	?>

    <?// название поля
    $name = ($fieldInfo['MULTIPLE'] == "N" ? "PROPERTY[" . $code . "]" : "PROPERTY[" . $code . "][]");?>

    <?if($fieldInfo['HIDE_FIELD']):?>
        <input type="hidden" name="<?=$name?>" value="<?=$arResult['OLD_VALUE'][$code]?>"  />
        <?continue;?>
    <?endif;?>


    <div class="form-group <?if($code !== "PROPERTY_ITEMS"):?>inline<?endif;?> ciee-<?=strtolower($code)?> ciee-field-<?=strtolower($fieldInfo['PROPERTY_TYPE'])?> "><?

	$inputNum = 1;
	if ($fieldInfo['MULTIPLE'] == "Y"
		&& $fieldInfo['PROPERTY_TYPE'] != 'L'
		&& $fieldInfo['PROPERTY_TYPE'] != 'E'
		&& $fieldInfo['PROPERTY_TYPE'] != 'G'
	) {
		$inputNum += $fieldInfo["MULTIPLE_CNT"];
	}

	if(strlen($fieldInfo['NAME']) > 0):?>
		<?if($fieldInfo['PROPERTY_TYPE'] == "E")://для полей типа список?>
			<?$fieldInfo["ENUM"][0]["VALUE"] = $fieldInfo['NAME']; ?>

		<?elseif($fieldInfo['PROPERTY_TYPE'] == "F")://для полей типа файл?>
			<label for="" class="control-label"><?
			echo $fieldInfo['NAME'] . ':';
				if($fieldInfo['IS_REQUIRED'] == "Y")
			{
				?><span class="red">*</span><?
			}
			?>
			</label>
		<?else:?>
		<label for="<?=$code?>" class="field-title"><?
		echo $fieldInfo['NAME'] . ':';
			if($fieldInfo['IS_REQUIRED'] == "Y")
		{
			?><span class="red">*</span><?
		}
		?>
		</label>
		<?endif;?>
	<?
	endif;?>

	<?
		if($fieldInfo['IS_REQUIRED']) {
			$fieldInfo['NAME'] .= "*";
			$fieldInfo['PLACEHOLDER'] = $fieldInfo['PLACEHOLDER'] ? $fieldInfo['PLACEHOLDER']."*" : "";
		}
	?>
	<?if($fieldInfo['PROPERTY_TYPE'] == "L" && $fieldInfo["MULTIPLE"] == "Y" && $fieldInfo['LIST_TYPE'] == 'C'):?><div class="field-form-name"></div>
		<div class="field-input checkbox-0">
	<?else:?>
		<div class="field input-container">
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
				<textarea data-valid="<?=$fieldInfo["VALIDRULE"]?>" class="form-control" cols="<?=$fieldInfo["COL_COUNT"]?>" rows="6" name="<?=$name?>" placeholder="<?=$fieldInfo["PLACEHOLDER"]?>" ><?=$value?></textarea>
				<?
				break;

			case "S":
			case "N":
				switch($fieldInfo['USER_TYPE']):
					case 'DateTime':?>
						<input type="text" name="<?=$name?>" value="<?=$value?>" />&nbsp;
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
						<textarea data-valid="<?=$fieldInfo["VALIDRULE"]?>" class="form-control" cols="10" rows="6" name="<?=$name?>" ><?=$value?></textarea>
				  <?break;

				  default:?>
					<input data-valid="<?=$fieldInfo["VALIDRULE"]?>" class="form-control" type="text" name="<?=$name?>" value="<?=$value?>" placeholder="<?=$fieldInfo['PLACEHOLDER']?>"/>
				 <?endswitch;
			break;

			case 'F':
				?>
				<div class="file_group">
					<label class="btn _gray" for="<?=$code?>">Загрузить файл</label>
					<input data-valid="<?=$fieldInfo["VALIDRULE"]?>" data-valid-params = '<?=stripslashes($fieldInfo["VALID_ERROR_MSG"])?>' class="js_change_label <?=implode(" ",$arFieldClass)?> hidden" type="file" name="<?=$name?>" value="<?=$value?>"  <?if($fieldInfo['MULTIPLE'] == "Y"):?>multiple<?endif;?>/>
				</div>
				<?
				break;

			case "E":
			case "G":
			case 'L':
				if(!empty($fieldInfo['ENUM'])):
					if($fieldInfo['LIST_TYPE'] == 'C'):
						foreach($fieldInfo['ENUM'] as $propID => $info):
							?>

							<input class="<?=implode(" ",$arFieldClass)?>" class="pull-left" type="<?=($fieldInfo["MULTIPLE"] == "Y" ? "checkbox" : "radio")?>" value="<?=$propID?>" <?
								if(is_array($arResult['OLD_VALUE'][$code]) && in_array($propID,$arResult['OLD_VALUE'][$code]))
								{
									echo ' checked="checked" ';
								}
								elseif($propID == $arResult['OLD_VALUE'][$code])
								{
									echo ' checked="checked" ';
								}
							?> name="<?=$name?>" /><label class="pull-left lh-0 gray-6t"><?=$info['VALUE']?></label>
							<?
						endforeach;

					else:
						?>
						<select data-valid="<?=$fieldInfo["VALIDRULE"]?>" class="<?=implode(" ",$arFieldClass)?> field-select" <?=$fieldInfo["MULTIPLE"] == "Y" ? 'multiple="multiple"' : ''?> name="<?=$name?>" data-placeholder="<?=$fieldInfo['PLACEHOLDER']?>"   >

							<?
							foreach($fieldInfo['ENUM'] as $propID => $info):
								?>
								<option value="<?=$propID ? $propID : ""?>" <?
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
				<img src="/bitrix/tools/captcha.php?captcha_sid=<?=$arResult["CAPTCHA_CODE"]?>" width="180" height="40" alt="CAPTCHA" class="ciee-captcha-image js-update-captcha-image" />
				<input data-valid="required" type="text" name="captcha_word" maxlength="50" value="" class="ciee-captcha-input" placeholder="<?=$fieldInfo['PLACEHOLDER']?>">
				<input type="hidden" name="captcha_sid" value="<?=$arResult["CAPTCHA_CODE"]?>" class="js-update-captcha-code"/>
				<?
				break;
		endswitch;
	}?>
	<?if($arParams["JQUERY_VALID"] == "Y"):?>
    <div class="error help-block"></div>
    <?endif;?>
    <?if (strlen($fieldInfo['TOOLTIP']) > 0):?>
       <p class="field-description"><?=$fieldInfo['TOOLTIP']?></p>
    <?endif;?>
	</div><!-- .input-container -->
	</div><!-- .form-group -->


<?
endforeach;
		if(false !== $depthLevel)
			echo str_repeat('</div>',$depthLevel);
	?>

	<div class="form-group tooltip-block">
		<span class="red">*</span>
		<span><?=GetMessage('REQUIRED_MESSAGE_LABLE')?></span>
	</div>

	<div class="form-group">
		<div class="input-container">
			<button type="submit" class="btn btn-primary <?=($arParams['AJAX'] == "Y" ? 'bx-js-update-button' : '')?>" name="iblock_submit" value="<?=$arParams['SUBMIT_TEXT']?>"><?=$arParams['SUBMIT_TEXT']?></button>
		</div>
	</div>

	<div style="display:none">
		<?if(isset($arResult['USER_FIELDS'])):?>
			<?=$arResult['USER_FIELDS']?>
		<?endif;?>
	</div>

	<?if(strlen($arParams['ADDITIONAL_TEXT']) > 0):?>
		<div class="b-additional-text"><?=htmlspecialchars_decode($arParams['ADDITIONAL_TEXT'])?></div>
	<?endif;?>

</form>
