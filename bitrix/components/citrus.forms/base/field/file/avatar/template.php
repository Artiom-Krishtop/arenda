<?
/**
 * @var $fieldInfo
 */

use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

$accept = array();
if ($fieldInfo['FILE_TYPE']) {
	$accept = array_map(function($fileExt){
		return '.'.trim($fileExt);
	}, explode(',', $fieldInfo['FILE_TYPE']));
}
?>

<div class="file-upload-light" id="<?=$fieldInfo['ID']?>container">
    <div class="file-upload-light__inner _template-avatar">
        <input
            name="<?=$fieldInfo['CODE']?>"
            type="file"
            title="<?=Loc::getMessage('FORM_FILE_SELECT')?>"
            <?if($fieldInfo['MULTIPLE'] == 'Y'):?>multiple="multiple"<?endif;?>
            id="<?=$fieldInfo['ID']?>"
            <?if(!empty($accept)):?>
	            accept="<?=implode(', ', $accept)?>"
            <?endif;?>
            >

        <label
            for="<?=$fieldInfo['ID']?>"
            class="file-upload-light__preview <?=!$fieldInfo['FILES'] ? '_empty': ''?>"
            <?if($fieldInfo['FILES']['SRC']):?>
                style="background-image: url(<?=$fieldInfo['FILES']['SRC']?>);"
            <?endif;?>
        ></label>

        <div class="file-upload-light__description">
            <div class="file-upload-light__description-title"><?=$fieldInfo['PLACEHOLDER'] ? $fieldInfo['PLACEHOLDER'] : ''?></div>
            <div class="file-upload-light__description-content"><?=($fieldInfo['DESCRIPTION'])?></div>
            <label for="<?=$fieldInfo['ID']?>" class="file-upload-light__label"><?=Loc::getMessage("FORM_AVATAR_BTN")?></label>
        </div>
    </div>
</div>

<script>
	;(function(){
		new fileUploadLight($('#<?=$fieldInfo['ID']?>container'));
	}());
</script>

