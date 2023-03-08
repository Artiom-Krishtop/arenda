<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use Citrus\Arealty\Template\Property;
use Citrus\Arealty\Template\TemplateHelper;

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

<?php

if ($arResult["DESCRIPTION"])
{
	?>
	<div class="catalog-section-description"><?=$arResult["DESCRIPTION"]?></div>
	<p class="indent"></p>
	<?php
}

if (empty($arResult['ITEMS']))
{
	ShowNote($arParams['EMPTY_LIST_MESSAGE'] ? $arParams['EMPTY_LIST_MESSAGE'] : GetMessage("CITRUS_REALTY_NO_OFFERS"));
	return;
}

if ($arParams["DISPLAY_TOP_PAGER"])
{
	echo $arResult["NAV_STRING"];
}

$strElementEdit = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_EDIT");
$strElementDelete = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_DELETE");
$arElementDeleteParams = array("CONFIRM" => GetMessage("CITRUS_REALTY_DELETE_CONFIRM"));


?>

<div class="table-slider">

	<?#left col?>
	<div class="table-slider__left">
		<div class="table-slider__th"><?= Loc::getMessage("TABLE_SLIDER_PHOTO") ?></div>

		<?foreach ($arResult['ITEMS'] as $key => $arItem):
			$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], $strElementEdit);
			$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], $strElementDelete, $arElementDeleteParams);

			if(empty($arItem['PREVIEW_PICTURE'] && empty($arItem['DETAIL_PICTURE']) && !empty($arItem['PROPERTIES']['photo']['VALUE']))){
				$photoID = array_shift($arItem['PROPERTIES']['photo']['VALUE']);
				$photoData = CFile::GetFileArray($photoID);
		
				$arItem['PREVIEW_PICTURE'] = $photoData;
				$arItem['DETAIL_PICTURE'] = $photoData;
			}
			
			$preview = \Citrus\Arealty\Helper::resizeOfferImage($arItem, 250, 225);
			?>
			<div class="table-slider__td _photo"
			     style="background: url(<?=$preview['src']?>);"
			     id="<?=$this->GetEditAreaId($arItem["ID"])?>">
				<a href="<?=$arItem['DETAIL_PAGE_URL']?>" class="table-slider__detail-link"><?=$arItem['NAME']?></a>
			</div><!-- .table-slider__th -->
		<?endforeach;?>
	</div><!-- .favorites-table__left -->

	<?#center?>
	<div class="table-slider__center _with-right-col">
		<div class="p__swiper table-slider__swiper">
			<div class="swiper-container">
				<div class="swiper-wrapper">

				<?#slide?>
				<?foreach ( $arResult['FIELDS'] as $arProperty ):?>

					<div class="swiper-slide table-slider__slide">
						<a href="<?=$arProperty['SORT_LINK']?>"
						   class="table-slider__th _sort">
							<span
							   class="table-slider__property-name-list">
								<span class="table-slider__property-name">
									<?=$arProperty['name']?>
								</span>
							</span><!-- .table-slider__property-name-list -->

							<?php

							$iconClass = ['table-slider__sort-icon'];
							if ($arProperty['SELECTED']) $iconClass[] = '_active';
							if ($arProperty['ORDER'] === 'DESC') $iconClass[] = '_desc';
							?>
							<span class="<?=implode(' ', $iconClass)?>"></span>
						</a><!-- .table-slider__th -->

						<?foreach ( $arResult['ITEMS'] as $arItem):
							$property = new Property($arItem);?>
							<div class="table-slider__td"
							     data-property-code="<?=$arProperty['code']?>">
								<?if($arProperty['code'] === 'cost'):?>
								    <b><?=$property->getFormatValue($arProperty['code'])?></b>
									<?= TemplateHelper::quickSaleLabel($arItem, '.table-slider__share-label.theme--bg-color')?>
								<?else:?>
									<span><?=$property->getFormatValue($arProperty['code'])?></span>
								<?endif;?>
							</div><!-- .table-slider__td -->
						<?endforeach;?>

					</div><!-- .swiper-slide -->
				<?endforeach;?>

				</div><!-- .swiper-wrapper -->
			</div><!-- .swiper-container -->
			<div class="swiper-scrollbar"></div>
		</div><!-- .p__swiper -->
	</div>

	<?#right col?>
	<div class="table-slider__right">
		<div class="table-slider__th"><?= Loc::getMessage("TABLE_SLIDER_ACTIONS") ?></div>

		<?foreach ( $arResult['ITEMS'] as $arItem):
			$property = new Property($arItem);
			$geodata = $property->getValue('geodata');
			?>
			<div class="table-slider__td">
				<div class="table-slider__action-block">
					<?if($geodata && $geodata instanceof \Citrus\Yandex\Geo\GeoObject):
						if ($geodata->getLatitude() && $geodata->getLongitude())
						{
							$dataCoords = [
									$geodata->getLatitude(),
									$geodata->getLongitude(),
								];
						}
						?>
						<a href="javascript:void(0);"
						   data-coords="<?=\Bitrix\Main\Web\Json::encode($dataCoords)?>"
						   title="<?=(string) $geodata?>"
						   data-address="<?=(string) $geodata?>"
						   class="table-slider__action-link js-map-link">
							<i class="icon-on-map"></i>
							<span class="table-slider__action-text"><?= Loc::getMessage("TABLE_SLIDER_ACTION_MAP") ?></span>
						</a>
					<?endif;?>
					<a href="javascript:void(0);"
					   data-id="<?=$arItem['ID']?>"
					   class="table-slider__action-link add2favourites">
						<i class="icon-favorites _not-added"></i>
						<i class="icon-favorites-full _added"></i>

						<span class="table-slider__action-text _not-added"><?= Loc::getMessage("TABLE_SLIDER_FAVORITE") ?></span>
						<span class="table-slider__action-text _added"><?= Loc::getMessage("TABLE_SLIDER_FAVORITE_ADDED") ?></span>
					</a>
				</div>
			</div>
		<?endforeach;?>
	</div><!-- .table-slider__right -->

</div><!-- .table-slider -->


<script>
	;(function(){
		if (typeof currency !== 'undefined') {
			currency.updateHtmlCurrency($('.table-slider [data-currency-base]'));
		}

		$('.table-slider__th').responsiveEqualHeightGrid();
		$('.table-slider__left .table-slider__td').each(function (index) {
			var nthIndex = index + 2;
			var $items = $(this)
			.add($('.table-slider__slide .table-slider__td:nth-child(' + nthIndex + ')'))
			.add($('.table-slider__right .table-slider__td:nth-child(' + nthIndex + ')'));

			$items.responsiveEqualHeightGrid();
		});


		// http://idangero.us/swiper/api/
		var swiper = new Swiper('.table-slider__swiper .swiper-container', {
			watchOverflow: true,
			scrollbar: {
				el: '.table-slider__swiper .swiper-scrollbar',
				draggable: true
			},
			freeMode: true,
			slidesPerView: 'auto',
			breakpoints: {
				380: {
					slidesPerView: 1,
					freeMode: false
				}
			},
			on: {
				init: function () {}
			},
			freeModeMomentumBounce: false,
			touchReleaseOnEdges: true
		});
	}());
</script>

<?if ($arParams["DISPLAY_BOTTOM_PAGER"])
{
	echo $arResult["NAV_STRING"];
}
