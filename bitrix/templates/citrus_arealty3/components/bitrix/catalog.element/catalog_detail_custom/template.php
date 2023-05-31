<?php

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$this->setFrameMode(true);

use Bitrix\Main\Localization\Loc,
	Bitrix\Main\Web\Json;

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
$addImage = function($id, $title='', $alt='', $isPanoramic = false) use (&$images, $arResult, &$videos, &$panoramic)
{
	$pic = CFile::GetFileArray($id);

	if ($pic)
	{
		if(stripos($pic['CONTENT_TYPE'], 'image') !== false && !$isPanoramic){
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

			return true;
		}
		
		if(stripos($pic['CONTENT_TYPE'], 'image') !== false && $isPanoramic){
			$preview = CFile::ResizeImageGet($id, Array('width' => 600, 'height' => 600), BX_RESIZE_IMAGE_PROPORTIONAL, $bInitSizes = true);
			$panoramic[] = array(
				'id' => 'img' . $id,
				'src' => $pic["SRC"],
				'width' => $pic['WIDTH'],
				'height' => $pic['HEIGHT'],
				'preview' => $preview,
				'alt' => strlen($alt) ? $alt : $arResult['NAME'],
				'title' => strlen($title) ? $title : $arResult['NAME'],
				'is_panoramic' => true
			);

			return true;
		}
		
		if(stripos($pic['CONTENT_TYPE'], 'video') !== false) {
			$videos[] = array(
				'id' => 'video' . $id,
				'src' => $pic["SRC"],
				'type' => $pic['CONTENT_TYPE'],
				'width' => $pic['WIDTH'],
				'height' => $pic['HEIGHT'],
				'preview' => '',
				'alt' => strlen($alt) ? $alt : $arResult['NAME'],
				'title' => strlen($title) ? $title : $arResult['NAME'],
			);

			return true;
		}
	}

	return false;
};

$images = array();
$videos = array();
$panoramic = array();

if (is_array($arResult["DETAIL_PICTURE"]))
	$addImage($arResult["DETAIL_PICTURE"]["ID"], $arResult["DETAIL_PICTURE"]["TITLE"], $arResult["DETAIL_PICTURE"]["ALT"]);
elseif (is_array($arResult["PREVIEW_PICTURE"]))
	$addImage($arResult["PREVIEW_PICTURE"]["ID"], $arResult["PREVIEW_PICTURE"]["TITLE"], $arResult["PREVIEW_PICTURE"]["ALT"]);

if (is_array($arResult["PROPERTIES"]["photo"]['VALUE'])){
	foreach ($arResult["PROPERTIES"]["photo"]['VALUE'] as $key => $id){
		$addImage($id, $arResult["PROPERTIES"]["photo"]['DESCRIPTION'][$key]);
	}
}

if (is_array($arResult["PROPERTIES"]["PANORAMIC_PHOTOS"]['VALUE'])){
	foreach ($arResult["PROPERTIES"]["PANORAMIC_PHOTOS"]['VALUE'] as $key => $id){
		$addImage($id, '', '', true);
	}
}

$detailText = strlen(trim(strip_tags($arResult["DETAIL_TEXT"]))) ? $arResult["DETAIL_TEXT"] : (strlen(trim(strip_tags($arResult["PREVIEW_TEXT"]))) ? $arResult["PREVIEW_TEXT"] : false);

$printProp = function ($propertyCode, $emptyPlaceholder = '&mdash;', $showHint = false) use (&$arResult)
{
	$value = $arResult["PROPERTIES"][$propertyCode]["VALUE"];
	if (empty($value))
	{
		return $emptyPlaceholder;
	}

	if (!is_array($value))
	{
		$value = array($value);
	}

    if ($showHint) {
        // ������� ����������� ������ ��� �������
        if (stripos($propertyCode, 'land_area') !== false)
            $value .= GetMessage("CITRUS_REALTY_HUNDRED_SQR_METERS");
        elseif (stripos($propertyCode, '_area') !== false)
            $value .= GetMessage("CITRUS_REALTY_SQR_METERS");
    }

	return implode(', ', $value);
};
$this->addExternalJs(SITE_TEMPLATE_PATH . '/js/MapAdapter.js')
?>

<?if($arParams['PRINT'] === 'Y'):?>
	<h1 class="content-title"><?=$arResult['NAME']?></h1>
<?endif;?>

<div class="section _with-padding">
    <div class="w">
        <hr class="section__border-top">
        <div class="section-inner">
            <div class="section__header">
	            <h1 id="pagetitle"><?=$arResult['NAME']?></h1>
                <div class="section-description"><?=(string)$arResult['ADDRESS']?></div>
            </div><!-- .section__header -->

            <div class="section__content catalog-section-content">
                <div class="row row-grid">
					<?if(count($images) || count($videos) || count($panoramic)):?>
                        <div class="col-lg-6 col-print-12">
							<?php if ($arParams['PRINT'] != 'Y')
							{
								?>
                                <div class="image-actions print-hidden">
	                                <?php
	                                $pdfParams = [
		                                'ID' => $arResult['ID'],
		                                'IBLOCK_ID' => $arParams["IBLOCK_ID"],
		                                'PROPERTY_CODE' => $arParams['PROPERTY_CODE'],
		                                'COMPONENT_TEMPLATE' => 'pdf_detail',
	                                ];
	                                ?>
                                    <a href="<?=SITE_DIR?>ajax/pdf.php" class="image-actions__link"
	                                       data-toggle="modal"
	                                       data-params="<?= \Citrus\Core\Components\Pdf::encodeParams($pdfParams) ?>"
	                                       data-id="<?=$arResult["ID"]?>">
                                        <span class="image-actions__link-icon"><i class="icon-letter"></i></span>
                                        <span class="image-actions__link-text"><?=Loc::getMessage("CITRUS_REALTY_PDF_SEND")?></span>
                                    </a>
                                    <a href="javascript:void(0);" class="image-actions__link js-citrus-detail-print" data-id="<?= $arResult['ID'] ?>">
                                        <span class="image-actions__link-icon"><i class="icon-print1"></i></span>
                                        <span class="image-actions__link-text"><?=Loc::getMessage("CITRUS_REALTY_PRINT_VERSION")?></span>
                                    </a>
	                                <?php if (empty($arParams['IS_JK']) || $arParams['IS_JK'] != 'Y') { ?>
                                    <a href="javascript:void(0);" class="image-actions__link add2favourites"
                                       data-id="<?=$arResult["ID"]?>">
                                        <span class="image-actions__link-icon"><i class="icon-favorites"></i></span>
                                        <span class="image-actions__link-text control-link-label"><?=Loc::getMessage("CITRUS_REALTY_ADD_TO_FAV")?></span>
                                    </a>
	                                <?php } ?>
                                </div>
								<?php
							} ?>
                            <div class="object-gallery">
                                <div class="object-gallery-previews" itemscope="" itemtype="http://schema.org/ImageGallery">

									<? $active = true; ?>
									
									<? foreach ($images as $id => $image): ?>
                                        <figure itemprop="associatedMedia" itemscope=""
                                                itemtype="http://schema.org/ImageObject"
                                                class="<? if ($active && !$id): ?>is-active<? endif; ?>">
                                            <a href="<?=$image["src"]?>" itemprop="contentUrl"
                                               data-size="<?=$image["width"]?>x<?=$image["height"]?>"
                                               title="<?=$image["title"]?>" class="gallery-previews">
                                                <img src="<?=$image["preview"]["src"]?>" itemprop="thumbnail"
                                                     title="<?=$image["title"]?>" alt="<?=$image["alt"]?>">
                                            </a>
                                            <figcaption itemprop="caption description"><?=$image["title"]?></figcaption>
                                        </figure>
										<? $active = false; ?>
									<? endforeach; ?>

									<? foreach ($videos as $id => $video): ?>
										<figure itemprop="associatedMedia" itemscope="" class="<? if ($active && !$id): ?>is-active<? endif; ?>">
											<video controls="true">
												<source type="<?= $video['type']?>" src="<?= $video['src']?>">
											</video>
										</figure>
										<? $active = false; ?>
									<? endforeach; ?>
									<? foreach ($panoramic as $id => $panoramicImg): ?>
                                        <figure 
											itemprop="associatedMedia" 
											itemscope="" itemtype="http://schema.org/ImageObject"    
											class="<? if ($active && !$id): ?>is-active<? endif; ?>"
										>
											<div class="js-panoramic-box" id="<?= $panoramicImg['id'] ?>"></div>
											<script>
												$().ready(function(){
													pannellum.viewer("<?= $panoramicImg['id'] ?>", {
														"type": "equirectangular",
														"panorama": "<?= $panoramicImg['src'] ?>",
														"autoLoad": true,
														"hfov": 200 ,
													});
												})
											</script>
                                            <figcaption itemprop="caption description"><?= $panoramicImg["title"]?></figcaption>
                                        </figure>
										<? $active = false; ?>
									<? endforeach; ?>
                                </div>
								<? if ((count($images) + count($videos) + count($panoramic)) > 1): ?>
                                    <div class="object-gallery-thumbs">
                                        <div class="swiper-container">
                                            <div class="swiper-wrapper">

												<? $active = true; ?>
												
												<? foreach ($images as $id => $image): ?>
                                                    <div class="swiper-slide">
                                                        <a href="javascript:void(0)" rel="nofollow"
                                                           class="gallery-thumbs <? if ($active && !$id): ?>is-active<? endif; ?>">
                                                            <img src="<?=$image["preview"]["src"]?>"
                                                                 title="<?=$image["title"]?>" alt="<?=$image["alt"]?>">
                                                        </a>
                                                    </div>
													<? $active = false; ?>
												<? endforeach; ?>
												<? foreach ($videos as $id => $video): ?>
													<div class="swiper-slide">
														<a href="javascript:void(0)" rel="nofollow" class="gallery-thumbs video <? if ($active && !$id): ?>is-active<? endif; ?>">
															<video class="gallery-thumbs__video" id="thumbs-video-<?= $id ?>">
																<source type="<?= $video['type']?>" src="<?= $video['src']?>">
															</video>
														</a>
													</div>

													<? $active = false; ?>
												<? endforeach; ?>
												<? foreach ($panoramic as $id => $panoramicImg): ?>
                                                    <div class="swiper-slide">
                                                        <a href="javascript:void(0)" rel="nofollow"
                                                           class="gallery-thumbs panoramic <? if ($active && !$id): ?>is-active<? endif; ?>">
                                                            <img src="<?=$panoramicImg["preview"]["src"]?>"
                                                                 title="<?=$panoramicImg["title"]?>" alt="<?=$panoramicImg["alt"]?>">
                                                        </a>
                                                    </div>

													<? $active = false; ?>
												<? endforeach; ?>
                                            </div>
                                            <div class="swiper-scrollbar"></div>
                                        </div>
                                    </div>
								<? endif; ?>
                            </div>
                        </div>
					<? endif; ?>
                    <div class="<?=count($images) || count($videos) || count($panoramic) ? 'col-lg-6 col-print-12' : 'col-xs-12 col-print-12'?>">
                        <div class="object-info">

                            <div class="object-option dl-menu">
                                <? if ((string)$arResult['ADDRESS']): ?>
                                    <div class="dl_element object-map print-hidden">
                                        <span><?= Loc::getMessage("CITRUS_AREALTY_OBJECT_ADDRESS") ?></span>
                                        <span>
                                            <a href="javascript:void(0);" class="map-link"
                                               data-address="<?=(string)$arResult['ADDRESS']?>"
                                               data-coords="<?=Json::encode($arResult['ADDRESS']->getCoordinates())?>"><?=(string)$arResult['ADDRESS']?>
                                            </a>
                                        </span>
                                    </div>
                                <? endif; ?>

                                <? if ((string)$arResult['PROPERTIES']['NEW_FLOOR']['VALUE']): ?>
                                    <div class="dl_element object-map print-hidden">
                                        <span>Этаж:</span>
                                        <span>
                                            <?=(string)$arResult['PROPERTIES']['NEW_FLOOR']['VALUE']?>
                                        </span>
                                    </div>
                                <? endif; ?>

                                <? if ((string)count($arResult['PROPERTIES']['NEW_ROOMS_AREA']['VALUE'])): ?>
                                    <div class="dl_element object-map print-hidden">
                                        <span>Количество комнат:</span>
                                        <span>
                                            <?=(string)count($arResult['PROPERTIES']['NEW_ROOMS_AREA']['VALUE'])?>
                                        </span>
                                    </div>
                                <? endif; ?>

                                <? if ((string)$arResult['PROPERTIES']['common_area']['VALUE']): ?>
                                    <div class="dl_element object-map print-hidden">
                                        <span>Общая площадь:</span>
                                        <span>
                                            <?=(string)$arResult['PROPERTIES']['common_area']['VALUE']?>
                                            <span class="catalog-item-price__unit"> / <?=GetMessage('CITRUS_REALTY_SQR_METERS')?></span>
                                        </span>
                                    </div>
                                <? endif; ?>

                                <? if ($arResult['PROPERTIES']['NEW_ROOMS_AREA']['VALUE']): ?>
                                    <div class="dl_element object-map print-hidden">
                                        <span>Площадь комнат:</span>
                                        <span>
                                            <?= implode(' ', array_map(function($item){
                                                return $item . '<span class="catalog-item-price__unit"> /' . GetMessage('CITRUS_REALTY_SQR_METERS') . '</span>';
                                            }, $arResult['PROPERTIES']['NEW_ROOMS_AREA']['VALUE']))?>
                                        </span>
                                    </div>
                                <? endif; ?>

                                <? if ($arResult['PROPERTIES']['NEW_ROOMS_TYPE']['VALUE']): ?>
                                    <div class="dl_element object-map print-hidden">
                                        <span>Тип помещения:</span>
                                        <span>
                                            <?= implode(', ', $arResult['PROPERTIES']['NEW_ROOMS_TYPE']['VALUE'])?>
                                        </span>
                                    </div>
                                <? endif; ?>

                                <? if ($arResult['PROPERTIES']['NEW_COMMERCIAL_FEATURES']['VALUE']): ?>
                                    <div class="dl_element object-map print-hidden">
                                        <span>Особенности помещения:</span>
                                        <span>
                                            <?= implode(', ', $arResult['PROPERTIES']['NEW_COMMERCIAL_FEATURES']['VALUE'])?>
                                        </span>
                                    </div>
                                <? endif; ?>

								<?
								$skipProperties = array("cost", "address", "photo", "contact");

								if (count($arResult["DISPLAY_PROPERTIES"]) > 0)
								{
									?>
									<?
									foreach ($arResult["DISPLAY_PROPERTIES"] as $pid => $arProperty)
									{
										if (array_search($pid, $skipProperties) !== false)
										{
											continue;
										}

										if ($arProperty["PROPERTY_TYPE"] == 'F')
										{
											if (!is_array($arProperty['VALUE'])) {
												$arProperty['VALUE'] = array($arProperty['VALUE']);
												$arProperty['DESCRIPTION'] = array($arProperty['DESCRIPTION']);
											}
											$arProperty["DISPLAY_VALUE"] = Array();
											foreach ($arProperty["VALUE"] as $idx=>$value) {
												$path = CFile::GetPath($value);
												$desc = strlen($arProperty["DESCRIPTION"][$idx]) > 0 ? $arProperty["DESCRIPTION"][$idx] : bx_basename($path);
												if (strlen($path) > 0)
												{
													$ext = pathinfo($path, PATHINFO_EXTENSION);
													$fileinfo = '';
													if ($arFile = CFile::GetByID($value)->Fetch())
														$fileinfo .= ' (' . $ext . ', ' . round($arFile['FILE_SIZE']/1024) . GetMessage('FILE_SIZE_Kb') . ')';
													$arProperty["DISPLAY_VALUE"][] = "<a href=\"{$path}\" class=\"file file-{$ext}\">" . $desc . "</a>" . $fileinfo;
												}
											}
											$val = is_array($arProperty["DISPLAY_VALUE"]) ? implode(', ', $arProperty["DISPLAY_VALUE"]) : $arProperty['DISPLAY_VALUE'];
										}
										else
										{
											if (!is_array($arProperty["DISPLAY_VALUE"]))
												$arProperty["DISPLAY_VALUE"] = Array($arProperty["DISPLAY_VALUE"]);

											array_map(function (&$v) {
												$v = strip_tags($v);
											}, $arProperty["DISPLAY_VALUE"]);

											// ������� ����������� ������ ��� �������
											if (stripos($pid, 'land_area') !== false)
												foreach ($arProperty["DISPLAY_VALUE"] as &$val)
													$val .= GetMessage("CITRUS_REALTY_HUNDRED_SQR_METERS");
											elseif (stripos($pid, '_area') !== false)
												foreach ($arProperty["DISPLAY_VALUE"] as &$val)
													$val .= GetMessage("CITRUS_REALTY_SQR_METERS");

											$ar = array();
											foreach ($arProperty["DISPLAY_VALUE"] as $idx=>$value)
												$ar[] = $value . (strlen($arProperty["DESCRIPTION"][$idx]) > 0 ? ' (' . $arProperty["DESCRIPTION"][$idx] . ')': '');

											$val = implode(', ', $ar);
										}

										?>
                                        <div class="dl_element">
                                            <span><?=$arProperty["NAME"]?></span>
                                            <span><?=$val?></span>
                                        </div>
										<?
									}
									?>
									<?
								}
								$offerFields = \Citrus\Core\array_get($arResult, 'OFFERS_FIELDS.display');
								if (is_array($offerFields))
								{
									foreach ($arResult["OFFERS_FIELDS"]['display'] as $title => $val) {
										?>
										<div class="dl_element">
											<span><?= $title ?></span>
											<span><?= $val ?></span>
										</div>
										<?php
									}
								}
								?>
								<? if ($arResult['PROPERTIES']['RENTAL_COMPANY']['VALUE_TXT']): ?>
                                    <div class="dl_element object-map print-hidden">
                                        <span>Компания-арендодатель:</span>
                                        <span>
                                            <?= $arResult['PROPERTIES']['RENTAL_COMPANY']['VALUE_TXT']?>
                                        </span>
                                    </div>
                                <? endif; ?>
                            </div>
							<? if ($arResult["PROPERTIES"]["cost"]["VALUE"]): ?>
								<?
								$priceAdditional = '';
								/*if ($printProp("cost_unit", ''))
								{
									$priceAdditional .= '<span class="catalog-item-price__unit"> / ' . str_replace(GetMessage("CITRUS_AREALTY_COST_UNIT_SEARCH"),
											GetMessage("CITRUS_AREALTY_COST_UNIT_REPLACE"),
											$printProp("cost_unit", '')) . '</span>';
								}*/
								if ($printProp("cost_period", ''))
								{
									$priceAdditional .= ' <span class="catalog-item-price__period">' . GetMessage("CITRUS_AREALTY_COST_PERIOD_IN") . $printProp("cost_period",
											'') . '</span>';
								} ?>
                                <div class="object-price_new">
                                    <?if($arResult["PROPERTIES"]["price_for_meter"]["VALUE"]){?>
                                        <div class="price_for_metr_block" >
                                            <span
                                                    data-currency-base="<?=$arResult["PROPERTIES"]["price_for_meter"]["VALUE"]?>"
                                                    data-currency-icon=""><?=$arResult["PROPERTIES"]["price_for_meter"]["VALUE"]?></span>
                                            <span class="catalog-item-price__unit"> / <?=GetMessage('CITRUS_REALTY_SQR_METERS')?></span>
                                        </div>
                                    <?}?>
									<? #currency set in js?>
                                    <span data-currency-base="<?=$printProp("cost", 0)?>"
                                          data-currency-icon="">&nbsp;</span>
									<?=$priceAdditional?>
                                </div>
                                <script>currency.updateHtml($('.object-price_new'))</script>
							<? endif; ?>
	                        <?php if ($arResult["OFFERS_FIELDS"]['raw']["MIN_COST"]) { ?>
		                        <div class="object-price_new">
			                        <? #currency set in js?>
			                        <?=Loc::getMessage('CITRUS_TEMPLATE_PRICE_FROM')?>
			                        <span data-currency-base="<?=$arResult["OFFERS_FIELDS"]['raw']["MIN_COST"]?>"
			                              data-currency-icon="">&nbsp;</span>
		                        </div>
		                        <script>currency.updateHtml($('.object-price_new'))</script>
	                        <?php } ?>

                            <br>
                            <div class="object-info_footer">
                                <a class="btn btn-primary print-hidden"
                                   rel="nofollow" data-toggle="modal"
                                   href="<?=SITE_DIR?>ajax/request_shedule.php?id=<?=$arResult["ID"]?>&RENT_MAIL=<?= $arResult['RENT_EMAIL']?>">
                                    <span class="btn-label"><?=Loc::getMessage("CITRUS_AREALTY_BTN_REQUEST_SHEDULE")?></span>
                                </a>
	                            <?
	                            if ($arResult['CONTACT'])
	                            {
	                            	?>
		                            <a class="personal_manager_link print-hidden" href="javascript:void(0)">
			                            <i class="icon-owner"></i>
			                            <span class="btn-label"><?=Loc::getMessage("CITRUS_AREALTY_BTN_PERSONAL_MANAGER")?></span>
		                            </a>
		                            <?php
	                            }
	                            ?>
                            </div>
							<div class="b-ya-share">
								<div class="ya-share2" data-curtain data-size="l" data-services="vkontakte,odnoklassniki,telegram,viber,whatsapp"></div>
								<script src="https://yastatic.net/share2/share.js"></script>
							</div>
                            <?if(!empty($arResult['PROPERTIES']['DESCRIPTION']['~VALUE']['TEXT'])):?>
                                <?= $arResult['PROPERTIES']['DESCRIPTION']['~VALUE']['TEXT']; ?>
                            <?endif;?>
                        </div>
                    </div>
                    <div class="col-xs-12 col-print-12">
                        <?if(!empty($arResult['PROPERTIES']['rooms_html']['~VALUE']['TEXT'])):?>
                            <?= $arResult['PROPERTIES']['rooms_html']['~VALUE']['TEXT']; ?>
                        <?endif;?>
                    </div>
                </div>

	            <?php if ($arResult['PROPERTIES']['callout']['VALUE'])
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
			    } ?>

                <div id="map<?= $arResult['ID']?>" style="height: 300px; margin: 30px 0"></div>
                <div class="object-text">
					<?=$detailText?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        let coords = <?=Json::encode($arResult['ADDRESS']->getCoordinates())?>;
        let address = '<?=(string)$arResult['ADDRESS']?>';

        let mapAdapter<?= $arResult['ID']?> = new MapAdapter({
            map_id: "map<?= $arResult['ID']?>",
            center: {
                lat: coords[0],
                lng: coords[1]
            },
            object: "ymaps",
            zoom: 13
        });

        mapAdapter<?= $arResult['ID']?>.addMarker({
            title: address,
            lat: coords[0],
            lng: coords[1],
            content: address,
        });
    });
</script>
<?php

$this->SetViewTarget('element-page-bottom');

if ($arResult['CONTACT'])
{
	?><a name="personal_manager" id="personal_manager"></a><?php

	$APPLICATION->IncludeComponent(
		"citrus.core:include",
		".default",
		[
			"AREA_FILE_SHOW" => "component",
			"_COMPONENT" => "citrus:template",
			"_COMPONENT_TEMPLATE" => "staff-block",
			"ITEM" => $arResult['CONTACT'],
			"h" => "h2.h1",
			"TITLE" => Loc::getMessage("CITRUS_AREATLY_PERSONAL_MANAGER_BLOCK"),
			"DESCRIPTION" => Loc::getMessage("CITRUS_AREATLY_PERSONAL_MANAGER_BLOCK_DESc"),
			"PAGE_SECTION" => "Y",
			"PADDING" => "Y",
		],
		$component,
		['HIDE_ICONS' => 'Y']
	);
}
