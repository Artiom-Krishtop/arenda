<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();
?>
<div class="bx-interface-toolbar btn-row btn-row--xs-center">
    <?foreach($arParams["BUTTONS"] as $index=>$item):?>
        <?
            $item['TYPE'] = $item['TYPE'] ? $item['TYPE'] : 'toolbar-btn';
            $arClass = array('btn btn-stretch', $item['TYPE'], 'bx-interface-toolbar--link');
            if ($item['RIGHT']) $arClass[] = '_right';
            if ($item['ADDITIONAL_CLASS']) $arClass[] = $item['ADDITIONAL_CLASS'];
        ?>
        <a href="<?=$item["LINK"]?>" title="<?=$item["TITLE"]?>" <?=$item["LINK_PARAM"]?> class="<?=implode(' ',$arClass)?>">
            <?if($item['ICON']):?><span class="bx-interface-toolbar--link-icon <?=$item['ICON']?>"></span><?endif;?>
            <?if($item['MOBILE_TEXT']):?>
                <span class="bx-interface-toolbar--link-text display-xs-n display-sm-ib"><?=$item["TEXT"]?></span>
                <span class="bx-interface-toolbar--link-text display-sm-n"><?=$item['MOBILE_TEXT']?></span>
            <?else:?>
                <span class="bx-interface-toolbar--link-text"><?=$item["TEXT"]?></span>
            <?endif;?>
        </a>
    <?endforeach;?>
</div>
