<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use Citrus\Arealty\Entity\SettingsTable;
use Citrus\Arealty\Helper;
use Spatie\HtmlElement\HtmlElement;

Loc::loadMessages(__FILE__);

Citrus\Arealty\Template\showPart('main-slider', ['view-target' => 'page-top']);

/**
 * $APPLICATION->ShowTitle() replacement
 * @todo �������� ����� ����� � ���������� �������� �� ����� ������ ������
 */
if ($APPLICATION->GetProperty('SHOW_TITLE', 'Y') === 'Y')
{
    $subheaderHtml = '';
    if ($subheader = $APPLICATION->GetProperty('PAGE_SUBHEADER'))
    {
        $subheaderHtml = HtmlElement::render('.section-description', $subheader);
    }

    $title = $APPLICATION->GetPageProperty('pageH1', 'h1#pagetitle');

    $pageSectionClass = trim($APPLICATION->GetPageProperty('pageSectionClass'));
    $pageSectionClass = $pageSectionClass ? ' ' . $pageSectionClass : '';

    $pageSectionContentClass = trim($APPLICATION->GetPageProperty('pageSectionContentClass'));
    $pageSectionContentClass = $pageSectionContentClass ? ' ' . $pageSectionContentClass : '';

    $titleHtml = HtmlElement::render($title, $APPLICATION->GetTitle(false));

    $blockStart = <<<HTML
<section class="section section--page-wrapper _with-padding$pageSectionClass">

	<div   class="w find-form">
	
		<hr class="section__border-top">
		<div class="section-inner">
			<header class="section__header">
			    {$titleHtml}
				{$subheaderHtml}
			</header>
			<div class="section__content$pageSectionContentClass">

HTML;
    $APPLICATION->AddViewContent('workarea-start', $blockStart);

    $blockEnd = <<<HTML
			</div><!-- .section__content -->
		</div><!-- .section-inner -->
	</div><!-- .w -->
</section>
HTML;
    echo $blockEnd;
}

$APPLICATION->ShowViewContent('workarea-end');

$mapIblockId = $APPLICATION->GetProperty('mapCatalogIblockId') ?: $APPLICATION->GetCurPage(false) == SITE_DIR ? Helper::getIblock('offers') : '';
if ($mapIblockId):
	?> <?$APPLICATION->IncludeComponent(
	"citrus.core:include",
	".default",
	Array(
		"AREA_FILE_SHOW" => "sect",
		"AREA_FILE_SUFFIX" => "footer_map",
		"COMPONENT_TEMPLATE" => ".default",
		"COMPOSITE_FRAME_MODE" => "A",
		"COMPOSITE_FRAME_TYPE" => "AUTO",
		"MAP_IBLOCK_ID" => $mapIblockId,
		"PAGE_SECTION" => "N",
		"TITLE" => "",
		"WIDGET_REL" => "map"
	)
);?><?
endif;

$APPLICATION->ShowViewContent('footer-before');

?>
<section class="b-section-banner section">
    <?$APPLICATION->IncludeComponent(
        "bitrix:news.line",
        "ads-banners-bottom",
        Array(
            "IBLOCK_TYPE" => "info",
            "IBLOCKS" => "25",
            "NEWS_COUNT" => "20",
            "FIELD_CODE" => Array("ID", "PREVIEW_PICTURE"),
            "SORT_BY1" => "SORT",
            "SORT_ORDER1" => "ASC",
            "SORT_BY2" => "ACTIVE_FROM",
            "SORT_ORDER2" => "DESC",
            "DETAIL_URL" => "",
            "ACTIVE_DATE_FORMAT" => "d.m.Y",
            "CACHE_TYPE" => "A",
            "CACHE_TIME" => "3600",
            "CACHE_GROUPS" => "Y",
            'SHOW_SLIDER_NAV' => 'N'
        )
    );?>
</section>
<?

Citrus\Arealty\Template\showPart('footer-callout');

?> <footer class="f print-hidden">
<div class="w">
	<div class="f-t row row-grid">
		<div class="col-md-6 col-lg-8">
			 <?$APPLICATION->IncludeComponent(
	"bitrix:menu",
	"footer_menu",
	Array(
		"ALLOW_MULTI_SELECT" => "Y",
		"CACHE_SELECTED_ITEMS" => "N",
		"CHILD_MENU_TYPE" => "left",
		"COMPONENT_TEMPLATE" => "bottom",
		"DELAY" => "N",
		"MAX_LEVEL" => "2",
		"MENU_CACHE_GET_VARS" => [],
		"MENU_CACHE_TIME" => "36000000",
		"MENU_CACHE_TYPE" => "A",
		"MENU_CACHE_USE_GROUPS" => "N",
		"ROOT_MENU_TYPE" => "bottom",
		"USE_EXT" => "Y"
	)
);?>
		</div>
		<div class="col-md-6 col-lg-4">
			<div class="f-t__contact-w">
				<div class="f-t__title">
					 <?= Loc::getMessage("CITRUS_AREALTY3_FOOTER_CONTACT_TITLE") ?>
				</div>
				<div class="f-contacts">
					<div class="f-contacts__item">
						<div class="f-contacts__item-icon">
 <i class="icon-phone"></i>
						</div>
						<div class="f-contacts__item-value">
 <?=SettingsTable::showValue("PHONE")?>
						</div>
						 <!-- .f-contacts__item-value -->
					</div>
					 <!-- .f-contacts__item" -->
					<div class="f-contacts__item <span id=">
						<div class="f-contacts__item-icon">
 <i class="icon-mailmanager"></i>
						</div>
						<div class="f-contacts__item-value">
 <a href="mailto:<?=SettingsTable::getValue("EMAIL")?>">  <?=SettingsTable::showValue("EMAIL")?> </a>
						</div>
						 <!-- .f-contacts__item-value -->
					</div>
					 <!-- .f-contacts__item -->
					<div class="f-contacts__item">
						<div class="f-contacts__item-icon">
 <i class="icon-map"></i>
						</div>
						<div class="f-contacts__item-value" data-settings="ADDRESS">
							 <?=SettingsTable::showValue("ADDRESS")?>
						</div>
						 <!-- .f-contacts__item-value -->
					</div>
					 <!-- .f-contacts__item -->
				</div>
				 <!-- .f-contacts --> <?php if (CModule::IncludeModule("subscribe")
								&& $APPLICATION->GetProperty('SHOW_FOOTER_SUBSCRIBE_FORM') !== 'N')
						{
							?>
				<div class="f-subscribe">
					 <?$APPLICATION->IncludeComponent(
	"citrus:subscribe.form",
	"",
	Array(
		"FORMAT" => "text",
		"INC_JQUERY" => "N",
		"NO_CONFIRMATION" => "N"
	)
);?>
				</div>
				 <?php
						}
						?>
			</div>
			 <!-- .f-t__col-contact -->
		</div>
		 <!-- .col -->
	</div>
	<div class="f-b">
		<div class="f-b__copy">
 <span class="fa fa-copyright"></span> <?=date('Y')?>, <?=SettingsTable::showValue("SITE_NAME")?><br>
		</div>
		<div class="f-b__soc">
			 <?$APPLICATION->IncludeFile(
						SITE_DIR."include/footer_social.php",
						[],
						["MODE"=>"html"]
					);?>
		</div>
		<div class="f-b__developer">
			<div id="bx-composite-banner">
			</div>
		</div>
	</div>
	 <!-- .f-b -->
</div>
 <!-- .w --> </footer>
    <!-- .container -->
<!-- .cry-layout -->

<script>

    $( document ).ready(function() {
        if(document.querySelector('.filter.citrus-sf-wrapper')){
            let collapsed = document.querySelector('.collapsed_find-form');
            let form = document.querySelector('.filter.citrus-sf-wrapper');
            if($('.section._with-padding.section-color-n').length != 0){
                $('.section._with-padding.section-color-n')[0].style.zIndex='1000';
                $('.section._with-padding.section-color-n')[0].style.position='relative';

            }
            if($('.filter.citrus-sf-wrapper').length !=0){
                $('.filter.citrus-sf-wrapper')[0].style.zIndex='1000';
                $('.filter.citrus-sf-wrapper')[0].style.position='relative';

            }
            var header_height = document.querySelector('.c-side.bg-white').offsetHeight;
            var scrolled = 0;
            window.onscroll = function () {
                scrolled = window.pageYOffset || document.documentElement.scrollTop;
                if (scrolled > 0) {
                    collapsed.classList.add('active');
                }
                else{
                    collapsed.classList.remove('active');
                }
                if(form.classList.contains('popup') && scrolled > 0){
                    //if(window.offset)

                    if(window.screen.width < 768){
                        $(form).css('transform',`translateY(${scrolled - 140}px)`);
                    }
                    else{
                        $(form).css('transform',`translateY(${scrolled - 40}px)`);
                    }


                }
                else{
                    form.classList.remove('popup');
                    $(form).css('transform',`translateY(${0}px)`);
                    if($('.section._with-padding.section-color-n').length != 0){
                        $('.section._with-padding.section-color-n')[0].style.zIndex='1000';
                        $('.section._with-padding.section-color-n')[0].style.position='relative';

                    }
                    if($('.filter.citrus-sf-wrapper').length !=0){
                        $('.filter.citrus-sf-wrapper')[0].style.zIndex='1000';
                        $('.filter.citrus-sf-wrapper')[0].style.position='relative';

                    }
                    $(form).css('padding',`0`);
                }


                }
            collapsed.onclick = (e) =>{
                e.stopPropagation();

                form.classList.toggle('popup');

                //$(find_form[0]).parent().css('top',`${scrolled-160}px`)
                if(window.screen.width < 768){
                    $(form).css('transform',`translateY(${scrolled - (header_height*2)}px)`);
                }
                else{
                    $(form).css('transform',`translateY(${scrolled - 40}px)`);
                }
                $(form).css('padding',`100px 15px 40px`);
                if($('.section._with-padding.section-color-n').length != 0){
                    $('.section._with-padding.section-color-n')[0].style.zIndex='1000';
                    //$('.section._with-padding.section-color-n')[0].style.position='fixed';

                }
                if($('.filter.citrus-sf-wrapper').length !=0){
                    $('.filter.citrus-sf-wrapper')[0].style.zIndex='1000';
                   // $('.filter.citrus-sf-wrapper')[0].style.position='fixed';

                }
            }

        }

        //let find_form = document.querySelectorAll('.find-form');

        //find_form[0].classList.add('active_find');
    });

   /*
        //console.log(find_form);

        window.onscroll = function() {
            var scrolled = window.pageYOffset || document.documentElement.scrollTop;
            // var scrolled = window.pageYOffset || document.documentElement.scrollTop;
            if (scrolled > 0) {
                // if(!find_form[0].classList.contains('not_active_find')){
                // $('.translate-top').addClass('active');
                // find_form[0].classList.add('not_active_find');
                collapsed.classList.add('active');

                //}
            }
            else {

                //find_form[0].classList.remove('not_active_find');
                //$('.translate-top').removeClass('active');
                collapsed.classList.remove('active');
            }
            /*if(find_form[0].parentNode.classList.contains('popup') && scrolled > 0){
                // $(find_form[0]).parent().css('top',`${scrolled-160}px`)
                $(find_form[0]).parent().css('top',`${scrolled-40}px`)
            }
            else{
                find_form[0].parentNode.classList.remove('popup');
                $(find_form[0]).parent().css('top',`0`)
            }
            collapsed.onclick = (e) =>{
                e.stopPropagation();
                find_form[0].parentNode.classList.toggle('popup');
                //$(find_form[0]).parent().css('top',`${scrolled-160}px`)
                $(find_form[0]).parent().css('top',`${scrolled-40}px`)


            }
        }

        document.addEventListener('click',(e)=>{
            if(!e.target.closest('.popup') ){
            }
        })


        }

    });*/
</script>


<?php



$APPLICATION->ShowProperty('BeforeHeadClose');

?>
