/**
 * @var {object} settingsWidget - vuejs object
 */

BX.addCustomEvent('settings.widget', function(){
	window.templateSettings = new function () {
		var self = this;
		self.$items = {};
		
		/**
		 * Обработка изменений виджета настроек
		 * @param field - поле которое изменилось
		 * @param item - элемент который изменился (ex. поле типа 'blocks')
		 */
		self.onChangeField = function (field, item) {
			//console.log(field);
			
			var $items = self.$items[field.code] || $([]);
			var isItems = typeof $items !== 'undefined';
			var value = field.value;
			
			/**
			 * Фильтрует элементы по data-settings-rel='rel'
			 * Если rel не указан, возвращает только элементы без data-settings-rel
			 * @param [rel]
			 * @return {Object}
			 */
			var $getFilterItems = function (rel) {
				return $items.filter(function () {
					return $( this ).data( 'settings-rel' ) === rel;
				});
			};
			
			/**
			 * Скрывает или показывает родительский блок для поля
			 * Ищет по коду data-settings-container
			 * @param hide
			 */
			var isContainerChecked = false;
			var toggleSettingsContainer = function () {
				if (isContainerChecked) return;
				
				var hide = false;
				
				switch (field.type) {
					case 'checkbox':
						hide = !field.checked;
						break;
					case 'text':
						hide = value.length === 0;
						break;
					default:
                    	//colorScheme, avatar, text, checkbox, select, blocks
						return;
				}
				
				var $containers = $('[data-settings-container='+field.code+']');
				if ($containers.length) {
					$containers[hide ? 'addClass' : 'removeClass']('hidden');
				}
				isContainerChecked = true;
			};
			
			var $valueItems;
			if (typeof item !== 'undefined') {
				$valueItems = $getFilterItems(item.value);
			}
			
			switch (field.code) {
				case 'SCHEME_LOGO':
					$items.attr('src', value);
					break;
				case 'SCHEME_FAVICON':
					$items.attr('href', value);
					break;
				case 'SCHEME':
					//add css
					if (field.settings.FILES.css) {
						for (var cssFileName in field.settings.FILES.css) {
							var $link = $('[data-scheme-css="' + cssFileName +'"]');
							if (!$link.length) {
								$link = $('<link data-scheme-css="'+ cssFileName +'" rel="stylesheet" href="">');
								$('head').append($link);
							}
							$link.attr('href', field.settings.FILES.css[cssFileName]);
						}
					}
					window.citrusTemplateColor = value;
					window.citrusMapIcon.href = field.settings.FILES.png['map.png'];
					BX.onCustomEvent(field, 'SCHEME');
					break;
				case 'LOGO':
					// set in 'SCHEME_LOGO'
					break;
				case 'SITE_NAME':
					var arText = value.split(' ');
					$getFilterItems('text2').html(arText.pop());
					$getFilterItems('text1').html(arText.join(' '));
					$getFilterItems().html(value);
					break;
				case 'EMAIL':
					$getFilterItems('text').html(value);
					$getFilterItems('link').attr('href', 'mailto:'+value);
					$getFilterItems().attr('href', 'mailto:'+value).html(value);
					break;
				case 'PHONE':
					var clearValue = value.replace(/[^\d\+]/g,"");
					$getFilterItems('text').html(value);
					$getFilterItems('link').attr('href', 'tel:'+clearValue);
					$getFilterItems().attr('href', 'tel:'+clearValue).html(value);
					break;
				case 'LOGO_SHOW_TEXT':
					$items[field.checked ? 'addClass' : 'removeClass']('with_desc');
					break;
				case 'CURRENCY':
					if (typeof currency !== 'undefined')
						currency.setCurrent(value, true, true);
					break;
				case 'CURRENCY_FACTOR':
					if (typeof currency !== 'undefined')
						currency.setCurrentFactor(value);
					break;
				default:
					if (!isItems) return;
					switch (field.type) {
						case 'checkbox':
							$items[field.checked ? 'removeClass' : 'addClass']('hidden');
							break;
						case 'select':
							break;
						case 'blocks':
							if (typeof $valueItems !== 'undefined') {
								if (item.checked) $valueItems.addClass('settings-prepare-visible');
								$valueItems
									[item.checked ? 'slideDown': 'slideUp']('fast', function () {
									$valueItems.trigger('changeVisible', item.checked);
									if (item.checked) {
										$valueItems.removeClass('settings-prepare-visible');
										// после показа нужно вызвать триггер resize для обновления слайдеров
										window.dispatchEvent(new Event('resize'));
									}
								});
							} else {
								field.values.forEach(function (item) {
									self.onChangeField(field, item);
								});
							}
							break;
						default:
							$items.html(value)
								[value.length ? 'removeClass' : 'addClass']('hidden');
							break;
					}
					break;
			}
			toggleSettingsContainer();
		};
		settingsWidget.$on('change', self.onChangeField);
		
		// для неактивных блоков ставим display:none;
		self.checkAvalibleBlock = function(fieldRel){
			if (typeof settingsWidget === 'undefined') return;
			
			var $item = $('[data-settings="BLOCKS"][data-settings-rel="'+fieldRel+'"]');
			if (!$item.length) return;
			
			var settingsField = settingsWidget.getFieldByCode('BLOCKS');
			if (typeof settingsField !== 'undefined' && settingsField.values.length) {
				
				var blocks = settingsField.values;
				var fieldValueForCode = blocks.filter(function (fieldValue) {
					return fieldValue.value === fieldRel;
				});
				
				if (fieldValueForCode.length > 0 && !fieldValueForCode[0].checked)
					$item.hide();
			}
		};
		
		$(function(){
		   $('[data-settings]').each(function (index, item) {
		   	    var arSettingsName = $(this).data('settings').split(';');
			   arSettingsName.forEach(function (settingsName) {
				   if (!self.$items[settingsName]) {
					   self.$items[settingsName] = $(item);
				   } else {
					   self.$items[settingsName] = self.$items[settingsName].add($(item));
				   }
			   });
		   });
		   
			// убираем скрытые блоки
			
		});
	};
});