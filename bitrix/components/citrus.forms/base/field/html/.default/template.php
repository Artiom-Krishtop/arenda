<?/**
 * @var $fieldInfo
 */
?>

<?if(is_array($fieldInfo['OLD_VALUE']) && $fieldInfo['OLD_VALUE']['TYPE']):?>
	Подключить визуальный редактор!
<?else:?>
	<textarea placeholder="<?=$fieldInfo['PLACEHOLDER']?>" class="form-control" cols="10" rows="6" name="<?=$fieldInfo["CODE"]?>" maxlength="355"><?=$fieldInfo['OLD_VALUE']?></textarea>
<?endif;?>