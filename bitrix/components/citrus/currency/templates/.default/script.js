
;(function () {
	/**
	 *
	 * @param {Object} params - настройки валют:
	 * @param {Object} params.rates - курсы валют
	 * @param {String} params.current - код текущей валюты
	 * @param {String} params.base - код базовой валюты
	 * @constructor
	 */
	window.Currency = function (params) {
		"use strict";

		// vars
		{
			var self = this;
			self.base = params.base;
			self.current = params.base;
			self.defaultFormatOptions = {
				mark: '.',
				decimals: 0,
				thousand: ' ',
				/*encoder: function ( value ) {
					// delete 0 after point
					if (this.options.decimals > 0 && this.options.mark.length) {
						value = '' + value;
						var arSplitValue = value.split(this.options.mark);
						if (!arSplitValue[1]) value = arSplitValue[0];
					}
					return +value;
				},*/
				/*decoder: function (value) { return +value; }*/
			};
			self.moneyFormat = wNumb(self.defaultFormatOptions);
			self.items = params.items;
			self.factors = params.factors;
		}

		// events
		{
			self.events = {
				'update' : {},
			};
			self.on = function (eventName, eventFunction) {
				if (!self.events[eventName] || !self.events[eventName]['fn'])
					self.events[eventName] = {
						triggered: false,
						fn: []
					};
				self.events[eventName]['fn'].push(eventFunction);
			};
			self.trigger = function (eventName, arg1, arg2) {
				if (!self.events[eventName] || !self.events[eventName]['fn'] ||  !self.events[eventName]['fn'].length) return;

				self.events[eventName]['triggered'] = true;
				self.events[eventName]['fn'].forEach(function (fn) {
					fn.call(self, arg1, arg2);
				});
			};
		}

		// methods
		{
			/**
			 * Очищает от всего кроме чисел
			 * @param {number|string} val
			 * @param {object} formatOptions - настрйка формата числа
			 * @return {number|string} возвращает пустую строку или число
			 */
			self.clearValue = function (val, formatOptions) {
				if (typeof val !== 'number' && typeof val !== 'string') return '';

				val = ''+val;

				var mark = typeof formatOptions !== 'undefined' && formatOptions.decimals > 0 ? formatOptions.mark : '';
				if (mark) {
					var splitVal = val.split(mark);
					splitVal.map(function (splitValPart) {
						return splitValPart.replace(/[^\d\.]/g, '');
					});
					return splitVal.join(mark);
				}

				// clear non digit
				val = val.replace(/\D/g, '');
				if (val !== '') val = +val;
				return val;
			};
			/**
			 * Очищает и форматирует в формат цены
			 */
			self.toFormatWithClear = function (val, formatOptions) {
				formatOptions = formatOptions || {};
				formatOptions = $.extend(self.defaultFormatOptions, formatOptions);

				val = self.clearValue(val, formatOptions);
				var format = wNumb(formatOptions);

				return val === '' || val === '0' ? val : format.to(+val);
			};
			self.fromFormat = function (val, formatOptions) {
				var format = self.moneyFormat;
				if (typeof formatOptions === 'object') {
					format = wNumb($.extend(self.defaultFormatOptions, formatOptions));
				}

				return val === '' ? val : format.from(''+val);
			};

			self.setRates = function (newRates) {
				var currencyCode;
				for (currencyCode in newRates) {
					if (newRates.hasOwnProperty(currencyCode))
						self.rates[currencyCode] = newRates[currencyCode];
				}
				return self.rates;
			};
			self.getRate = function(currencyCode){
				if (!self.items[currencyCode]['RATE']) console.warn('Empty rate for: '+currencyCode);
				return +self.items[currencyCode]['RATE'];
			};
			self.getCurrentRate = function() {
				return self.getRate(self.current);
			};

			/**
			 * Устанавливает валюту
			 * @param {string} currencyCode - код валюты, ex: USD. Курс валют должен быть определен заранее.
			 * @param {boolean} [needUpdateHtml=false] - нужно ли обновлять валюты на странице после установки курса
			 * @param {boolean} [updateStorage=false] - нужно ли обновлять переменную в localStorage
			 * @return {string}
			 */
			self.setCurrent = function(currencyCode, needUpdateHtml, updateStorage) {
				if (!self.getRate(currencyCode)) console.warn('Empty currency rate for: '+currencyCode);

				if (self.current !== currencyCode) {
					self.current = currencyCode;
					if (needUpdateHtml) {
						self.updateHtml();
						self.updateSelect();
					}
					if (updateStorage === true) {
						self.saveToStorage();
					}

					self.trigger('update');
				}
				return self.current;
			};
			self.setBase = function (currencyCode) {
				self.base = currencyCode;
			};

			self.convertBaseToCurrent = function (val) {
				return val / self.getCurrentRate();
			};
			self.convertToBase = function (val, currencyName) {
				var currencyName = currencyName || self.current;
				return val * self.getRate(currencyName);
			};
			self.convertCurrentToBase = function(val) {
				return self.convertToBase(self.current, val);
			};

			self.saveToStorage = function () {
				localStorage.setItem('currency', self.current);
			};
			self.getFromStorage = function () {
				return localStorage.getItem('currency');
			};
			self.setFromStorage = function (needUpdateHtml) {
				var storageCur = self.getFromStorage();
				if (typeof storageCur != 'string' || !self.items[storageCur]) storageCur = self.base;

				if (storageCur !== self.current)
					self.setCurrent(storageCur, needUpdateHtml);

				return storageCur;
			};
			
			self.getFactor = function (currencyCode) {
				if (typeof currencyCode === 'undefined') currencyCode = self.current;
				return self.items[currencyCode]['FACTOR'];
			};
			self.getFactorValue = function(currencyCode){
				if (typeof currencyCode === 'undefined') currencyCode = self.current;
				return self.getFactor(currencyCode) ? +self.getFactor(currencyCode)['VALUE'] : 1
			};
			self.convertToCurrencyFactor = function (val, currency) {
				var currency = currency || self.current;
				return val / self.getFactorValue(currency);
			};
			self.convertFromCurrencyFactor = function (val, currency) {
				var currency = currency || self.current;
				return val * self.getFactorValue(currency);
			};

			self.convertToCurrentWithFactor = function (val, currency) {
				var currency = currency || self.current;
				return self.convertBaseToCurrent(self.convertToCurrencyFactor(val, currency));
			};
			self.convertFromCurrencyWithFactor = function (val, currency) {
				var currency = currency || self.current;
				return self.convertToBase(self.convertFromCurrencyFactor(val, currency), currency)
			};
		}

		// html
		{
			self.updateHtmlCurrency = function($items){
				var $items = $items || $('[data-currency-base], [data-currency-icon]');

				$items.each(function () {
					if ($(this).data('currency') === self.current || $(this).data('currency-fixed')) return;

					var baseValue = +$(this).data('currency-base');
					if (baseValue)
						$(this).html(self.moneyFormat.to(self.convertBaseToCurrent(baseValue)));

					if ($(this).data('currency-icon') !== 'undefined'){
						$(this).attr('data-icon-position', self.items[self.current]['SHOW_AFTER'] !== 'N' ? 'after':'before');
						$(this).attr('data-currency-icon', self.items[self.current]['SIGN']);
					}

					$(this).attr('data-currency', self.current);
					$(this).data('currency', self.current);
				});
			};

			self.updateCurrencyFactorName = function($items){
				var $items = $items || $('[data-currency-factor]');
				var factorName = self.getFactor()['TEXT'];
				$items.html(factorName ? ', '+factorName : '');
			};

			/**
			 * Пересчитывает валюты и меняет иконки валют в текущем блоке.
			 * Если блок не задан то меняет валюты на всей странице.
			 * @param {Object} [$block] - jquery object
			 */
			self.updateHtml = function ($block) {
				var $block = $block || $('body');
				self.updateHtmlCurrency($block.find('[data-currency-base], [data-currency-icon]'));
				self.updateCurrencyFactorName();
			};
		}

		// currency select
		{
			self.$currencyDropdown = $('.header-currency-dropdown');
			self.$currencyDropdownOpenBth = self.$currencyDropdown.find('.js-open-dropdown');

			self.closeDropdown = function() {
				self.$currencyDropdown.removeClass('_open');
			};

			self.$currencyDropdown.find('.js-citrus-arealty-currency').on('click', function () {
				if ($(this).hasClass('_active')) return;
				var newCurrency = $(this).find('[data-currency]').data('currency');
				self.setCurrent(newCurrency, true, true);
				self.closeDropdown();
			});

			self.$currencyDropdownOpenBth.on('click', function () {
				self.$currencyDropdown.toggleClass('_open');
			});

			self.updateSelect = function(){
				self.updateHtmlCurrency(self.$currencyDropdown.find('[data-currency-icon]'));

				var $currentListItem = self.$currencyDropdown.find('[data-currency="'+self.current+'"]');
				var $currentListItemBlock = $currentListItem.closest('.js-citrus-arealty-currency');
				if (!$currentListItemBlock.hasClass('_active')) {
					$currentListItemBlock.addClass('_active').siblings().removeClass('_active');
				}
			};


			cui.clickOff(self.$currencyDropdown, self.closeDropdown);
		}

		// init
		{
			self.setFromStorage();
			self.updateSelect();
			
			window.addEventListener('storage', function(events) {
				if (events.key === "currency") self.setFromStorage(true);
			});
		}
	};
}());