<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$this->setFrameMode(true);

?>

<?if (!empty($arResult["ITEMS"])):?>
    <div class="nav-sliders p__swiper">
        <div class="swiper-container">
            <div class="swiper-wrapper">
                <? foreach($arResult["ITEMS"] as $i => $arItem):?>
                <? if($arParams["MAX_LEVEL"] == 1 && $arItem["DEPTH_LEVEL"] > 1)
                    continue;?>
                    <div class="swiper-slide">
                        <a class="nav-sliders__link <?if($arResult["ACTIVE_INDEX"] == $i):?>is-active<?endif;?>" href="<?=$arItem["LINK"]?>">
                            <span class="btn-label"><?=$arItem["TEXT"]?></span>
                        </a>
                    </div>
                <? endforeach; ?>
            </div>
        </div>
        <div class="swiper-button-prev">
            <span class="fa fa-caret-left"></span>
        </div>
        <div class="swiper-button-next">
            <span class="fa fa-caret-right"></span>
        </div>
    </div>
    <script>
        ;(function () {
	        new Swiper('.nav-sliders .swiper-container', {
		        prevButton: '.nav-sliders .swiper-button-prev',
		        nextButton: '.nav-sliders .swiper-button-next',
		        spaceBetween: 5,
		        initialSlide: <?=$arResult["ACTIVE_INDEX"]?>,
		        onInit: typeof lockSwipesWiderSlides !== "undefined" ? lockSwipesWiderSlides : function () {},
		        slidesPerView: 6,
		        breakpoints: {
			        767: {
				        slidesPerView: 1
			        },
			        1023: {
				        slidesPerView: 3
			        },
			        1279: {
				        slidesPerView: 4
			        }
		        }
	        });
        })();
    </script>
<?endif;?>