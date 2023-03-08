<?
/**
 * @var $arItem
 */
use Citrus\Arealty\Entity\Metro;
use Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Web\Json;
?>
<div style="visibility: hidden; position: absolute;" class="hidden-svg-icons">
	<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
	     width="10px" height="10px" viewBox="0 0 357 357">
		<g id="svg-icon--close">
			<polygon points="357,35.7 321.3,0 178.5,142.8 35.7,0 0,35.7 142.8,178.5 0,321.3 35.7,357 178.5,214.2 321.3,357 357,321.3
				214.2,178.5"/>
		</g>
	</svg>
</div>

<div class="citrus-sf-label" id="filter-label-<?=$arItem['ID']?>">
	<span class="citrus-sf-label_name"><?=$arItem["NAME"]?></span><?=$arItem['HINT'] ? ', '.$arItem['HINT'] : ''?>
	<span class="citrus-sf-label_value"></span>
	<span class="citrus-sf-label_close"><i class="icon-close" aria-hidden="true"></i></span>
</div>


<div style="display: none;">
	<div class="sf-location" id="filter-values-<?=$arItem['ID']?>" >
		<header class="sf-location__header">
			<div class="sf-location__title"><?= Loc::getMessage("METRO_TITLE") ?></div>
		</header>
		<div class="sf-location__tab-content">
		<div class="sf-location__tab-it _active"
		     data-property-code="<?=$arItem['CODE']?>"
		     data-name="<?=$arItem['NAME']?>">
			<div class="metro">
				<div class="metro__map" id="metro-map-container"><?=Metro\CityTable::getSvg('moscow');?></div>
				<div id="metro-item-<?=$arItem['ID']?>"></div>

				<script type="text/x-template" id="metro-template">
					<div class="metro__nav">
						<div class="metro__sf-checkbox" style="display: none;">
							<input
								type="checkbox"
								v-for="cb in checkbox"
								:name="cb['CONTROL_NAME']"
								:id="cb['CONTROL_ID']"
								:value="cb['HTML_VALUE']"
								ref="checkbox"
								:checked="inResultStationName(cb['VALUE'])"
								:data-name="cb['VALUE']"
							>
						</div>

						<div class="metro__nav-top">
							<div class="metro__search">
								<input type="text"
								       name=""
								       placeholder="<?=Loc::getMessage("METRO_SEARCH_PLACEOLDER")?>"
								       class="metro__search-input"
								       @focus="openPopup('metro-search-result')"
								       @blur="/*hidePopup('metro-search-result')*/"
								       @keydown.prevent.down="highlightSearchResult(+1)"
								       @keydown.prevent.up="highlightSearchResult(-1)"
								       @keyup.enter="clickHighlightResultItem()"
								       ref="searchInput"
								       v-model="searchText">
								<i class="metro__search-icon fa fa-search"></i>
								<div class="popup"
								     ref="searchResult"
								     :class="{'_active': (popup['metro-search-result'] && searchText.length) }">
									<div class="popup__content" v-if="searchText.length">
										<div v-if="!searchStations.length" class="metro__search-no-result"><?= Loc::getMessage("METRO_EMPTY_SEARCH_RESULT") ?></div>
										<div v-if="searchStations.length"
										     class="metro__search-result">
											<div class="metro__search-result__item"
											     :title="!station.isActive ? $root.lang.NO_OBJECT_STATION : ''"
											     :class="{
											        '_highlight': stationKey === searchHighlightIndex || activeSearchStations.length === 1,
											        '_active': station.isActive
											     }"
											     v-for="(station, stationKey) in searchStations"
											     @click="onClickSearchResult(station, $event)">{{station.NAME}}</div>
										</div>
									</div>
								</div>
							</div>
							<div class="metro__lines">
								<div ref="popupLinesLabel"
								     class="metro__lines__label"
								     :class="{'_active': popup['metro-lines']}"
								     @click="togglePopup('metro-lines')">
									<div class="metro__lines__label-name"><?= Loc::getMessage("METRO_SELECT_LINE") ?></div>
								</div>
								<div ref="popupLines" class="popup" :class="{'_active': popup['metro-lines']}">
									<div class="popup__content">
										<div class="metro__line-list">
											<div v-for="(line, lineKey) in activeLines"
											     class="metro-line"
											     :class="{'_active': inResult(line), '_disable': line.disable}"
											     @click="toggleResult(line)" >
												<div class="metro-line__label">
													<template v-if="line.SVG_ID === 'inring'">
														<span class="metro-line__icon metro-line__icon_type_in-ring"></span>
													</template>
													<template v-else>
												             <span class="metro-line__icon metro-line__icon_type_line"
												                   :style="{'background-color': line.COLOR}"></span>
													</template>

												</div>
												<div class="metro-line__name">{{ line.NAME }}</div>
											</div>
										</div><!-- .metro__list -->
									</div><!-- .popup__content -->
								</div><!-- .popup -->
							</div><!-- .metro__lines -->
						</div><!-- .metro__nav-top -->

						<div class="metro__result">
							<div class="metro__result-item" v-for="resultItem in formatResult">
								<div class="metro__result-item__label">
									<template v-if="resultItem.SVG_ID === 'inring'">
										<span class="metro-line__icon metro-line__icon_type_in-ring"></span>
									</template>
									<template v-else-if="resultItem.isStation">
							            <span
								            v-for="resultItemLine in getLinesById(resultItem.LINE)"
								            v-if="resultItemLine.IS_GROUP !== 'Y'"
								            class="metro-item__icon metro-item__icon_type_station"
								            :style="{'background-color': resultItemLine.COLOR}"
							            ></span>
									</template>
									<template v-else>
								            <span
									            class="metro-line__icon metro-line__icon_type_line"
									            :style="{'background-color': resultItem.COLOR}"></span>
									</template>
								</div>
								<div class="metro__result-item__name"
								     :title="resultItem.NAME">
									{{resultItem.NAME}}
								</div>
								<div class="metro__result-item__remove"
								     @click="removeFromResult(resultItem)"><svg class="svg-icon" viewBox="0 0 357 357"><use xlink:href="#svg-icon--close"/></svg></div>
							</div>
						</div>

						<div class="metro__btns">
							<a href="javascript:void(0);"
							   @click="saveResult()"
							   class="metro__btn btn btn-primary"><?= Loc::getMessage("METRO_CHOSE") ?></a>
							<a href="javascript:void(0);"
							   @click="resetResult()"
							   class="metro__btn metro__clear-link"><?= Loc::getMessage("METRO_RESET") ?></a>
						</div>


					</div><!-- .metro__nav -->
				</script><!-- vue template -->
			</div><!-- .metro -->
			<script>
				new Vue({
					el: '#metro-item-<?=$arItem['ID']?>',
					data: <?=\Bitrix\Main\Web\Json::encode(array(
						'stations' => Metro\StationTable::getItems($arResult['METRO_CITY']),
						'lines' => Metro\LineTable::getItems($arResult['METRO_CITY']),
						'checkbox' => array_values($arItem['VALUES']),
						'lang' => [
							'AUTO_REPLACE_EN_RU' => Loc::getMessage("AUTO_REPLACE_EN_RU"),
							'NO_OBJECT_STATION' => Loc::getMessage("METRO_NO_OBJECT_STATION")
						]
					))?>,
					template: '<metro :stations="stations" :lines="lines" :lang="lang" :checkbox="checkbox" />',
				});
			</script>
		</div><!-- .sf-location__tab-it -->
	</div><!-- .sf-location__tab-content -->
	</div><!-- .sf-location -->
</div>

<?CJSCore::Init(array('magnificPopup', 'vue'));?>

<script>
	;(function () {
		new SMARTFILTER_LOCATION("<?=$arItem['ID']?>");
	})();
</script>