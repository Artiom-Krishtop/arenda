<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
use Bitrix\Main\Localization\Loc;

$this->setFrameMode(true);

if (empty($arResult)) return;
?>
<div class="f-menu">
	<div class="f-menu-item">
<?
$previousLevel = 0;
$parentLink = '';
$subItemsCounter = 0;
$maxSubmenuItems = 8;
$skipSubItems = false;
foreach($arResult as $arItem):?>
	<?if ($previousLevel && $arItem["DEPTH_LEVEL"] < $previousLevel):	?>
			</div><!-- .f-menu-item__submenu -->
		</div><!-- .f-menu-item -->
		<div class="f-menu-item">
	<?endif?>

	<?if($previousLevel && $arItem['PARAMS']['SUBMENU']):?>
		</div><!-- .f-menu-item -->
		<div class="f-menu-item">
	<?endif;?>

	<?if($arItem['DEPTH_LEVEL'] === 1):?>
		<?
		$skipSubItems = false;
		$subItemsCounter = 0;
		$parentLink = $arItem["LINK"];?>
		<a class="f-menu-item__title" href="<?=$arItem["LINK"]?>">
			<?=$arItem["TEXT"]?>
		</a>
	<?else:?>
		<? if($skipSubItems) continue;?>
		<?if(++$subItemsCounter === $maxSubmenuItems):?>
			<div class="f-menu-item__submenu-item _more">
				<a href="<?=$parentLink?>"><?= Loc::getMessage("FOOTER_MENU_MORE_LINK") ?></a>
			</div>
		<?endif;?>
		<?if($subItemsCounter>$maxSubmenuItems-1) continue;?>

		<div class="f-menu-item__submenu-item">
			- <a href="<?=$arItem["LINK"]?>"><?=$arItem["TEXT"]?></a>
		</div>
	<?endif;?>


	<?if ($arItem["IS_PARENT"] && $arItem['PARAMS']['SUBMENU']):?>
	<div class="f-menu-item__submenu">
	<?endif;?>

	<?$previousLevel = $arItem["DEPTH_LEVEL"];?>

	<?if($arItem["IS_PARENT"] && !$arItem['PARAMS']['SUBMENU'])
		$skipSubItems = true;?>
<?endforeach;?>

	<?if ($previousLevel > 1)://close last item tags?>
		</div><!-- .f-menu-item__submenu -->
	<?endif?>
	</div><!-- .f-menu-item -->

</div><!-- .f-menu -->

