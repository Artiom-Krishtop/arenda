<? /**
 * @var $fieldInfo
 */
if (!empty($fieldInfo['ITEMS'])):?>
		<?$fieldInfo['PLACEHOLDER'] = strlen($fieldInfo['PLACEHOLDER']) ? $fieldInfo['PLACEHOLDER'] : $fieldInfo['ITEMS'][0]["VALUE"];
		if ($fieldInfo["MULTIPLE"] == "Y") {
			unset($fieldInfo['ITEMS'][0]);
		}
		else {
			$fieldInfo['ITEMS'][0]["VALUE"] = $fieldInfo['PLACEHOLDER'];
		}?>
		<select id="<?=$fieldInfo["ID"]?>" class="field-select form-control" <?=$fieldInfo["MULTIPLE"] == "Y" ? 'multiple="multiple"' : ''?> name="<?=$fieldInfo["CODE"]?>" title="<?=$fieldInfo['PLACEHOLDER']?>" data-width="100%" data-live-search="true">
			<?
			foreach ($fieldInfo['ITEMS'] as $selectItem):?>
				<option
						value="<?=$selectItem["ID"] ? $selectItem["ID"] : ""?>"
						<? if ($fieldInfo["OLD_VALUE"] == $selectItem["ID"]): ?>selected="selected"<?endif;?>
						<? if (!$selectItem["ID"]): ?>class="default_value"<?endif;?>
				><?=$selectItem['VALUE']?></option>
			<?
			endforeach;
			?>
		</select>
<?endif; ?>

<? CJSCore::Init('bootstrap_select')?>
<script>cui_form.initPlugins($("#<?=$fieldInfo["ID"]?>"), "selectpicker");</script>
