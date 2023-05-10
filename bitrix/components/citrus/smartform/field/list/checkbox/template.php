<? /**
 * @var $fieldInfo
 */

if (!empty($fieldInfo['ITEMS'])):?>

    <?if($fieldInfo["MULTIPLE"] === "Y"):?>
        <div class="cui-checkbox-group checkbox-count-<?=count($fieldInfo['ITEMS'])?>">
            <?foreach ($fieldInfo['ITEMS'] as $selectItem):?>
                <? if (!$selectItem["ID"]) continue;
                $is_checked = is_array($fieldInfo['OLD_VALUE']) && in_array($selectItem['ID'], $fieldInfo['OLD_VALUE']);?>
                <label class="cui-checkbox__label">
                    <input
                            type="checkbox"
                            class="cui-checkbox__input"
                            name="<?=$fieldInfo["CODE"]?>"
                            value="<?=$selectItem["ID"]?>"
                            <?if($is_checked):?>checked="checked"<?endif;?>
                    >
                    <span class="cui-checkbox__checkmark"></span>
                    <span class="cui-checkbox__label-text"><?=$selectItem['VALUE']?></span>
                </label>
            <?endforeach;?>
        </div>
    <? else: ?>
        <div class="cui-radio-group">
            <?foreach ($fieldInfo['ITEMS'] as $selectItem):?>
                <? $is_checked = $selectItem['ID'] === $fieldInfo['OLD_VALUE'];?>
                <label class="cui-radio__label">
                    <input
                            type="radio"
                            name="<?=$fieldInfo["CODE"]?>"
                            class="cui-radio__input"
                            value="<?=$selectItem["ID"]?>"
                            <?if($is_checked):?>checked="checked"<?endif;?>
                    >
                    <span class="cui-radio__checkmark"></span>
                    <span class="cui-radio__label-text"><?=$selectItem['VALUE']?></span>
                </label>
            <?endforeach;?>
        </div>
    <?endif;?>

<?endif;?>
