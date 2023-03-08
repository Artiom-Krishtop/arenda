<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
use Bitrix\Main\Localization\Loc;
$this->setFrameMode(true);

?>

<div class="tac">
    <a class="btn btn-primary js_link_form_review" rel="nofollow" href="javascript:void(0)">
        <span class="btn-label"><?=Loc::getMessage("CITRUS_REVIEWS_ADD_NEW")?></span></a>
</div>

<div class="article">
    <div class="article-list">
		<?php
		foreach ($arResult['ITEMS'] as $arItem) :
			$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'],
				CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
			$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'],
				CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"),
				array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
			?>
            <div class="article-item" id="<?=$this->GetEditAreaId($arItem['ID'])?>">
                <div class="article-body">
                    <div class="article-user">
                        <div class="article-user-ava">
							<?
							$picture = is_array($arItem["PREVIEW_PICTURE"]) ? $arItem["PREVIEW_PICTURE"] : $arItem["DETAIL_PICTURE"];
							$width = (int)$arParams['RESIZE_IMAGE_WIDTH'] <= 0 ? 170 : (int)$arParams['RESIZE_IMAGE_WIDTH'];
							if ($arParams["DISPLAY_PICTURE"] != "N" && is_array($picture)):
								$arSmallPicture = CFile::ResizeImageGet(
									$picture["ID"],
									array(
										'width' => $width,
										'height' => (int)$arParams['RESIZE_IMAGE_HEIGHT'] <= 0 ? 170 : (int)$arParams['RESIZE_IMAGE_HEIGHT'],
									),
									BX_RESIZE_IMAGE_EXACT,
									$bInitSizes = true
								);
								?>
                                <a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><span style="min-width: <?=$width?>px; background-image: url('<?=$arSmallPicture["src"]?>');"></span></a>
							<? endif ?>
                        </div>
                        <div class="article-user-body">
							<? if ($arParams["DISPLAY_DATE"] != "N" && $arItem["DISPLAY_ACTIVE_FROM"]): ?>
                                <div class="item-date-new">
									<?=$arItem["DISPLAY_ACTIVE_FROM"]?>
                                </div>
							<? endif; ?>
							<? if ($arParams["DISPLAY_NAME"] != "N" && $arItem["NAME"]): ?>
                                <a class="article-user-name" href="<?=$arItem["DETAIL_PAGE_URL"]?>"><? echo $arItem["NAME"] ?></a>
							<? endif; ?>

							<?
							$locationProperties = array();
							if ($arItem["PROPERTIES"]["country"]["VALUE"])
							{
								$locationProperties[] = $arItem["PROPERTIES"]["country"]["VALUE"];
							}
							if ($arItem["PROPERTIES"]["city"]["VALUE"])
							{
								$locationProperties[] = $arItem["PROPERTIES"]["city"]["VALUE"];
							}
							?>
                            <span class="article-user-meta"><?=implode(", ", $locationProperties)?></span>
							<? if ($arParams["DISPLAY_PREVIEW_TEXT"] != "N" && ($arItem["PREVIEW_TEXT"] || $arItem["DETAIL_TEXT"])):
								$textParser = new CTextParser();
								$text = $arItem["PREVIEW_TEXT"] ? $arItem["PREVIEW_TEXT"] : $textParser->html_cut($arItem["DETAIL_TEXT"],
									512); ?>
                                <div class="article-desc">
									<?=$text?>

									<?
									if (count($arResult["DISPLAY_PROPERTIES"]) > 0)
									{
										?>
                                        <dl class='b-reviews-props'>
											<?
											foreach ($arResult["DISPLAY_PROPERTIES"] as $pid => $arProperty)
											{
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
													$ar = array();
													foreach ($arProperty["DISPLAY_VALUE"] as $idx => $value)
													{
														$ar[] = $value . (strlen($arProperty["DESCRIPTION"][$idx]) > 0 ? ' (' . $arProperty["DESCRIPTION"][$idx] . ')' : '');
													}

													$val = implode(' / ', $ar);
												}


												if ($arProperty["PROPERTY_TYPE"] != 'F')
												{
													?>
                                                    <dt><?=$arProperty["NAME"]?></dt>
                                                    <dd><?=$val?></dd>
													<?
												}
												else
												{
													?>
                                                    <dd class="fileprop"><?=$val?></dd><?
												}
											}
											?>
                                        </dl>
										<?
									}
									?>
                                </div>
							<? endif; ?>
                            <a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?= Loc::getMessage("CITRUS_AREALTY_READ_FULL_REVIEW") ?></a>
                        </div>
                    </div><!-- /article-user -->

            </div>
        </div>
        <?endforeach;?>
    </div><!-- .article-list -->
</div><!-- .article -->

<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
    <div class="b-reviews-list-pager b-reviews-list-pager-bottom">
		<?=$arResult["NAV_STRING"]?>
    </div>
<?endif;?>
