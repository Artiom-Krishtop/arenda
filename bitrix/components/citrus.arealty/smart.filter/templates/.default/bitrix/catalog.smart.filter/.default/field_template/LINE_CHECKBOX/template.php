<?
/**
 * Шаблон переключателей в виде кнопок
 * @var $arItem;
 */
?>

<div class="citrus-sf-label"
     onclick="smartFilter.toggleValues(this, event)">
    <span class="citrus-sf-label_name"><?=$arItem["NAME"]?>
    </span><?=$arItem['HINT'] ? ', '.$arItem['HINT'] : ''?>
    <span class="citrus-sf-label_value"></span>
    <span class="citrus-sf-label_close"><i class="icon-close" aria-hidden="true"></i></span>
</div>

<div class="citrus-sf-values">
    <div class="line-checkbox">
        <? foreach ($arItem["VALUES"] as $val => $ar):?>
        <label class="line-checkbox__item <?=$ar["DISABLED"] ? 'disabled' : '' ?> <?=($ar["DISABLED"] && !$ar["CHECKED"]) ? 'no-clicked' : '' ?>"
               onclick="smartFilter.clickLabel(event, this)">
            <input
                class="line-checkbox__item-input"
                type="checkbox"
                value="<? echo $ar["HTML_VALUE"] ?>"
                name="<? echo $ar["CONTROL_NAME"] ?>"
                id="<? echo $ar["CONTROL_ID"] ?>"
                <? echo $ar["CHECKED"] ? 'checked="checked"' : '' ?>
                data-name="<?=$ar["VALUE"];?>"
            />
            <span class="line-checkbox__item-label no-select">
                <?=$ar["VALUE"];?>
            </span>
        </label>
        <?endforeach;?>
    </div>
</div><!-- .citrus-sf-values -->