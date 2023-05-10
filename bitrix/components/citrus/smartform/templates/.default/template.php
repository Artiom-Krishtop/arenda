<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<div class="bx-urf-wrapp">

<?if(strlen($arParams['FORM_TITLE']) > 0):?>
	<div class="bx-urf-title"><?=$arParams['FORM_TITLE']?></div>
<?endif;?>

<?if(strlen($arParams['BEFORE_FORM_TOOLTIP']) > 0):?>
	<div class="bx-urf-tooltip"><?=htmlspecialchars_decode($arParams['BEFORE_FORM_TOOLTIP'])?></div>
<?endif;?>

<?if(isset($arResult['ERRORS'])):?>
	<?ShowError(htmlspecialchars_decode(implode("\n\r\t", $arResult['ERROR_TITLE'])));?>
	<?ShowError(htmlspecialchars_decode(implode("\n\r\t", $arResult['ERRORS'])));?>
<?endif;?>

<?if(array_key_exists('saccess', $_REQUEST)):?>
	<?ShowMessage(Array("TYPE"=>"OK", "MESSAGE"=> strlen($arParams['SUCCESS_TEXT']) > 0 ? htmlspecialchars_decode($arParams['SUCCESS_TEXT']) : GetMessage('TPL_URF_OK_MESSAGE')));?>
<?endif;?>

<?if(empty($arResult['ITEMS'])):?>
	<?ShowError(GetMessage('TPL_USER_REG_FORM_EMPTY_FIELDS_LIST'));?>
<?else:?>
	<form id="<?=$arResult['FORM_ID']?>" name="<?=$arResult['FORM_ID']?>" action="<?=$arResult['FORM_ACTIONS']?>" method="POST" enctype="multipart/form-data" >
	<?=bitrix_sessid_post();?>
	<input type="hidden" value="<?=$arResult['FORM_ID']?>" name="FORM_ID" />
	<?foreach($arResult['ITEMS'] as $code => $arField):
		$name = $arField['CODE'];
		$oldValue = isset($arField['OLD_VALUE']) ? $arField['OLD_VALUE'] : "";

		if($arField['HIDE_FIELD']):?>
			<input type="hidden" name="<?=$name?>" size="25" value="<?=$oldValue?>" id="<?=$code?>" />
		<?
			continue;
		endif;?>

		<?if(array_key_exists('IS_FIELD', $arField) && !$arField['IS_FIELD']):?>
		<div class="bx-urf-itemgroup-wrapp">
			<div class="bx-urf-itemgroup-title"><?=$arField['TITLE']?></div>
			<?if(strlen($arField['TOOLTIP']) > 0):?>
			<div class="bx-urf-itemgroup-tooltip"><?=$arField['TOOLTIP']?></div>
			<?endif;?>
		</div>
		<?else:?>
		<div class="bx-urf-item-wrapp <?=($arField['HIDE_FIELD'] ? 'bx-urf-item-wrapp-disable' : '')?> __<?=(ToLower($code))?>">
			<div class="bx-urf-item-title"><?=$arField['TITLE']?><?if($arField['IS_REQUIRED']):?><span class="bx-urf-item-required">*</span><?endif;?></div>
			<div class="bx-urf-item-val">
				<div>
				<?switch($arField['TYPE']):
					case 'F':
						echo CFile::InputFile(
							$name, // Название поля для ввода файла
							20, // Ширина поля для ввода файла
							$oldValue, //Цифровой идентификатор существующего файла
							"/upload/", //Путь к папке от корня сайта в которой хранятся файлы. Например: "/upload/iblock/"
							0, // Максимальный размер файла (байт)
							"IMAGE"
						);
						/*
						$APPLICATION->IncludeComponent(
							"bitrix:main.file.input",
							"",
							array(
								"MODULE_ID" => "main",
								"ALLOW_UPLOAD" => "I",
								"INPUT_NAME_UNSAVED" => $code,
								"INPUT_NAME" => $code,
								"INPUT_CAPTION" => GetMessage('TPL_UR_PHOTO_ADD_BUTTON'),
								"MULTIPLE" => "N",
							),
							$component,
							array("HIDE_ICONS"=>"Y")
						);*/
					break;

					case 'DATE':?>
						<input type="text" name="<?=$name?>" size="25" value="<?=$oldValue?>" id="<?=$code?>" />&nbsp;
						<?$APPLICATION->IncludeComponent(
							'bitrix:main.calendar',
							'',
							array(
								'SHOW_INPUT' => "N",
								'FORM_NAME' => $arResult['FORM_ID'],
								'INPUT_NAME' => $name,
								'INPUT_VALUE' => $oldValue,
								'SHOW_TIME' => "N",
 								'HIDE_TIMEBAR' => "Y"
							),
							$component,
							array('HIDE_ICONS' => 'Y')
						);
					break;

					case 'USER_FIELD':
						$APPLICATION->IncludeComponent(
							"bitrix:system.field.edit",
							$arField["USER_TYPE_ID"],
							array("bVarsFromForm" => false, "arUserField" => $arField),
							$component,
							array("HIDE_ICONS"=>"Y")
						);
					break;

					case 'CAPTCHA':?>
						<input type="hidden" name="captcha_sid" value="<?=$arResult["CAPTCHA_CODE"]?>" />
						<img src="/bitrix/tools/captcha.php?captcha_sid=<?=$arResult["CAPTCHA_CODE"]?>" width="180" height="40" alt="CAPTCHA" class="ciee-captcha-image" />
						<input type="text" name="captcha_word" maxlength="6" value="" class="ciee-captcha-input">
						<div class="bx-urf-clearfix"></div>
					<?break;

					case 'L':
						if(!empty($arField['ITEMS'])):?>
							<select id="<?=$code?>" name="<?=$name?>" size="3">
	  						<?foreach($arField['ITEMS'] as $key => $value):?>
								<option value="<?=$key?>" <?=($oldValue == $key ? 'selected="selected"' : '')?>><?=$value?></option>
							<?endforeach;?>
							</select>
						<?endif;
  					break;

					case 'S':
					default:?>
						<input <?=(isset($arField['IS_PASSWORD']) && $arField['IS_PASSWORD'] ? ' type="password"' : ' type="text" ')?>id="<?=$code?>" name="<?=$name?>" value="<?=$oldValue?>" class="" />
				<?endswitch;?>
				</div>
				<?if(strlen($arField['TOOLTIP']) > 0):?>
				<div class="bx-urf-item-tooltip"><?=$arField['TOOLTIP']?></div>
				<?endif;?>
			</div>
			<div class="bx-urf-clearfix"></div>
		</div>
		<?endif;?>
	<?endforeach;?>
		<div class="bx-urf-btn">
			<input type="submit" name="save" value="<?=(strlen($arParams['BUTTON_TITLE']) > 0 ? htmlspecialchars_decode($arParams['BUTTON_TITLE']) : GetMessage('TPL_UR_BUTTON_TITLE'))?>" />
		</div>
	</form>

	<?if(strlen($arParams['AFTER_FORM_TOOLTIP']) > 0):?>
		<div class="bx-urf-tooltip"><?=htmlspecialchars_decode($arParams['AFTER_FORM_TOOLTIP'])?></div>
	<?endif;?>
<?endif;?>
</div>
