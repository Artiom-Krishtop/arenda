<? /**
 * @var $fieldInfo
 */

array_push($templateData['cjscore'], 'bootstrap_select');

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

<script>
	;(function(){
		var $el = $("#<?=$fieldInfo["ID"]?>");
		$el.selectpicker({
			styleBase: '',
			style: '',
			tickIcon: 'fa fa-check',
		});
		$(document).on('reset', '#<?=$arResult["FORM_ID"]?>', function(e){
			$el.val('default');
			$el.selectpicker("refresh");
		});
	}());
</script>
