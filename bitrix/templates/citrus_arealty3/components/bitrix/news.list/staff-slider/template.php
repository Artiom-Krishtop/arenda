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

$splitSections = $arParams['SPLIT_SECTION'] !== 'N';

?>

<div class="p__swiper staff-swiper _nav-offset _pagination-hide-nav" id="swiper-block-id-<?= $arParams['BLOCK_PREFIX'] ?>">
	<div class="swiper-container">
		<div class="swiper-wrapper">
			<?foreach ( $arResult['ITEMS'] as $key => $arItem):
				$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
				$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));?>

				<div class="swiper-slide staff-swiper__slide"
				     id="<?=$this->GetEditAreaId($arItem['ID'])?>">
					<?$APPLICATION->IncludeComponent(
						"citrus:template",
						"staff-item",
						array(
							'ITEM' => $arItem,
						),
						$component,
						array("HIDE_ICONS" => "Y")
					);?>
				</div>

			<?endforeach;?>
		</div>
	</div><!-- .swiper-container -->

	<div class="swiper-pagination"></div>
	<div class="swiper-button-prev"><i class="icon-arrow-left"></i></div>
	<div class="swiper-button-next"><i class="icon-arrow-right"></i></div>
	<script>
		;(function() {
			// http://idangero.us/swiper/api/
			var selSwiper = "<?= !empty($arParams['BLOCK_PREFIX'])?
				('#swiper-block-id-' . $arParams['BLOCK_PREFIX'])
				: '.staff-swiper'?>";
			var elSwiper = document.querySelector(selSwiper + ' .swiper-container');
			var swiper = new Swiper(elSwiper, {
				watchOverflow: true,
				/*autoplay: {
				  delay: 5000,
				},*/
				// pagination
				pagination: {
					el: selSwiper + ' .swiper-pagination',
					clickable: true
				},
				// Navigation arrows
				navigation: {
					nextEl: selSwiper + ' .swiper-button-next',
					prevEl: selSwiper + ' .swiper-button-prev'
				},

				slidesPerView: 1,
				spaceBetween: 20,
				breakpoints: {
                    479: {
                        slidesPerView: 1,
                        spaceBetween: 20
                    },
                    1023: {
                        slidesPerView: 2,
                        spaceBetween: 20
                    },
                    4000: {
                        slidesPerView: 3,
                        spaceBetween: 30
                    }
				}
			});
            $(window).resize(function () {
                swiper.update();
            });

		})();
	</script>
</div><!-- .staff-swiper -->

