<? /**
 * @var $fieldInfo
 */

if (!empty($fieldInfo['ITEMS'])):

	array_push($templateData['cjscore'], 'bootstrap_select');

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
		<?
endif; ?>

