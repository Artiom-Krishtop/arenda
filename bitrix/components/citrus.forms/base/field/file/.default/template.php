<?
/**
 * @var $fieldInfo
 */

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

$accept = array();
if ($fieldInfo['FILE_TYPE'])
{
	$accept = array_map(function ($fileExt) {
		return '.' . trim($fileExt);
	}, explode(',', $fieldInfo['FILE_TYPE']));
}
?>

<div class="file-upload-light" id="<?=$fieldInfo['ID']?>container">
    <div class="file-upload-light__inner">
        <input
                name="<?=$fieldInfo['CODE']?>"
                type="file"
                title="<?=Loc::getMessage('FORM_FILE_SELECT')?>"
				<? if ($fieldInfo['MULTIPLE'] == 'Y'): ?>multiple="multiple"<? endif; ?>
                id="<?=$fieldInfo['ID']?>"
			<? if (!empty($accept)): ?>
                accept="<?=implode(', ', $accept)?>"
			<? endif; ?>
        >

        <div class="file-upload-light__description">
            <label for="<?=$fieldInfo['ID']?>"
                   class="file-upload-light__label btn btn-secondary"><?=Loc::getMessage("FORM_FILE_BTN")?></label>
            <div class="file-upload-light__description-content"><?=($fieldInfo['DESCRIPTION'])?></div>
        </div>
    </div>
</div>

