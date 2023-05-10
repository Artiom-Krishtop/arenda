<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();/** @var array $arParams */
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

use Citrus\Arealty,
	Citrus\Arealty\Object\Address;
?>

<?if($arParams['AJAX']):?>
	<?
	$templateData['ITEMS'] = array_reduce($arResult['ITEMS'], function ($items, $arItem)  {

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

		$address = Address::createFromFields($arItem);

		$geo = $address->getGeo();
		if (!$geo)
		{
			return $items;
		}

		if(!empty($arItem['PROPERTIES']['photo']['VALUE'])){
			foreach ($arItem['PROPERTIES']['photo']['VALUE'] as $key => $photoID) {
				$photoData = CFile::GetFileArray($photoID);

				if(stripos($photoData['CONTENT_TYPE'], 'video') !== false) {
					unset($arItem['PROPERTIES']['photo']['VALUE'][$key]);
				}
			}
		}
		
		if(empty($arItem['PREVIEW_PICTURE']) && empty($arItem['DETAIL_PICTURE']) && !empty($arItem['PROPERTIES']['photo']['VALUE'])){
			$photoID = array_shift($arItem['PROPERTIES']['photo']['VALUE']);
			$photoData = CFile::GetFileArray($photoID);
	
			$arItem['PREVIEW_PICTURE'] = $photoData;
			$arItem['DETAIL_PICTURE'] = $photoData;
		}

		if(empty($arItem['PREVIEW_PICTURE']) && empty($arItem['DETAIL_PICTURE']) && !empty($arItem['PROPERTIES']['PANORAMIC_PHOTOS']['VALUE'])){
			$photoID = array_shift($arItem['PROPERTIES']['PANORAMIC_PHOTOS']['VALUE']);
			$photoData = CFile::GetFileArray($photoID);
	
			$arItem['PREVIEW_PICTURE'] = $photoData;
			$arItem['DETAIL_PICTURE'] = $photoData;
		}
		
		ob_start();

		$img = Arealty\Helper::resizeOfferImage($arItem, 190, 170);?>
		<a href="<?=$arItem["DETAIL_PAGE_URL"]?>" class="citrus-objects-map-popup">
			<?
			echo \CFile::ShowImage($img['src'], $img['width'], $img['height'], ' class="citrus-objects-map-popup__image"');

			if ($arItem["PROPERTIES"]["cost"]["VALUE"]) {
				$priceAdditional = '';
				if ($arItem['PROPERTIES']['cost_unit']['VALUE'])
				{
					$priceAdditional .= '<span class="catalog-item-price__unit"> / ' . str_replace(GetMessage("CITRUS_AREALTY_COST_UNIT_SEARCH"), GetMessage("CITRUS_AREALTY_COST_UNIT_REPLACE"), $arItem['PROPERTIES']['cost_unit']['VALUE']) . '</span>';
				}
				if ($printProp("cost_period", ''))
				{
					$priceAdditional .= ' <span class="catalog-item-price__period">' . GetMessage("CITRUS_AREALTY_COST_PERIOD_IN") . $printProp("cost_period", '') . '</span>';
				}
				?>
				<span class="citrus-objects-map-popup__price">
				<?#currency set in js?>
				<span
					data-currency-icon=""
					data-currency-base="<?=$printProp("cost", 0)?>"></span>
				<?=$priceAdditional?>
				</span><?
			}
			?>
			<span class="citrus-objects-map-popup__title"><?=$arItem["NAME"]?></span>
			<span class="citrus-objects-map-popup__desc"><?=(string)$address?></span>
		</a>
		<?
		$body = ob_get_clean();
		$items[] = array(
			'name' => $arItem["NAME"],
			'address' => (string)$address,
			'code' => $arItem['CODE'],
			'body' => $body,
			'coord' => $address->getCoordinates(),
		);

		return $items;
	}, array());
	echo \Bitrix\Main\Web\Json::encode($templateData['ITEMS'] ?: array());
	?>
<?else:?>
	<div class="citrus-objects-map" id="<?=$arResult["MAP_ID"]?>"></div>
	<script data-src="/bitrix/">
        ;(function(){
            new BigDataMap(<?=\Bitrix\Main\Web\Json::encode(
				array(
					'mapId' => $arResult["MAP_ID"],
					'ajaxPath' => $componentPath.'/ajax.php',
					'arParams' => $arResult['AJAX_PARAMS'],
					'pageCount' => $arResult['PAGE_COUNT'],
					'templateName' => $templateName,
				)
			)?>);
        }());
	</script>
<?endif;?>