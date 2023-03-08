<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Main\Localization\Loc,
	Citrus\Arealty\Helper,
    Citrus\Arealty;

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
/** @var CBitrixComponent $component */?>


<?if ($arResult["DESCRIPTION"])
{
	?><div class="section-description"><?=$arResult["DESCRIPTION"]?></div><?
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

<div class="catalog catalog-th-list">
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
	foreach ($arResult['ITEMS'] as $key => $arItem) :
        $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], $strElementEdit);
        $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], $strElementDelete, $arElementDeleteParams);

		if(!empty($arItem['PROPERTIES']['photo']['VALUE'])){
			foreach ($arItem['PROPERTIES']['photo']['VALUE'] as $key => $photoID) {
				$photoData = CFile::GetFileArray($photoID);

				if(stripos($photoData['CONTENT_TYPE'], 'video') !== false) {
					unset($arItem['PROPERTIES']['photo']['VALUE'][$key]);
				}
			}
		}
		
		if(empty($arItem['PREVIEW_PICTURE'] && empty($arItem['DETAIL_PICTURE']) && !empty($arItem['PROPERTIES']['photo']['VALUE']))){
			$photoID = array_shift($arItem['PROPERTIES']['photo']['VALUE']);
			$photoData = CFile::GetFileArray($photoID);
	
			$arItem['PREVIEW_PICTURE'] = $photoData;
			$arItem['DETAIL_PICTURE'] = $photoData;
		}

		$offerFields = isset($arResult['OFFERS_FIELDS'][$arItem['XML_ID']]) ? $arResult['OFFERS_FIELDS'][$arItem['XML_ID']] : null;

        $preview = \Citrus\Arealty\Helper::resizeOfferImage($arItem, 250, 225);
        if (!$preview) $preview = array( "src" => SITE_TEMPLATE_PATH."/images/no-photo.png");
        $id = $this->GetEditAreaId($arItem["ID"]);?>

        <div class="catalog-item" id="<?=$id?>">
            <div class="catalog-item-preview">
                <a href="<?=$arItem["DETAIL_PAGE_URL"]?>">
                    <?if( Arealty\Helper::getModuleOption("lazyload") == "Y"):?>
                        <span data-lazyload="<?=$preview["src"]?>" class="catalog-item-images" style="background-image: url('<?=$preview["placeholder_src"]?>');"></span>
                    <?else:?>
                        <span class="catalog-item-images" style="background-image: url('<?=$preview["src"]?>');"></span>
                    <?endif;?>
                </a>
            </div>
            <div class="catalog-item-container">
                <div class="catalog-item-body">
                    <a href="<?=$arItem["DETAIL_PAGE_URL"]?>" class="catalog-item-name">
                        <span><?=$arItem["NAME"]?></span>
                    </a>
                    <div class="catalog-item-address">
		                <?if((string)$arItem["ADDRESS"]):?>
                            <a href="javascript:void(0);" class="control-link map-link" rel="nofollow" data-address="<?=(string)$arItem["ADDRESS"]?>" title="<?=GetMessage("CITRUS_REALTY_VIEW_ON_MAP")?>" data-coords="<?=\Bitrix\Main\Web\Json::encode($arItem["ADDRESS"]->getCoordinates())?>"><span class="control-link-icon fa fa-fw fa-map-marker"></span> <span class="map-link-text"><?=(string)$arItem["ADDRESS"]?></span></a>
		                <?endif;?>
                    </div>
                    <div class="catalog-item-desc">
                        <?if(!empty($arItem['PROPERTIES']['DESCRIPTION']['~VALUE']['TEXT'])):?>
                            <?= $arItem['PROPERTIES']['DESCRIPTION']['~VALUE']['TEXT']?>
                        <?endif;?>
	                    <?//=$arItem["PREVIEW_TEXT"]?>
                    </div>
                </div>
                <div class="catalog-item-meta">
	                <?if ($arItem["PROPERTIES"]["cost"]["VALUE"]){
		                $priceAdditional = '';
		                if ($printProp("cost_unit", ''))
		                {
			                $priceAdditional .= '<span class="catalog-item-price__unit">' . /*str_replace(GetMessage("CITRUS_AREALTY_COST_UNIT_SEARCH"), GetMessage("CITRUS_AREALTY_COST_UNIT_REPLACE"), $printProp("cost_unit", '')) .*/ '</span>';
		                }
		                if ($printProp("cost_period", ''))
		                {
			                $priceAdditional .= ' <span class="catalog-item-price__period">' . GetMessage("CITRUS_AREALTY_COST_PERIOD_IN") . $printProp("cost_period", '') . '</span>';
		                }?>
		                <div class="catalog-item-price">
			                <?#currency set in js?>
			                <span data-currency-base="<?=$printProp("cost", 0)?>" data-currency-icon="">&nbsp;</span>
			                <?=$priceAdditional?>
		                </div>
		                <script>
			                currency.updateHtml($('#<?=$id?> .catalog-item-price'));
		                </script>
		                <?
	                }?>

	                <?if (isset($offerFields) && $offerFields['raw']["MIN_COST"]){
		                ?>
		                <div class="catalog-item-price">
			                <?#currency set in js?>
			                <?= Loc::getMessage("CITRUS_AREALTY_TEMPLATE_FROM") ?>
			                <span data-currency-base="<?= $offerFields['raw']["MIN_COST"] ?>" data-currency-icon="">&nbsp;</span>
		                </div>
		                <script>
			                currency.updateHtml($('#<?=$id?> .catalog-item-price'));
		                </script>
		                <?
	                }?>

                    <div class="catalog-item-info">
		                <?
		                if (!$arItem["DISPLAY_COLUMNS_DEFAULT"]) :
			                foreach ($arItem["DISPLAY_COLUMNS"] as $propertyCode => $column)
			                {
				                if (0 === strpos($propertyCode, '~'))
				                {
					                // ���� ��������
					                switch ($propertyCode)
					                {
						                case "~DETAIL_PICTURE":
							                break;

						                case "~NAME":
							                break;

						                default:
							                echo '<div class="catalog-item-info-item">' . $arItem[substr($propertyCode, 1)] . '</div>';
							                break;
					                }
				                }
				                else if (isset($arItem['PROPERTIES'][$propertyCode]))
				                {
					                $arProp = $arItem['PROPERTIES'][$propertyCode];
					                if ($propertyCode == 'cost')
					                {
						                echo '<div class="catalog-item-info-item"><span class="fw600">' . $arProp['NAME'] . '</span> ' . ($printProp("cost", 0) ? number_format($printProp("cost", 0), 0, ',', ' ') . '<span class="icon-ruble"></span>' : '') . '</div>';
					                }
					                else
					                {
						                echo '<div class="catalog-item-info-item"><span class="fw600">' . $arProp['NAME'] . '</span> ' . $printProp($propertyCode). '</div>';
					                }
				                }
			                }
		                else:?>
                            <div class="catalog-item-info-item"><span class=""><?=Loc::getMessage("CITRUS_REALTY_AREA")?></span>:
                                <span class="nobr"><?=$printProp("common_area")?></span>
                            </div>
                            <div class="catalog-item-info-item"><span class="fw700"><?=$arItem["PROPERTIES"]["rooms"]["NAME"]?></span>: <span class="nobr"><?=count($arItem["PROPERTIES"]['NEW_ROOMS_AREA']["VALUE"])?></span></div>
                            <div class="catalog-item-info-item"><span class="fw700"><?=$arItem["PROPERTIES"]["NEW_FLOOR"]["NAME"]?></span>: <span class="nobr"><?=$printProp("NEW_FLOOR")?>/<?=$printProp("floors")?></span></div>
		                <?endif; ?>

		                <?php if (isset($offerFields))
		                {
		                	foreach ($offerFields['display'] as $fieldTitle => $fieldValue)
			                {
				                ?>
				                <div class="catalog-item-info-item"><span class="fw600"><?=$fieldTitle?></span>: <span class="nobr"><?=$fieldValue?></span></div>
				                <?
			                }
		                } ?>
                    </div><!-- .catalog-item-info -->

                    <div class="catalog-item-control ">
                        <div class="btn-row btn-grid">
	                        <?php if (empty($arParams['IS_JK']) || $arParams['IS_JK'] != 'Y') { ?>
                            <a class="add2favourites control-link" data-id="<?=$arItem["ID"]?>" href="javascript:void(0);" rel="nofollow">
                                <span class="control-link-icon icon-star-full"></span>
                                <span class="control-link-label"><?=Loc::getMessage("CITRUS_REALTY_2FAV")?></span>
                            </a>
                            <?php } ?>
                        </div>
                    </div>
                </div><!-- .catalog-item-meta -->
            </div><!-- .catalog-item-container -->
        </div><!-- .catalog-item -->
    <?endforeach;?>
</div>

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
?>
