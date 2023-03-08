<? /**
 * @var $fieldInfo
 */

if (!empty($fieldInfo['ITEMS'])):

	array_push($templateData['cjscore'], 'barrating');

    ?>
    <div class="citrus-form-rating">
        <div class="citrus-form-rating__name">
            <?=$fieldInfo['PLACEHOLDER']?>
        </div>
        <div class="citrus-form-rating__plugin">
            <select style="display: none;" name="<?=$fieldInfo["CODE"]?>" id="<?=$fieldInfo['ID']?>">
                <option value=""></option>
		        <?
		        foreach ($fieldInfo['ITEMS'] as $selectItem):
			        ?>
                    <option
                            value="<?=$selectItem["ID"] ? $selectItem["ID"] : ""?>"
					        <? if ($fieldInfo["OLD_VALUE"] == $selectItem["ID"]): ?>selected="selected"<?endif;?>
                    ><?=$selectItem['VALUE']?></option>
			        <?
		        endforeach;
		        ?>
            </select>
        </div>
    </div>

    <script>
       ;(function(){
	       $('#<?=$fieldInfo['ID']?>').barrating({
		       theme: 'fontawesome-stars',
		       initialRating: null
	       });
	       $('#<?=$fieldInfo['ID']?>').on('reset', function () {
		       $(this).barrating('clear');
	       });
       }());
    </script>
<?endif; ?>

