<?php

use Bitrix\Main\Localization\Loc,
	Citrus\Arealty;

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

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
$this->setFrameMode(true);

// nook add image title from SEO module
$addImage = function($id, $title='', $alt='') use (&$images, $arResult)
{
	$pic = CFile::GetFileArray($id);
	if ($pic)
	{
		$preview = CFile::ResizeImageGet($id, Array('width' => 600, 'height' => 600), BX_RESIZE_IMAGE_PROPORTIONAL, $bInitSizes = true);
		$images[] = array(
            'id' => 'img' . $id,
			'src' => $pic["SRC"],
			'width' => $pic['WIDTH'],
			'height' => $pic['HEIGHT'],
			'preview' => $preview,
			'alt' => strlen($alt) ? $alt : $arResult['NAME'],
			'title' => strlen($title) ? $title : $arResult['NAME'],
		);
	}
};

$images = array();
if (is_array($arResult["DETAIL_PICTURE"]))
	$addImage($arResult["DETAIL_PICTURE"]["ID"], $arResult["DETAIL_PICTURE"]["TITLE"], $arResult["DETAIL_PICTURE"]["ALT"]);
elseif (is_array($arResult["PREVIEW_PICTURE"]))
	$addImage($arResult["PREVIEW_PICTURE"]["ID"], $arResult["PREVIEW_PICTURE"]["TITLE"], $arResult["PREVIEW_PICTURE"]["ALT"]);
if (is_array($arResult["PROPERTIES"]["photo"]['VALUE']))
	foreach ($arResult["PROPERTIES"]["photo"]['VALUE'] as $key => $id)
	{
		$addImage($id, $arResult["PROPERTIES"]["photo"]['DESCRIPTION'][$key]);
	}

if (empty($images)) $images["no-photo"] = array( "src" => SITE_TEMPLATE_PATH."/images/no-photo.png");

$previewText = strlen(trim(strip_tags($arResult["PREVIEW_TEXT"]))) ? $arResult["PREVIEW_TEXT"] : '';
$detailText = strlen(trim(strip_tags($arResult["DETAIL_TEXT"]))) ? $arResult["DETAIL_TEXT"] : '';

?>
<div class="object">
    <div class="row row-grid">
        <?if(count($images)):?>
            <div class="col-lg-6">
                <div class="object-gallery">
                    <div class="object-gallery-previews" itemscope="" itemtype="http://schema.org/ImageGallery">
                        <?foreach ($images as $id=>$image):?>
                            <figure itemprop="associatedMedia" itemscope="" itemtype="http://schema.org/ImageObject" class="<?if(!$id):?>is-active<?endif;?>">
                                <a href="<?=$image["src"]?>" itemprop="contentUrl" data-size="<?=$image["width"]?>x<?=$image["height"]?>" title="<?=$image["title"]?>" class="gallery-previews">
                                    <img src="<?=$image["preview"]["src"]?>" itemprop="thumbnail" alt="<?=$image["title"]?>">
                                </a>
                                <figcaption itemprop="caption description"><?=$image["title"]?></figcaption>
                            </figure>
                        <?endforeach;?>
                    </div>
                    <?if(count($images)>1):?>
                        <div class="object-gallery-thumbs">
                            <div class="swiper-container">
                                <div class="swiper-wrapper">
                                    <?foreach ($images as $id=>$image):?>
                                        <div class="swiper-slide">
                                            <a href=" javascript:void(0)" rel="nofollow" class="gallery-thumbs <?if(!$id):?>is-active<?endif;?>">
                                                <img src="<?=$image["preview"]["src"]?>" alt="<?=$image["title"]?>">
                                            </a>
                                        </div>
                                    <?endforeach;?>
                                </div>
                            </div>
                            <div class="swiper-scrollbar"></div>
                        </div>
                    <?endif;?>
                </div>
            </div>
        <?endif;?>
        <div class="<?=count($images) ? 'col-lg-6' : 'col-xs-12'?>">
            <div class="object-info">
		        <? if ((string)$arResult['ADDRESS']): ?>
                    <div class="object-address">
                        <span><?=$arResult['ADDRESS']?></span>
                    </div>
                    <div class="object-map print-hidden">
                        <a href="javascript:void(0);" class="map-link"
                           data-address="<?=(string)$arResult['ADDRESS']?>"
                           data-coords="<?=\Bitrix\Main\Web\Json::encode($arResult['ADDRESS']->getCoordinates())?>"
                        >
                            <span class="object-map-icon fa fa-fw fa-map-marker"></span>
                            <span class="object-map-label"><?=GetMessage("CITRUS_REALTY_ON_MAP")?></span>
                        </a>
                    </div>
		        <? endif; ?>

	            <? if ($arResult["OFFERS_FIELDS"]['raw']["MIN_COST"]): ?>
		            <div class="object-price">
			            <?=Loc::getMessage('CITRUS_TEMPLATE_PRICE_FROM')?> <span class=""><?=number_format(Arealty\Helper::convertCost($arResult["OFFERS_FIELDS"]['raw']["MIN_COST"]), 0, ',', ' ')?></span>
			            <span class="currency-icon"><span
					            class="<?= strtolower(Arealty\Helper::getSelectedCurrency()) ?>"></span></span>
		            </div>
	            <? endif; ?>

		        <?
		        $skipProperties = array("cost", "address", "photo", "contact");
		        if (count($arResult["DISPLAY_PROPERTIES"]) > 0)
		        {
			        ?>
                    <dl class="object-option dl-menu">
				        <?
				        foreach ($arResult["DISPLAY_PROPERTIES"] as $pid => $arProperty)
				        {
					        if (array_search($pid, $skipProperties) !== false)
					        {
						        continue;
					        }

					        if ($arProperty["PROPERTY_TYPE"] == 'F')
					        {
						        if (!is_array($arProperty['VALUE']))
						        {
							        $arProperty['VALUE'] = array($arProperty['VALUE']);
							        $arProperty['DESCRIPTION'] = array($arProperty['DESCRIPTION']);
						        }
						        $arProperty["DISPLAY_VALUE"] = Array();
						        foreach ($arProperty["VALUE"] as $idx => $value)
						        {
							        $path = CFile::GetPath($value);
							        $desc = strlen($arProperty["DESCRIPTION"][$idx]) > 0 ? $arProperty["DESCRIPTION"][$idx] : bx_basename($path);
							        if (strlen($path) > 0)
							        {
								        $ext = pathinfo($path, PATHINFO_EXTENSION);
								        $fileinfo = '';
								        if ($arFile = CFile::GetByID($value)->Fetch())
								        {
									        $fileinfo .= ' (' . $ext . ', ' . round($arFile['FILE_SIZE'] / 1024) . GetMessage('FILE_SIZE_Kb') . ')';
								        }
								        $arProperty["DISPLAY_VALUE"][] = "<a href=\"{$path}\" class=\"file file-{$ext}\">" . $desc . "</a>" . $fileinfo;
							        }
						        }
						        $val = is_array($arProperty["DISPLAY_VALUE"]) ? implode(', ',
							        $arProperty["DISPLAY_VALUE"]) : $arProperty['DISPLAY_VALUE'];
					        }
					        else
					        {
						        if (!is_array($arProperty["DISPLAY_VALUE"]))
						        {
							        $arProperty["DISPLAY_VALUE"] = Array($arProperty["DISPLAY_VALUE"]);
						        }

						        array_map(function (&$v) {
							        $v = strip_tags($v);
						        }, $arProperty["DISPLAY_VALUE"]);

						        if (stripos($pid, '_area') !== false)
						        {
							        foreach ($arProperty["DISPLAY_VALUE"] as &$val)
							        {
								        $val .= GetMessage("CITRUS_REALTY_SQR_METERS");
							        }
						        }

						        $ar = array();
						        foreach ($arProperty["DISPLAY_VALUE"] as $idx => $value)
						        {
							        $ar[] = $value . (strlen($arProperty["DESCRIPTION"][$idx]) > 0 ? ' (' . $arProperty["DESCRIPTION"][$idx] . ')' : '');
						        }

						        $val = implode(', ', $ar);
					        }

					        ?>
                            <dt><?=$arProperty["NAME"]?></dt>
                            <dd><?=$val?></dd>
					        <?
				        }

				        foreach ($arResult["OFFERS_FIELDS"]['display'] as $title => $val)
				        {
					        ?>
                            <dt><?=$title?></dt>
                            <dd><?=$val?></dd>
					        <?
				        }
				        {

				        }
				        ?>
                    </dl>
			        <?
		        }
		        ?>
            </div>
        </div>
    </div>
	<?

	if ($previewText)
	{
		?>
        <div class="object-text">
			<?=$previewText?>
        </div>
		<?
	}

	if ($arResult['PROPERTIES']['callout']['VALUE'])
    {
        ?><?$APPLICATION->IncludeComponent(
            "citrus:realty.callout",
            ".default",
            array(
                "IBLOCK_ID" => $arResult['PROPERTIES']['callout']['LINK_IBLOCK_ID'],
                "ID" => $arResult['PROPERTIES']['callout']['VALUE']
            ),
            $component
        )?><?
    }

	if ($detailText)
	{
		?>
        <div class="object-text">
			<?=$detailText?>
        </div>
		<?
	}

	?>
    <div class="object-footer">
        <button type="button" class="btn btn-secondary add2favourites" data-id="<?=$arResult["ID"]?>">
            <span class="btn-icon icon-star-full"></span>
            <span class="btn-label control-link-label"><?=GetMessage("CITRUS_REALTY_ADD_TO_FAV")?></span>
        </button>
        <button type="button" onclick="window.print();" class="btn btn-secondary">
            <span class="btn-icon fa fa-fw fa-print"></span>
            <span class="btn-label"><?=GetMessage("CITRUS_REALTY_PRINT_VERSION")?></span>
        </button>
		<button type="button" class="btn btn-secondary js-citrus-pdf-send" data-id="<?= $arResult["ID"] ?>">
			<span class="btn-label"><?=GetMessage("CITRUS_REALTY_PDF_SEND")?></span>
		</button>
    </div>

</div>

<? //continue in component_epilog?>
