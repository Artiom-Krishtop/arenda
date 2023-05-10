<? /**
 * @var $fieldInfo
 */

echo "sdv333sdvs";

if (!empty($fieldInfo['ITEMS'])):

	if ($fieldInfo['LIST_TYPE'] == 'C'):?>
		<div class="material_checkbox">
			<?
			foreach ($fieldInfo['ITEMS'] as $selectItem):
				$propID = $selectItem['ID'];

				$itemId = $arResult["FORM_ID"] . $fieldInfo["CODE"] . "_" . $propID;
				?>
				<input type="<?=($fieldInfo["MULTIPLE"] == "Y" ? "checkbox" : "radio")?>" value="<?=$selectItem["ID"]?>" <?
				if(
					is_array($fieldInfo['OLD_VALUE'])
					&& in_array($propID, $fieldInfo['OLD_VALUE'])
				) {
					echo ' checked="checked" ';
				}
				elseif ($propID == $fieldInfo['OLD_VALUE']) {
					echo ' checked="checked" ';
				}
				?>
					name="<?=$fieldInfo["CODE"]?>"
					id="<?=$itemId?>"
				/>
				<label for="<?=$itemId?>"><?=$selectItem['VALUE']?></label>
				<?
			endforeach; ?>
		</div>
		<?
	else:
		?>
		<?
		$fieldInfo['PLACEHOLDER'] = strlen($fieldInfo['PLACEHOLDER']) ? $fieldInfo['PLACEHOLDER'] : $fieldInfo['ITEMS'][0]["VALUE"];
		if ($fieldInfo["MULTIPLE"] == "Y") {
			unset($fieldInfo['ITEMS'][0]);
		}
		else {
			$fieldInfo['ITEMS'][0]["VALUE"] = $fieldInfo['PLACEHOLDER'];
		}
		?>
		<select class="field-select form-control" <?=$fieldInfo["MULTIPLE"] == "Y" ? 'multiple="multiple"' : ''?> name="<?=$fieldInfo["CODE"]?>" title="<?=$fieldInfo['PLACEHOLDER']?>" data-width="100%">
			<?
			foreach ($fieldInfo['ITEMS'] as $selectItem):
				?>
				<option
						value="<?=$selectItem["ID"] ? $selectItem["ID"] : ""?>"
						<? if ($fieldInfo["OLD_VALUE"] == $selectItem["ID"]): ?>selected="selected"<?endif;
				?>
						<? if (!$selectItem["ID"]): ?>class="default_value"<?endif;
				?>
				><?=$selectItem['VALUE']?></option>
				<?
			endforeach;
			?>
		</select>
		<?
	endif;
endif; ?>

