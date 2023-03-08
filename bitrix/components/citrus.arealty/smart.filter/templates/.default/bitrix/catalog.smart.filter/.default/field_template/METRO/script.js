
;(function(){
	"use strict";
	
	/**
	 * @type object
	 * @property {array} c.stations - Станции метро
	 * @property {array} c.lines - Линии метро
	 * @property {object} c.popup - Всплывающие списки в поиске или селекте
	 * @property {string} c.searchText - Строка поиска
	 * @property {array} c.result - Результат выбора метро
	 */
	var c;
	Vue.component('metro', {
		template: "#metro-template",
		props: ['stations', 'lines', 'lang', 'checkbox'],
		data: function () {
			return {
				popup: {
					'metro-lines': false,
					'metro-search-result': false
				},
				searchText: '',
				maxSearchResult: 10,
				searchHighlightIndex: -1,
				selectedStations: [],
				result: [],
				backupResult: [],
				activeTippy: {}
			}
		},
		created: function(){c = this;},
		computed: {
			searchStations: function () {
				if (!c.searchText.length) return [];
				var searchStations = c.stations.filter(function (station) {
					var stationName = station.NAME.toLowerCase();
					return stationName.indexOf(c.searchText.toLowerCase()) > -1 ||
						stationName.indexOf(c.autoReplaceKeyboard(c.searchText.toLowerCase())) > -1
					
				});
				if (searchStations.length > c.maxSearchResult)
					searchStations.splice(c.maxSearchResult-1, searchStations.length-c.maxSearchResult);
				
				return searchStations
			},
			activeSearchStations: function () {
				return c.searchStations.filter(function (station) {
					return station.isActive;
				});
			},
			// если все станции ветки выбраны то выбираем ветку
			// и удаляем из результата все станции ветки
			formatResult: function () {
				var checkedLineIds = [];
				var result = [];
				var excludeStations = [];
				
				c.result.forEach(function (station, resultKey) {
					
					station.LINE.forEach(function (lineId) {
						if (checkedLineIds.indexOf(lineId) > -1) return;
						
						checkedLineIds.push(lineId);
						
						var lineStation = c.getStationsByLineId(lineId);
						var resultLineStation = c.result.filter(function (station1) {
							return station1.LINE.indexOf(lineId) > -1 && station1.isActive;
						});
						if (lineStation.length === resultLineStation.length && lineStation.length >= 1) {
							result = result.concat(c.getLinesById(lineId));
							excludeStations = excludeStations.concat(lineStation)
						}
					});
					if (excludeStations.indexOf(station) === -1) result.push(station);
				});
				return result;
			},
			activeStations: function () {
				return c.stations.filter(function(stantion){
					return (c.checkbox.filter(function (cb) {
						return cb.VALUE === stantion.NAME;
					}).length > 0);
				});
			},
			activeLines: function () {
				return c.lines.map(function(line){
					line.disable = (c.getStationsByLineId(line.ID).length < 1);
					return line;
				});
			},
		},
		methods: {
			/**
			 * Добавляет активные станции на карту
			 * @param {object} item - станция или ветка
			 */
			addToSvg: function(item) {
				item = [].concat(item);
				item.forEach(function (oneItem) {
					if (oneItem.isStation) {
						$(oneItem.mapGroup).addClass('_active');
					} else {
						c.addToSvg(c.getStationsByLineId(oneItem.ID));
					}
				});
			},
			removeFromSvg: function(item) {
				item = [].concat(item);
				item.forEach(function (oneItem) {
					if (oneItem.isStation) {
						$(oneItem.mapGroup).removeClass('_active');
					} else {
						c.removeFromSvg(c.getStationsByLineId(oneItem.ID));
					}
				});
			},
			
			removeFromResult: function(arItems) {
				arItems = [].concat(arItems);
				arItems.forEach(function (oneItem) {
					if (oneItem.isStation) {
						c.removeFromSvg(oneItem);
						c.$delete(c.result, c.result.indexOf(oneItem));
					} else {
						var lineStations = c.getStationsByLineId(oneItem.ID);
						// удаляются только станции которых нет в других выбранных ветках
						// (для станций в нескольких группах)
						// но станций должно быть больше 1
						lineStations = lineStations.filter(function (station) {
							var result = c.formatResult.filter(function(resultLine) {
								return !resultLine.isStation &&
									resultLine.ID !== oneItem.ID &&
									(station.LINE.indexOf(resultLine.ID) > -1);
							});
							return result.length <= 1;
						});
						c.removeFromResult(lineStations);
					}
				});
			},
			addToResult: function (arItems) {
				arItems = [].concat(arItems);
				arItems.forEach(function (oneItem) {
					if (c.inResult(oneItem)) return;
					if (oneItem.isStation) {
						c.result.unshift(oneItem);
						c.addToSvg(oneItem);
					} else {
						if (!oneItem.disable) c.addToResult(c.getStationsByLineId(oneItem.ID));
					}
				});
			},
			inResult: function (item){
				return c.result.indexOf(item) > -1 || c.formatResult.indexOf(item) > -1;
			},
			/**
			 * добавляет в результат
			 * @param {object} item - станция или ветка
			 */
			toggleResult: function (item) {
				c[c.inResult(item) ? 'removeFromResult' : 'addToResult'](item);
			},
			
			/* выбор станции из поиска */
			onClickSearchResult: function (station, event) {
				if (!station.isActive) {
					if (c.activeTippy.destroyAll) {
						c.activeTippy.destroyAll();
						c.activeTippy = {};
					}
					
					var o = event.target;
					station.tippy = c.activeTippy = tippy(o, {
						placement: 'bottom-start',
						performance: true,
						trigger: 'custom',
						zIndex: 9999,
					});
					setTimeout(function () {
						o._tippy.show();
					}, 0);
					setTimeout(function () {
						station.tippy.destroyAll();
					}, 1000);
					return;
				}
				
				c.searchText = '';
				c.searchHighlightIndex = -1;
				
				if (typeof station !== 'undefined') c.addToResult(station);
			},
			highlightSearchResult: function(index) {
				var resultLength = c.activeSearchStations.length;
				
				if (!resultLength) return;
				
				var startSearchIndex = c.searchHighlightIndex;
				c.searchHighlightIndex += index;
				
				if (index < 0 && startSearchIndex === -1) {
					c.searchHighlightIndex = resultLength - 1;
				}
				
				if (c.searchHighlightIndex > resultLength) c.searchHighlightIndex = -1;
				if (c.searchHighlightIndex < -1) c.searchHighlightIndex = -1;
			},
			clickHighlightResultItem: function() {
				c.onClickSearchResult(c.activeSearchStations.length === 1 ?
					c.activeSearchStations[0] : c.activeSearchStations[c.searchHighlightIndex]);
			},
			
			getLinesById: function (arId) {
				arId = [].concat(arId);
				return c.lines.filter(function (line) {
					return arId.indexOf(line.ID) > -1;
				});
			},
			getStationsByLineId: function (lineId) {
				return c.activeStations.filter(function (station) {
					return station.LINE.indexOf(lineId) > -1;
				});
			},
			
			togglePopup: function (popupName) {
				c.popup[popupName] = !c.popup[popupName];
			},
			openPopup: function (popupName) {
				c.popup[popupName] = true;
			},
			hidePopup: function(popupName) {
				c.popup[popupName] = false;
			},

			saveBackUpResult: function(){
				c.backupResult = c.result.reduce(function (ar, resultItem) {
					ar.push(resultItem);
					return ar;
				}, []);
			},
			resetResult: function(){
				c.removeFromResult(c.result);
				c.addToResult(c.backupResult);
			},
			saveResult: function () {
				c.saveBackUpResult();
				$.magnificPopup.close();
				smartFilter.updateDuplicate();
				smartFilter.reload();
			},
			
			inResultStationName: function(stationName){
				return (c.result.filter(function(resultItem){
					return resultItem.NAME === stationName;
				}).length > 0);
			},
			
			autoReplaceKeyboard: function( str ) {
				var replacer = c.lang['AUTO_REPLACE_EN_RU'];
				
				return str.replace(/[A-z/,.;\'\]\[]/g, function ( x ){
					return x == x.toLowerCase() ? replacer[ x ] : replacer[ x.toLowerCase() ].toUpperCase();
				});
			},
			getStationsByName: function (name) {
				return c.stations.filter(function(station){
					return station.NAME === name;
				});
			}
		},
		mounted: function(){
			cui.clickOff($(c.$refs.popupLinesLabel).add($(c.$refs.popupLines)), function () {
				c.hidePopup('metro-lines');
			});
			
			cui.clickOff($(c.$refs.searchInput).add($(c.$refs.searchResult)), function () {
				c.hidePopup('metro-search-result');
			});
			
			c.$map = $('#metro-map-container');
			
			// привязка станций к точкам из справочника
			c.stations.map(function (stantion) {
				var id = stantion.SVG_ID;
				
				stantion.isStation = true;
				
				stantion.isActive = (c.checkbox.filter(function (cb) {
					return cb.VALUE === stantion.NAME;
				}).length > 0);

				var $mapGroup = $(c.$map).find('#'+id)
					.addClass('metro-map-group')
					.on('click', function(event) {
						if(stantion.isActive) c.toggleResult(stantion);
					});
				
				stantion.mapGroup = $mapGroup.get(0);
				
				if (stantion.isActive)
					$mapGroup.addClass('_clickable');
				$mapGroup.find('text').addClass('metro-map-name');

				var grouppedStations = $mapGroup.find('g');

				var $stationGroup = grouppedStations.length ?
                    	grouppedStations : $(stantion.mapGroup);
				
				$stationGroup.each(function () {
					$(this).find('circle,ellipse').last().addClass('metro-map-point');
				});
				
				return stantion;
			});
			
			// сортируем станции по активности
			c.stations.sort(function (a, b) {
				return b.isActive - a.isActive;
			});
			
			c.checkbox.forEach(function (cb) {
				if (cb.CHECKED) {
					c.addToResult(c.getStationsByName(cb.VALUE));
				}
			});
			
			$(this.$refs.checkbox).on('change', function () {
				c[$(this).prop('checked') ? 'addToResult' : 'removeFromResult'](c.getStationsByName($(this).data('name')));
			});

			c.saveBackUpResult();
		},
	});
	
	window.SMARTFILTER_LOCATION = function (id) {
		var self = this;
		
		var $label = $('#filter-label-'+id);
		var $values = $('#filter-values-'+id);
		var districtGroupSliderSelector = '#district-group-slider-'+id;
		
		//popup
		$label.on('click', function(event) {
			event.preventDefault();
			$.magnificPopup.open({
				items: {
					src: '#filter-values-'+id
				},
				type: 'inline',
				midClick: true,
				callbacks: {
					'open': function () {
					
					},
					'close': function () {
						c.resetResult();
					},
				}
			});
		});
		
		
	};
}());