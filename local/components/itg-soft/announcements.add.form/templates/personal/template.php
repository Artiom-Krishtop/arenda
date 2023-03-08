<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
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
$this->setFrameMode(false);

if (!empty($arResult['PROPERTY_LIST_FULL'] && !empty($arResult['TEMPLATE_TABS']))):?>
	<section class="account-content">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/vanillajs-datepicker@1.2.0/dist/css/datepicker.min.css">
    <script src="https://cdn.jsdelivr.net/npm/vanillajs-datepicker@1.2.0/dist/js/datepicker.min.js"></script>

		<section class="account-content__inner account-content__inner-visible">
			<?
				$APPLICATION->IncludeComponent(
					"bitrix:breadcrumb",
					"account",
					Array(
						"START_FROM" => "0", 
						"PATH" => "", 
						"SITE_ID" => SITE_ID 
					)
				);
			?>
			<div class="account-content__new">
				<h1 class="account-content__title"><? $APPLICATION->ShowTitle()?></h1>
			</div>
			<div class="account-content__new-nav">
				<? $firstTab = true; ?>
				<? foreach ($arResult['TEMPLATE_TABS'] as $name => $tab):?>
					<h3 class="account-content__new-item<?= $firstTab ? ' account-content__new-item--active' : ''?>" data-nav-target="<?= $name?>"><?= GetMessage('IBLOCK_FORM_' . $name)?></h3>
					<? $firstTab = false;?>
				<? endforeach; ?>
			</div>
			
			<? if (!empty($arResult["ERRORS"]) || strlen($arResult["MESSAGE"]) > 0): ?>
				<div class="account-content__notice-block">
					<? if (!empty($arResult["ERRORS"])):?>
						<?ShowError(implode("<br />", $arResult["ERRORS"]))?>
					<?endif;
					if (strlen($arResult["MESSAGE"]) > 0):?>
						<?ShowNote($arResult["MESSAGE"])?>
					<?endif?>
				</div>
			<? endif; ?>

			<form action="<?=POST_FORM_ACTION_URI?>" class="account-content__new-blocks" method="post" enctype="multipart/form-data" name="announcements_add">
				<input type="hidden" name="iblock_submit" value="<?=GetMessage("IBLOCK_FORM_SUBMIT")?>" />
				<input type="hidden" name="PROPERTY[IBLOCK_SECTION][]" value="<?= array_key_first($arResult['PROPERTY_LIST_FULL']['IBLOCK_SECTION']["ENUM"])?>" />
				
				<?=bitrix_sessid_post()?>
				<?if ($arParams["MAX_FILE_SIZE"] > 0):?><input type="hidden" name="MAX_FILE_SIZE" value="<?=$arParams["MAX_FILE_SIZE"]?>" /><?endif?>

				<? $firstTab = true; ?>
				<? $lastTab = array_key_last($arResult['TEMPLATE_TABS']);?>

				<? foreach ($arResult['TEMPLATE_TABS'] as $name => $tab):?>
					<div class="account-content__new-block<?= $firstTab ? ' account-content__new-block--active' : ''?>" data-target="<?= $name?>">
						<div class="form">
							<div class="form__inner">
								<div class="form__column<?= in_array($name, array('PHOTO', 'PANORAMIC_PHOTO')) ? ' tab-photo__column' : ''?>">
									<? foreach ($tab as $key => $fieldName): ?>
										<? if ($key == round(count($tab) / 2)):?>
											</div><div class="form__column">
										<? endif; ?>
										<? if (!empty($arResult["PROPERTY_LIST_FULL"][$fieldName])): ?>
											<? 
											$field = $arResult["PROPERTY_LIST_FULL"][$fieldName];

											if (intval($propertyID) > 0) {
												if (
													$field["PROPERTY_TYPE"] == "T" &&
													$field["ROW_COUNT"] == "1"
												) {
													$field["PROPERTY_TYPE"] = "S";
												} elseif (
													(
														$field["PROPERTY_TYPE"] == "S" ||
														$field["PROPERTY_TYPE"] == "N"
													)
													&&
													$field["ROW_COUNT"] > "1"
												) {
													$field["PROPERTY_TYPE"] = "T";
												}
											} elseif (($propertyID == "TAGS") && CModule::IncludeModule('search')) {
												$field["PROPERTY_TYPE"] = "TAGS";
											}
					
											if ($field["MULTIPLE"] == "Y") {
												$inputNum = ($arParams["ID"] > 0 || count($arResult["ERRORS"]) > 0) ? count($arResult["ELEMENT_PROPERTIES"][$field['ID']]) : 0;
												$inputNum += $field["MULTIPLE_CNT"];
											} else {
												$inputNum = 1;
											}
					
											if($field["GetPublicEditHTML"]){
												$INPUT_TYPE = "USER_TYPE";
											} else {
												$INPUT_TYPE = $field["PROPERTY_TYPE"];
											}

											if (!empty($field['NAME'])){
												$label = $field['NAME'];
											}else {
												$label = GetMessage('IBLOCK_FORM_' . $fieldName);
											}

											switch ($INPUT_TYPE) {
												case 'USER_TYPE':?>
													<div class="form__item<?= $field["USER_TYPE"] == "DateTime" ? ' form__item-date' : ''?>">
														<label class="form__label<?= $field['IS_REQUIRED'] == 'Y' || in_array($fieldName, $arResult["PROPERTY_REQUIRED"]) ? ' required' : ''?>" for="PROPERTY[<?=$field['ID']?>][<?=$i?>]"><?= $label?>:</label>
														<?for ($i = 0; $i<$inputNum; $i++)
														{
															if ($arParams["ID"] > 0 || count($arResult["ERRORS"]) > 0)
															{
																$value = intval($field['ID']) > 0 ? $arResult["ELEMENT_PROPERTIES"][$field['ID']][$i]["~VALUE"] : $arResult["ELEMENT"][$field['ID']];
																$description = intval($field['ID']) > 0 ? $arResult["ELEMENT_PROPERTIES"][$field['ID']][$i]["DESCRIPTION"] : "";
															}
															elseif ($i == 0)
															{
																$value = intval($field['ID']) <= 0 ? "" : $arResult["PROPERTY_LIST_FULL"][$field['CODE']]["DEFAULT_VALUE"];
																$description = "";
															}
															else
															{
																$value = "";
																$description = "";
															}

															echo call_user_func_array($arResult["PROPERTY_LIST_FULL"][$field['CODE']]["GetPublicEditHTML"],
																array(
																	$arResult["PROPERTY_LIST_FULL"][$field['CODE']],
																	array(
																		"VALUE" => $value,
																		"DESCRIPTION" => $description,
																	),
																	array(
																		"VALUE" => "PROPERTY[".$field['ID']."][".$i."]",
																		"DESCRIPTION" => "PROPERTY[".$field['ID']."][".$i."][DESCRIPTION]",
																		"FORM_NAME"=>"announcements_add",
																	),
																));
														}?>
													</div>
													<? break;
												case "S":
												case "N":?>
													<div class="form__item<?= $field["USER_TYPE"] == "DateTime" ? ' form__item-date' : ''?>">
														<label class="form__label<?= $field['IS_REQUIRED'] == 'Y' || in_array($fieldName, $arResult["PROPERTY_REQUIRED"]) ? ' required' : ''?>" for="PROPERTY[<?=$field['ID']?>][<?=$i?>]"><?= $label?>:</label>

														<?for ($i = 0; $i < $inputNum; $i++) {
															if ($arParams["ID"] > 0 || count($arResult["ERRORS"]) > 0) {
																$value = intval($field['ID']) > 0 ? $arResult["ELEMENT_PROPERTIES"][$field['ID']][$i]["VALUE"] : $arResult["ELEMENT"][$fieldName];
															} elseif ($i == 0){
																$value = intval($field['ID']) <= 0 ? "" : $field["DEFAULT_VALUE"];
															} else {
																$value = "";
															}?>

															<input 
																class="form__input<?= $field["MULTIPLE"] == "Y" ? ' multiple' : ''?><?= $field["USER_TYPE"] == "DateTime" ? ' form__input-date' : '' ?>" 
																type="text" 
																name="PROPERTY[<?= !empty($field['ID']) ? $field['ID'] : $fieldName ?>][<?=$i?>]" 
																value="<?=$value?>" 
																<? if($field["USER_TYPE"] == "DateTime"):?>
																	placeholder="DD.MM.YYYY"
																<?endif; ?>
															/>


														<? } ?>
														<? if ($field['MULTIPLE'] == 'Y'):?>
															<button class="account-content__btn-add-field js-btn-add-field" data-prop-id="<?= $field['ID']?>" data-prop-order="<?= $inputNum?>"><?= GetMessage('IBLOCK_FORM_ADD_FIELD')?></button>
														<? endif; ?>
													</div><?
												break;
												
												case 'L':

													$type = $field["MULTIPLE"] == "Y" ? "multiselect" : "dropdown";

													switch ($type):
														case "checkbox":
														case "radio":
														case "dropdown":
														case "multiselect":
														?>
															<div class="form__item">
																<label class="form__label<?= $field['IS_REQUIRED'] == 'Y' || in_array($fieldName, $arResult["PROPERTY_REQUIRED"]) ? ' required' : ''?>" for=""><?= $label?>:</label>
																<select class="form__select" name="PROPERTY[<?= !empty($field['ID']) ? $field['ID'] : $fieldName ?>]<?=$type=="multiselect" ? "[]\" size=\"".$field["ROW_COUNT"]."\" multiple=\"multiple" : ""?>">
																	<option value=""><?echo GetMessage("CT_BIEAF_PROPERTY_VALUE_NA")?></option>
																	<?
																		if (intval($field['ID']) > 0){
																			$sKey = "ELEMENT_PROPERTIES";	
																		}else {
																			$sKey = "ELEMENT";
																		}

																		foreach ($field["ENUM"] as $key => $arEnum){
																			$checked = false;

																			if ($arParams["ID"] > 0 || count($arResult["ERRORS"]) > 0) {
																				foreach ($arResult[$sKey][$field['ID']] as $elKey => $arElEnum) {
																					if ($key == $arElEnum["VALUE"]){
																						$checked = true;
																						break;
																					}
																				}

																				if($fieldName == 'ACTIVE' && $key == $arResult['ELEMENT']['ACTIVE']){
																					$checked = true;
																				}
																			} else {
																				if ($arEnum["DEF"] == "Y") $checked = true;
																			}
																			?>
																				<option value="<?=$key?>" <?=$checked ? " selected=\"selected\"" : ""?>><?=$arEnum["VALUE"]?></option>
																			<?
																		}
																	?>	
																</select>
															</div>															
														<?
														break;
													endswitch;
												break;
												case "F":	
													$values = intval($field['ID']) > 0 ? $arResult["ELEMENT_PROPERTIES"][$field['ID']] : $arResult["ELEMENT"][$field['ID']];
													$inputValues = array();

													if (!empty($values)){
														foreach ($values as $key => $value) {
															$inputValues[] = $value['VALUE'];
														}
													}

													$APPLICATION->IncludeComponent(
														"bitrix:main.file.input", 
														"personal-file-input",
														array(
															"INPUT_NAME" => "PROPERTY_FILE_" . $field['ID'],
															"MULTIPLE" => "Y",
															"INPUT_VALUE" => $inputValues,
															"MODULE_ID" => "iblock",
															"MAX_FILE_SIZE" => "",
															"ALLOW_UPLOAD" => "A",
															"FIELD_ID" => $field['ID'],
															"ALLOW_UPLOAD_EXT" => "jpg, gif, bmp, png, jpeg, svg, mp4"
														),
														false
													);?>
					
												<?break;
											}?>
										<?endif; ?>
									<? endforeach; ?>
								</div>
							</div>
						</div>
						<? if ($name == 'ADRESS' && isset($arResult["PROPERTY_LIST_FULL"]['geodata'])):?>
							<?$geodataField = $arResult["PROPERTY_LIST_FULL"]['geodata']; ?>
							<div class="account-content__new-map">
								<h3 class="account-content__new-map_title">
									<?= GetMessage('GEO_DATA_ON_MAP')?>
								</h3>
								<div class="account-content__new-map_yandex">
								<?for ($i = 0; $i < 1; $i++){
									if ($arParams["ID"] > 0 || count($arResult["ERRORS"]) > 0)
									{
										$value = intval($geodataField['ID']) > 0 ? $arResult["ELEMENT_PROPERTIES"][$geodataField['ID']][$i]["~VALUE"] : $arResult["ELEMENT"][$geodataField['ID']];
										$description = intval($geodataField['ID']) > 0 ? $arResult["ELEMENT_PROPERTIES"][$geodataField['ID']][$i]["DESCRIPTION"] : "";
									}
									elseif ($i == 0)
									{
										$value = intval($geodataField['ID']) <= 0 ? "" : $arResult["PROPERTY_LIST_FULL"][$geodataField['CODE']]["DEFAULT_VALUE"];
										$description = "";
									}
									else
									{
										$value = "";
										$description = "";
									}

									echo call_user_func_array($arResult["PROPERTY_LIST_FULL"][$geodataField['CODE']]["GetPublicEditHTML"],
										array(
											$arResult["PROPERTY_LIST_FULL"][$geodataField['CODE']],
											array(
												"VALUE" => $value,
												"DESCRIPTION" => $description,
											),
											array(
												"VALUE" => "PROPERTY[".$geodataField['ID']."][".$i."]",
												"DESCRIPTION" => "PROPERTY[".$geodataField['ID']."][".$i."][DESCRIPTION]",
												"FORM_NAME"=>"announcements_add",
											),
										));
								}?>
								</div>
							</div>
						<? endif; ?>
						<? if ($firstTab):?>
							<button class="account-content__btn js-toggle-tab-btn" data-pos="<?= $name == $lastTab ? 'send' : 'next'?>"><?= $name == $lastTab ? GetMessage('IBLOCK_FORM_SUBMIT') : GetMessage('IBLOCK_FORM_NEXT')?></button>
						<? else: ?>
							<div class="account-content__btns">
								<button class="account-content__btn js-toggle-tab-btn" data-pos="prev"><?= GetMessage('IBLOCK_FORM_PREV')?></button>
								<button class="account-content__btn js-toggle-tab-btn" data-pos="<?= $name == $lastTab ? 'send' : 'next'?>"><?= $name == $lastTab ? GetMessage('IBLOCK_FORM_SUBMIT') : GetMessage('IBLOCK_FORM_NEXT')?></button>
							</div>
						<? endif;?>
					</div>
					<? $firstTab = false;?>
				<? endforeach; ?>

			</form>
		</section>
	</section>
<? endif; ?>