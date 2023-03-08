<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
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

use Bitrix\Main\Localization\Loc;

$this->setFrameMode(true);
$this->addExternalJs(SITE_TEMPLATE_PATH . "/js/custom.js");


if (!$arResult['ACTIVE'] && !$arResult['CAN_EDIT'])
	return;

if ($arParams['VIEW_TARGET'])
{
	$this->SetViewTarget($arParams['VIEW_TARGET']);
}

$showContent = function() use ($arResult, $arParams){
	global $APPLICATION;
	if ($arParams["AREA_FILE_SHOW"] === 'view_content')
		$APPLICATION->ShowViewContent($arParams['VIEW_CONTENT_ID']);
	else
		echo $arResult['FILE_CONTENT'];
};

	$wrapperAdditionalParameters = '';
	if ($arParams['WIDGET_REL'] && $arResult['CAN_EDIT']) {
	  $wrapperAdditionalParameters = 'data-settings="BLOCKS" data-settings-rel="'.$arParams['WIDGET_REL'].'"';
	  if (!$arResult['ACTIVE']) $wrapperAdditionalParameters .= ' style="display:none;" ';
	}

	?>
  <? if ($arParams['PAGE_SECTION'] == "Y"):?>
      <?
	  $params['h'] = $arParams['~h'] ? trim($arParams['~h']) : '.h1';

      $sectionClass = array('section');
      if ($arParams['PADDING'] !== 'N') $sectionClass[] = '_with-padding';
	$arParams['BG_COLOR'] = $arParams['BG_COLOR'] ? $arParams['BG_COLOR'] : 'n';
      $sectionClass[] = 'section-color-'.strtolower($arParams['BG_COLOR']);
      if ($arParams['CLASS']) $sectionClass[] = $arParams['CLASS'];
      if ($arParams['BOTTOM_SUBSTRATE'] === 'Y') $sectionClass[] = '_bottom_substrate';
      ?>

      <section class="<?=implode(' ',$sectionClass)?>" <?=$wrapperAdditionalParameters?>>

          <div  class="w find-form">

	          <hr class="section__border-top">

              <div class="section-inner">
                  <?if($arParams['TITLE'] || $arParams['DESCRIPTION']):?>
                      <header class="section__header">
                          <?if($arParams['TITLE']):?>

							  <?=\Spatie\HtmlElement\HtmlElement::render($params['h'], $arParams['TITLE'])?>
                          <?endif;?>

                          <?if($arParams['DESCRIPTION']):?>
                              <div class="section-description"><?=$arParams['DESCRIPTION']?></div>
                          <?endif;?>
                      </header>
                  <?endif;?>

	              <?if($arParams['CUT_CONTENT_HEIGHT'] > 0):?>
		              <div class="section__content _cut_overflow" 
		                   style="max-height: <?=$arParams['CUT_CONTENT_HEIGHT']?>px;">
			              <?$showContent();?>
		              </div>
		              <?if($arParams['CUT_CONTENT_HEIGHT'] > 0):?>
			              <div class="section-footer">
				              <a href="javascript:void(0);"
				                 class="btn btn-secondary js-section-show-more">
					              <?= Loc::getMessage("INCLUDE_TPL_READ_MORE") ?>
				              </a>
			              </div>
		              <?endif;?>
	              <?else:?>
		              <div class="section__content">
			              <?$showContent();?>
		              </div>
	              <?endif;?>

                  <?php

                  $params = $this->getComponent()->arParams;
                  if ($footerContent = $params['~FOOTER_CONTENT'] ?: $params['FOOTER_CONTENT'])
                  {
                      ?>
                      <div class="section-footer">
                        <?=$footerContent?>
                      </div>
                      <?php

                      unset($footerContent);
                  }

                  ?>

              </div><!-- .section-inner -->
          </div><!-- .w -->
      </section>
  <?else:?>
	  <?if($wrapperAdditionalParameters):?>
		  <div <?=$wrapperAdditionalParameters?>>
	  <?endif;?>
	        <?$showContent();?>
	  <?if($wrapperAdditionalParameters):?>
	      </div>
	  <?endif;?>
  <?endif;?>

