<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);?>

<?if (!empty($arResult)):?>
<ul class="h-menu">

<?
$previousLevel = 0;
foreach($arResult as $arItem):
	$arItem['TRUNCATE_TEXT'] = strlen($arParams['TRUNCATE_TEXT_LENGTH']) ? TruncateText($arItem["TEXT"], $arParams['TRUNCATE_TEXT_LENGTH']) : $arItem["TEXT"];

	$linkClass = ['h-menu__link'];
	if($arItem['PARAMS']['ICON']) $linkClass[] = '_with-icon';
	?>

	<?if ($previousLevel && $arItem["DEPTH_LEVEL"] < $previousLevel):?>
		<?=str_repeat("</ul></li>", ($previousLevel - $arItem["DEPTH_LEVEL"]));?>
	<?endif?>

	<?if ($arItem["IS_PARENT"]):?>

		<?if ($arItem["DEPTH_LEVEL"] == 1):?>
			<li class="<?if ($arItem["SELECTED"]):?>selected<?endif?>">
				<a href="<?=$arItem["LINK"]?>"
				   class="<?=implode(' ', $linkClass)?>">
					<?if($arItem['PARAMS']['ICON']):?>
						<span class="h-menu__item-icon"><i class="<?=$arItem['PARAMS']['ICON']?>"></i></span>
					<?endif;?>
					<span class="h-menu__item-text">
						<?=$arItem["TRUNCATE_TEXT"]?>
					</span>
				</a>
				<ul class="h-sub-menu theme_hover--bg-color"
				    <?if ($arItem["SELECTED"]):?>style="display:block;"<?endif?>>
		<?else:?>
			<li class="<?if ($arItem["SELECTED"]):?> selected<?endif?>">
				<a href="<?=$arItem["LINK"]?>"
				   class="<?=implode(' ', $linkClass)?>">
					<span class="h-menu__submenu-item-text">
						<?=$arItem["TRUNCATE_TEXT"]?>
					</span>
                    <span class="menu__open-submenu">
						<i class="icon-arrow-right"></i>
					</span>
				</a>
				<ul class="h-sub-menu theme_hover--bg-color">
		<?endif?>

	<?else:?>

		<?if ($arItem["PERMISSION"] > "D"):?>

			<?if ($arItem["DEPTH_LEVEL"] == 1):?>
				<li class="<?if ($arItem["SELECTED"]):?>selected<?endif?>">
					<a
						class="<?=implode(' ', $linkClass)?>"
						href="<?=$arItem["LINK"]?>">
						<?if($arItem['PARAMS']['ICON']):?>
							<span class="h-menu__item-icon"><i class="<?=$arItem['PARAMS']['ICON']?>"></i></span>
						<?endif;?>
						<span class="h-menu__item-text"><?=$arItem["TRUNCATE_TEXT"]?></span>
					</a></li>
			<?else:?>
				<li<?if ($arItem["SELECTED"]):?> class="selected"<?endif?>>
					<a class="<?=implode(' ', $linkClass)?>"
					   href="<?=$arItem["LINK"]?>">
						<span class="h-menu__submenu-item-text"><?=$arItem["TRUNCATE_TEXT"]?></span>
					</a>
				</li>
			<?endif?>

		<?else:?>

			<?if ($arItem["DEPTH_LEVEL"] == 1):?>
				<li><a href="" class="<?if ($arItem["SELECTED"]):?>root-item-selected<?else:?>root-item<?endif?>" title="<?=GetMessage("MENU_ITEM_ACCESS_DENIED")?>"><?=$arItem["TRUNCATE_TEXT"]?></a></li>
			<?else:?>
				<li><a href="" class="denied" title="<?=GetMessage("MENU_ITEM_ACCESS_DENIED")?>"><?=$arItem["TRUNCATE_TEXT"]?></a></li>
			<?endif?>

		<?endif?>

	<?endif?>

	<?$previousLevel = $arItem["DEPTH_LEVEL"];?>

<?endforeach?>

<?if ($previousLevel > 1)://close last item tags?>
	<?=str_repeat("</ul></li>", ($previousLevel-1) );?>
<?endif?>

</ul>
<?endif?>