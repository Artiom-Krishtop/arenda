<?
/**
 * Шаблон Выпадающего списка. Если элементов больше 10 то появляется строка поиска.
 * @var $arItem;
 */

use Bitrix\Main\Localization\Loc;
?>
<div class="citrus-sf-label"
     onclick="smartFilter.toggleValues(this, event)">
    <span class="citrus-sf-label_name"><?=$arItem["NAME"]?>
    </span><?=$arItem['HINT'] ? ', '.$arItem['HINT'] : ''?>
    <span class="citrus-sf-label_value"></span>
    <span class="citrus-sf-label_close"><i class="icon-close" aria-hidden="true"></i></span>
</div>

<div class="citrus-sf-values">
    <div class="citrus-select">
        <?if(count($arItem["VALUES"]) > 10):?>
            <div class="citrus-select__search">
                <input
                    type="text"
                    class="citrus-select__search-input"
                    onkeyup="smartFilter.searchDropdownItem(event, this)"
                    placeholder="<?=Loc::getMessage('CITRUS_FILTER_DROPDOWN_SEARCH_PLACEHOLDER')?>"
                >
                <i class="citrus-select__search-icon fa fa-search" aria-hidden="true"></i>
	            <a href="javascript:void(0);" class="citrus-select__chose-all hidden" onclick="smartFilter.choseAllDropdownItems(event, this)"><?=Loc::getMessage('CITRUS_FILTER_DROPDOWN_SEARCH_CHOSE_ALL')?></a>
            </div>
        <?endif;?>
        <div class="citrus-select__items-wrapper">
        <? foreach ($arItem["VALUES"] as $val => $ar): ?>
        <label
                class="citrus-select__item <?=$ar["DISABLED"] ? 'disabled' : '' ?> <?=($ar["DISABLED"] && !$ar["CHECKED"]) ? 'no-clicked' : '' ?>"
                onclick="smartFilter.clickLabel(event, this)">
            <input
                    class="citrus-select__item-input"
                    type="checkbox"
                    name="<?=$ar["CONTROL_NAME"]?>"
                    id="<?=$ar["CONTROL_ID"]?>"
                    value="<? echo $ar["HTML_VALUE"] ?>"
                    <?=$ar["CHECKED"] ? 'checked="checked"' : '' ?>
                    data-name="<?=$ar["VALUE"]?>"
            />
            <span class="filter-checkmark"></span>
            <span class="citrus-select__item-name no-select"><?=$ar["VALUE"]?></span>
        </label>
        <?endforeach;?>
        </div>
    </div>
</div><!-- .citrus-sf-values -->