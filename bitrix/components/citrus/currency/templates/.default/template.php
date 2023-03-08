<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);

use Bitrix\Main\Localization\Loc;
?>

<?if(count($arResult['ITEMS']) > 1):?>
<div class="header-currency">
	<div class="header-currency-label"><?=Loc::getMessage("CURRENCY_SELECT_TITLE")?></div>
	<div class="header-currency-dropdown">
		<button class="btn js-open-dropdown">
			<span class="btn-label">
				<span class="dropdown-current" data-currency-icon=""></span>
			</span>
			<span class="btn-icon fa fa-sort"></span>
		</button>
		<div class="dropdown-select" data-toggle="dropdown-select">
			<?php foreach ($arResult["ITEMS"] as $curr)
			{
				?>
				<div class="js-citrus-arealty-currency dropdown-option" title="<?= $curr["NAME"] ?>">
					<span class="btn-icon"
					      data-icon-position="after"
					      data-currency-fixed="true"
					      data-currency-icon="<?= $curr["SIGN"] ?>"
						  data-currency="<?=$curr["CODE"]?>"></span>
					<span class="fa fa-check currency-selected-icon"></span>
				</div>
				<?php
			} ?>
		</div>
	</div>
</div>
<?endif;?>
<script>
	var currency = new Currency(<?=\Bitrix\Main\Web\Json::encode(array(
		'base' => $arParams['BASE'],
		'items' => $arResult['ITEMS'],
		'factors' => $arResult['FACTORS']
	))?>);
</script>