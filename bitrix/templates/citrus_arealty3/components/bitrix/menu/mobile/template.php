<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);
use Bitrix\Main\Localization\Loc;
?>

<?if (!empty($arResult)):?>
<ul class="mobile-menu">

<?
$previousLevel = 0;
foreach($arResult as $arItem):
	$arItem['TRUNCATE_TEXT'] = strlen($arParams['TRUNCATE_TEXT_LENGTH']) ? TruncateText($arItem["TEXT"], $arParams['TRUNCATE_TEXT_LENGTH']) : $arItem["TEXT"];

	$liClass = ['mobile-menu__li', '_lvl-'.$arItem['DEPTH_LEVEL']];
	if ($arItem["IS_PARENT"]) $liClass[] = '_parent';
	if ($arItem["SELECTED"]) $liClass[] = '_selected';
	/*if ($arItem["IS_PARENT"] && $arItem["SELECTED"]) $liClass[] = '_open';*/
	?>

	<?if ($previousLevel && $arItem["DEPTH_LEVEL"] < $previousLevel):?>
		<?=str_repeat("</ul></li>",
			($previousLevel - $arItem["DEPTH_LEVEL"]));?>
	<?endif?>

	<?if ($arItem["PERMISSION"] > "D"):?>
		<li class="<?=implode(' ', $liClass)?>">
			<a class="mobile-menu__link <?if($arItem['PARAMS']['ICON']):?>_with-icon _primary<?endif;?>"
			   href="<?=$arItem["LINK"]?>">
				<?if($arItem['PARAMS']['ICON']):?>
				    <span class="mobile-menu__link-icon">
					    <i class="<?=$arItem['PARAMS']['ICON']?>"></i>
				    </span>
				<?endif;?>
				<span class="mobile-menu__link-text">
					<?=$arItem["TRUNCATE_TEXT"]?>
				</span>

				<?if ($arItem["IS_PARENT"]):?>
					<span class="mobile-menu__open-submenu">
						<i class="icon-arrow-right"></i>
					</span>
				<?endif?>
			</a>
			<?if ($arItem["IS_PARENT"]):?>
				<ul class="mobile-menu__sub-menu" <?if($arItem["SELECTED"]):?>
				    style="display: block;"
				<?endif;?>>
                    <li class="back_link <?=implode(' ', $liClass)?>">
                        <a href="javascript:void(0)" class="mobile-menu__link">
                            <span class="mobile-menu__back">
                                <i class="icon-arrow-left"></i>
                            </span>
                            <span class="mobile-menu__link-text"><?= Loc::getMessage("MENU_BACK_LVL_LINK") ?></span>
                        </a>
                    </li>
			<?endif;?>
	<?endif;?>
	<?$previousLevel = $arItem["DEPTH_LEVEL"];?>
<?endforeach?>

<?if ($previousLevel > 1)://close last item tags?>
	<?=str_repeat("</ul></li>", ($previousLevel-1) );?>
<?endif?>

<?php
$frame = new \Bitrix\Main\Page\FrameBuffered("menu_lk_link");
$frame->begin();
if ($arParams['IS_AUTHORIZED'])
{
	?>
	<li class="mobile-menu__li _lvl-1">
		<a class="mobile-menu__link _with-icon _primary" href="<?=SITE_DIR?>account/">
		    <span class="mobile-menu__link-icon">
			    <i class="icon-user"></i>
		    </span>
			<span class="mobile-menu__link-text">
				<?= Loc::getMessage("MENU_KABINET_LINK") ?>
			</span>
		</a>
	</li>
	<li class="mobile-menu__li _lvl-1">
		<a class="mobile-menu__link _with-icon" href="?logout=yes">
		    <span class="mobile-menu__link-icon">
			    <i class="icon-close"></i>
		    </span>
			<span class="mobile-menu__link-text">
				<?= Loc::getMessage("MENU_LOGOUT_LINK") ?>
			</span>
		</a>
	</li>
	<?php
}
else
{
	?>
	<li class="mobile-menu__li _lvl-1">
		<a class="mobile-menu__link _with-icon _primary" href="<?=SITE_DIR?>auth/">
		    <span class="mobile-menu__link-icon">
			    <i class="icon-user"></i>
		    </span>
			<span class="mobile-menu__link-text">
				<?= Loc::getMessage("MENU_LOGIN_LINK") ?>
			</span>
		</a>
	</li>
	<?php
}
$frame->end();?>

</ul>
<?endif?>