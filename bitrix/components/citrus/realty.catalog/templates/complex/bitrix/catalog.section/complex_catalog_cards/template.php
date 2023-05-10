<?php

use Bitrix\Main\Localization\Loc,
    Citrus\Arealty;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$this->setFrameMode(true);

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
/** @var CBitrixComponent $component */ ?>

<?
if ($arResult["DESCRIPTION"])
{
	?>
	<div class="section-description"><?=$arResult["DESCRIPTION"]?></div><?
}

if (empty($arResult['ITEMS']))
{
	ShowNote($arParams['EMPTY_LIST_MESSAGE'] ? $arParams['EMPTY_LIST_MESSAGE'] : GetMessage("CITRUS_REALTY_NO_OFFERS"));
	return;
}

if ($arParams["DISPLAY_TOP_PAGER"])
{
	?><?=$arResult["NAV_STRING"]?><?
}

$strElementEdit = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_EDIT");
$strElementDelete = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_DELETE");
$arElementDeleteParams = array("CONFIRM" => GetMessage("CITRUS_REALTY_DELETE_CONFIRM"));

?>
	<div class="catalog catalog-th-large">
        <div class="row fl-s">
<?

$printProp = function ($propertyCode, $emptyPlaceholder = '&mdash;') use (&$arItem)
{
	$value = $arItem["PROPERTIES"][$propertyCode]["VALUE"];
	if (empty($value))
	{
		return $emptyPlaceholder;
	}

	if (!is_array($value))
		$value = array($value);

	return implode(', ', $value);
};
foreach ($arResult['ITEMS'] as $key => $arItem)
{
	$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], $strElementEdit);
	$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], $strElementDelete, $arElementDeleteParams);

	$offerFields = isset($arResult['OFFERS_FIELDS'][$arItem['XML_ID']]) ? $arResult['OFFERS_FIELDS'][$arItem['XML_ID']] : null;

	$preview = Arealty\Helper::resizeOfferImage($arItem, 250, 225);
	if (!$preview) $preview = array( "src" => SITE_TEMPLATE_PATH."/images/no-photo.png");
	$id = $this->GetEditAreaId($arItem["ID"]);
	?>
	<div class="col-sm-6 col-md-4 col-lg-3 col-dt-1-5 va-xs-t catalog-item" id="<?=$id?>">

        <div class="catalog-item__preview-wrapper">
            <a class="catalog-item-preview" href="<?=$arItem["DETAIL_PAGE_URL"]?>">
                <?if( Arealty\Helper::getModuleOption("lazyload") == "Y"):?>
                    <span data-lazyload="<?=$preview["src"]?>" class="catalog-item-images" style="background-image: url('<?=$preview["placeholder_src"]?>');"></span>
                <?else:?>
                    <span class="catalog-item-images" style="background-image: url('<?=$preview["src"]?>');"></span>
                <?endif;?>
            </a>

	        <?if (isset($offerFields) && $offerFields['raw']["MIN_COST"]){
		        ?>
		        <div class="catalog-item-price">
			        <small><?= Loc::getMessage("CITRUS_AREALTY_TEMPLATE_FROM") ?></small>
			        <span><?=number_format(Arealty\Helper::convertCost($offerFields['raw']["MIN_COST"]), 0, ',', ' ')?></span>
			        <span class="currency-icon"><span
					        class="<?= strtolower(Arealty\Helper::getSelectedCurrency()) ?>"></span></span>
		        </div>
		        <?
	        }?>
        </div>

		<a href="<?=$arItem["DETAIL_PAGE_URL"]?>" class="catalog-item-name"><span><?=$arItem["NAME"]?></span></a>

        <div class="catalog-item-address">
	        <?=(string)$arItem['ADDRESS']?>
        </div>

		<div class="catalog-item-date"><?=FormatDate("j F Y", MakeTimeStamp($arItem[\Citrus\Arealty\SortOrder::getOfferDateField()]))?></div>


		<div class="catalog-item-meta">
			<div class="catalog-item-info">
				<?
				if (isset($offerFields))
				{
					foreach ($offerFields['display'] as $fieldTitle => $fieldValue)
					{
						?>
                        <div class="catalog-item-info-item"><span class="fw700"><?=$fieldTitle?></span>: <span class="nobr"><?=$fieldValue?></span></div>
						<?
					}
				}
				?>
            </div><!-- .catalog-item-info -->
            <div class="catalog-item-control">
                <? if ((string)$arItem["ADDRESS"]):?>
                    <a href="javascript:void(0);" class="control-link map-link" rel="nofollow" data-address="<?=(string)$arItem["ADDRESS"]?>" data-coords="<?=\Bitrix\Main\Web\Json::encode($arItem["ADDRESS"]->getCoordinates())?>">
                        <span class="control-link-icon fa fa-fw fa-map-marker"></span>
                        <span class="control-link-label"><?=GetMessage("CITRUS_REALTY_VIEW_ON_MAP")?></span>
                    </a>
                <?endif; ?>
            </div><!-- .catalog-item-control -->
		</div><!-- .catalog-item-meta -->

	</div><!-- .catalog-item -->
	<?
}
?>
        </div><!-- .row._no-fz -->
	</div><!-- .goods-list.row -->
<?if( Arealty\Helper::getModuleOption("lazyload") == "Y"):?>
<script>
    //lazyload
    if (cui.ifdefined($.fn.lazyLoadInView)) {
	    $('[data-lazyload]').lazyLoadInView();
    }
</script>
<?endif;?>
<?
if ($arParams["DISPLAY_BOTTOM_PAGER"])
{
	?><?=$arResult["NAV_STRING"]?><?
}

if (isset($arParams["SHOW_MAP"]) && $arParams["SHOW_MAP"] == "Y")
{
	$arealtyYamap = new Arealty\Yamap;
	$arealtyYamap->addObject($arResult["ITEMS"]);

	$this->SetViewTarget('under-footer');
	    $arealtyYamap->printMap();
	$this->EndViewTarget();
}