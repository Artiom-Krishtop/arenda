<?php

use Bitrix\Main\Localization\Loc;
use Citrus\Arealty\Helper;

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
/** @var CBitrixComponent $component */

if (empty($arResult['ITEMS']))
{
	ShowNote($arParams['EMPTY_LIST_MESSAGE'] ? $arParams['EMPTY_LIST_MESSAGE'] : GetMessage("CITRUS_REALTY_NO_OFFERS"));
	return;
}

$strElementEdit = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_EDIT");
$strElementDelete = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_DELETE");
$arElementDeleteParams = array("CONFIRM" => GetMessage("CITRUS_REALTY_DELETE_CONFIRM"));
?>

<div class="catalog-slider p__swiper _nav-offset _nav-offset--small">
    <div class="swiper-container">
        <div class="swiper-wrapper">
			<?foreach ($arResult['ITEMS'] as $key => $arItem):?>
				<?$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], $strElementEdit);
				$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], $strElementDelete,
					$arElementDeleteParams);

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

				if(empty($arItem['PREVIEW_PICTURE'] && empty($arItem['DETAIL_PICTURE']) && !empty($arItem['PROPERTIES']['PANORAMIC_PHOTOS']['VALUE']))){
					$photoID = array_shift($arItem['PROPERTIES']['PANORAMIC_PHOTOS']['VALUE']);
					$photoData = CFile::GetFileArray($photoID);
			
					$arItem['PREVIEW_PICTURE'] = $photoData;
					$arItem['DETAIL_PICTURE'] = $photoData;
				}
				
				?>
                <div class="swiper-slide catalog-slider__item"
                     id="<?=$this->GetEditAreaId($arItem["ID"])?>">
					<?$APPLICATION->IncludeComponent(
						"citrus:template",
						"catalog-card",
						array(
							'DATA' => $arItem,
							'SHOW_INFO' => 'Y',
							"URL_TEMPLATES_PATH" => $arParams['URL_TEMPLATES_PATH'],
						),
						$component,
						array("HIDE_ICONS" => "Y")
					);?>
                </div>
			<?endforeach;?>
        </div>
    </div>
    <div class="swiper-button-prev">
        <span class="icon-arrow-left"></span>
    </div>
    <div class="swiper-button-next">
        <span class="icon-arrow-right"></span>
    </div>
</div><!-- .catalog-slider -->

<footer class="section-footer">
    <a class="btn <?=(empty($arParams['FOOTER_BUTTON_CLASS']) ? 'btn-secondary' : $arParams['FOOTER_BUTTON_CLASS'])?>"
       href="<?=$arResult['IBLOCK_LIST_LINK']?>"><?=Loc::getMessage("CATALOG_CAROUSEL_ALL_LINK")?></a>
</footer>

<script>
    ;(function () {

	    <?if( Citrus\Arealty\Helper::getModuleOption("lazyload") == "Y"):?>
	    //lazyload
	    if ( typeof $.fn.lazyLoadInView !== 'undefined' ) {
		    $('[data-lazyload]').lazyLoadInView();
	    }
	    <?endif;?>

	    // equal height address
	    equalHeightBot($('.catalog-slider__item .catalog-card__address'));

	    // set prices
	    currency.updateHtml($('.catalog-slider .catalog-card__price'));

	    // init slider
	    new Swiper(".catalog-slider .swiper-container", {
		    slidesPerView: 'auto',
		   /* autoplay: {
		    	delay: 8000,
		    	disableOnInteraction: false
		    },*/
		    spaceBetween: 26,
		    watchOverflow: true,
		    navigation: {
			    prevEl: '.catalog-slider .swiper-button-prev',
			    nextEl:  '.catalog-slider .swiper-button-next',
		    },
		    breakpoints: {
			    479: {
				    spaceBetween: 20,
				    slidesPerView: 1
			    },
                767: {
                    spaceBetween: 30,
                    slidesPerView: 2
                },
                1023: {
                    spaceBetween: 30,
                    slidesPerView: 3
                },
                4000: {
                    spaceBetween: 30,
                    slidesPerView: 4
                },
		    }
	    });
    })();
</script>
