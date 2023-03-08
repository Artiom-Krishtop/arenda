<?
/**
 * @var $fieldInfo
 */

use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);
?>

<div class="file-upload-light" id="<?=$fieldInfo['ID']?>container">
    <div class="file-upload-light__inner _template-avatar">
        <input
            name="<?=$fieldInfo['CODE']?>"
            type="file"
            title="Выбрать файл"
            <?if($fieldInfo['MULTIPLE'] == 'Y'):?>multiple="multiple"<?endif;?>
            id="<?=$fieldInfo['ID']?>"
            >

        <? $src = $fieldInfo['FILES']['SRC']?>
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

