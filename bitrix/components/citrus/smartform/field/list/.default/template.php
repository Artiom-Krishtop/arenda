<? /**
 * @var $fieldInfo
 */
if (!empty($fieldInfo['ITEMS'])):
		$fieldInfo['PLACEHOLDER'] = strlen($fieldInfo['PLACEHOLDER']) ? $fieldInfo['PLACEHOLDER'] : $fieldInfo['ITEMS'][0]["VALUE"];
		if ($fieldInfo["MULTIPLE"] == "Y") {
			unset($fieldInfo['ITEMS'][0]);
		} else {
		    $firstItem = &$fieldInfo['ITEMS'][0];
			$firstItem["VALUE"] = $fieldInfo['PLACEHOLDER'];
			$firstItem["PLACEHOLDER"] = $fieldInfo['PLACEHOLDER'];
            if ($fieldInfo["IS_REQUIRED"] == "Y" ) $firstItem['PLACEHOLDER'] .= "<span class='starrequired'>*</span>";
		}

	    if ($fieldInfo["IS_REQUIRED"] == "Y") $fieldInfo['PLACEHOLDER'] .= "<span class='starrequired'>*</span>";
		?>
		<select 
            id="<?=$fieldInfo["ID"]?>"
            class="field-select form-control" 
            <?=$fieldInfo["MULTIPLE"] == "Y" ? 'multiple="multiple"' : ''?> 
            name="<?=$fieldInfo["CODE"]?>" 
            title="<?=$fieldInfo['PLACEHOLDER']?>"
            data-width="100%"
            <?if($fieldInfo["USER_TYPE"] === "EAutocomplete"):?>data-live-search="true"<?endif;?>
        >
			<?
			foreach ($fieldInfo['ITEMS'] as $selectItem):
				?>
				<option
                    value="<?=$selectItem["ID"] ? $selectItem["ID"] : ""?>"
                    <? if ($fieldInfo["OLD_VALUE"] == $selectItem["ID"]): ?>selected="selected"<?endif;?>
                    <? if (!$selectItem["ID"]): ?>class="default_value"<?endif;?>
                    data-content="<?=$selectItem["PLACEHOLDER"]?>"
				><?=$selectItem['VALUE']?></option>
				<?
			endforeach;
			?>
		</select>
		<? CJSCore::Init('bootstrap_select')?>
        <script>
	        cui_form.initPlugins($("#<?=$fieldInfo["ID"]?>"), "selectpicker");
	        $(document).on('reset', '#<?=$arResult["FORM_ID"]?>', function(e){
		        $("#<?=$fieldInfo["ID"]?>").val('default');
		        $("#<?=$fieldInfo["ID"]?>").selectpicker("refresh");
	        });
        </script>
		<?
endif; ?>

