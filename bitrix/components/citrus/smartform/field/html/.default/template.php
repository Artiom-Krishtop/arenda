<?php

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

?>

<?if(is_array($fieldInfo['OLD_VALUE']) && $fieldInfo['OLD_VALUE']['TYPE']):?>
	<?= Loc::getMessage('CITRUS_SMARTFORM_HTML_TITLE') ?>
<?else:?>
	<textarea class="form-control" cols="10" rows="6" name="<?=$fieldInfo["CODE"]?>"><?=$fieldInfo['OLD_VALUE']?></textarea>
<?endif;?>