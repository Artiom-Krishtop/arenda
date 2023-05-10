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

$this->setFrameMode(true);

if (!$arResult['ACTIVE'] && !$arResult['CAN_EDIT'])
	return;

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
	  $params['h'] = trim($arParams['h']) ? trim($arParams['h']) : '.h2';

      $sectionClass = array('section');
      if ($arParams['PADDING'] !== 'N') $sectionClass[] = '_with-padding';
	$arParams['BG_COLOR'] = $arParams['BG_COLOR'] ? $arParams['BG_COLOR'] : 'n';
      $sectionClass[] = 'section-color-'.strtolower($arParams['BG_COLOR']);
      if ($arParams['CLASS']) $sectionClass[] = $arParams['CLASS'];
      ?>
      <section class="<?=implode(' ',$sectionClass)?>" <?=$wrapperAdditionalParameters?>>
          <div class="w">
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
                  <?$showContent();?>
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

