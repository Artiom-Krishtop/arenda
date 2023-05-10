var citrusSmartFilterNumbers = function (data) {
	var self = this,
		arItem = data,
	    $container = $('.filter-numbers[data-property-id="'+arItem['ID']+'"]'),
		isRangeSlider = arItem['DISPLAY_TYPE'] == 'A',
		$rangeSliderInput = $container.find('.range-slider-input'),
		$minInput = $container.find('.filter-numbers_input._min'),
		$maxInput = $container.find('.filter-numbers_input._max'),
		$numberInputs = $minInput.add($maxInput),
		isCostField = arItem['CODE'] === 'cost',
		sliderStep = isCostField && currency.getFactorData() ? +currency.getFactorData()['STEP'] : 1;
	
	self.min = +arItem["VALUES"]["MIN"]["VALUE"];
	self.max = +arItem["VALUES"]["MAX"]["VALUE"];
	self.minValue = +( arItem["VALUES"]["MIN"]["HTML_VALUE"] ? arItem["VALUES"]["MIN"]["HTML_VALUE"]: arItem["VALUES"]["MIN"]["VALUE"]);
	self.maxValue = +( arItem["VALUES"]["MAX"]["HTML_VALUE"] ? arItem["VALUES"]["MAX"]["HTML_VALUE"]: arItem["VALUES"]["MAX"]["VALUE"]);
	
	self.currency = '';
	self.factorValue = '';
	
	$numberInputs.on('clear', function () {
		$numberInputs.data('real-value', '');
	});

	// format
	{
		// wNumb options. Doc https://refreshless.com/wnumb/
		var formatOptions = {
			decimals: sliderStep < 1 ? 1 : 0,
		};
		var self = this;
		self.fromFormat = function (val) {
			return currency.fromFormat(val, formatOptions);
		};
		self.toFormat = function (val) {
			return currency.toFormatWithClear(val, formatOptions);
		};
		
		//doc https://github.com/autoNumeric/autoNumeric
		$numberInputs.autoNumeric('init', {
			digitGroupSeparator: ' ',
			digitalGroupSpacing: '3',
			decimalPlacesOverride: sliderStep < 1 ? 1 : 0,
			minimumValue: 0,
			showWarnings: false,
			allowDecimalPadding: false,
		});
	}
	
	self.updateInputRealValue = function () {
		var realMinValue = self.minValue,
			realMaxValue = self.maxValue;

		if (isCostField) {
			realMinValue = currency.convertFromCurrencyWithFactor(realMinValue, self.currency, self.factorValue);
			realMaxValue = currency.convertFromCurrencyWithFactor(realMaxValue, self.currency, self.factorValue);
		}

		if (realMinValue) {
			$minInput.data('real-value', realMinValue === self.min ? '' : realMinValue);
		}

		if (realMaxValue) {
			$maxInput.data('real-value', realMaxValue === self.max ? '' : realMaxValue);
		}
	};
	
	self.updateInputs = function() {
		if (arItem['CODE'] === 'cost' && typeof currency !== 'undefined') {
			
			if (self.currency !== currency.current || self.factorValue !== currency.getFactorValue()) {
				
				if (self.currency.length) {
					self.min = currency.convertFromCurrencyWithFactor(self.min, self.currency, self.factorValue);
					self.max = currency.convertFromCurrencyWithFactor(self.max, self.currency, self.factorValue);
					self.minValue = currency.convertFromCurrencyWithFactor(self.minValue, self.currency, self.factorValue);
					self.maxValue = currency.convertFromCurrencyWithFactor(self.maxValue, self.currency, self.factorValue);
				}
				
				self.min = Math.floor(currency.convertToCurrentWithFactor(self.min));
				self.max = Math.ceil(currency.convertToCurrentWithFactor(self.max));
				self.minValue = self.minValue ? Math.floor(currency.convertToCurrentWithFactor(self.minValue)) : '';
				self.maxValue = self.maxValue ? Math.ceil(currency.convertToCurrentWithFactor(self.maxValue)) : '';
				self.currency = currency.current;
				self.factorValue = currency.getFactorValue();
			}
		}
		self.updateInputRealValue();

        if (arItem['CODE'] === 'cost' && typeof currency !== 'undefined') {
            $minInput.data('default-value', self.toFormat(self.min));
            $maxInput.data('default-value', self.toFormat(self.max));
            $minInput.attr('placeholder', arItem.lang.from + ' ' + self.toFormat(self.min));
            $maxInput.attr('placeholder', arItem.lang.to + ' ' + self.toFormat(self.max));
        } else {
            $minInput.data('default-value', self.min);
            $maxInput.data('default-value', self.max);
            $minInput.attr('placeholder', arItem.lang.from + ' ' + self.min);
            $maxInput.attr('placeholder', arItem.lang.to + ' ' + self.max);
        }

		if (self.minValue && self.minValue !== self.min) {
			$minInput.autoNumeric('set', self.minValue);
		} else {
			$minInput.val('');
		}
		if ( self.maxValue && self.maxValue !== self.max ) {
			$maxInput.autoNumeric('set', self.maxValue);
		} else {
			$maxInput.val('');
		}
	};
	
	self.updateSlider = function() {
		if (!isRangeSlider) return;
		
		if (!self.slider) {
			//doc http://ionden.com/a/plugins/ion.rangeslider/demo_advanced.html
			$rangeSliderInput.ionRangeSlider({
				type: "double",
				min: self.min,
				max: self.max,
				from: self.minValue,
				to: self.maxValue,
				values_separator: ' &ndash; ',
				step: sliderStep,
				onChange: function (data) {
					
					self.minValue = data.from;
					self.maxValue = data.to;
					
					self.updateInputs();
					smartFilter.keyup($minInput.get(0));
				}
			});
			self.slider = $rangeSliderInput.data('ionRangeSlider');
			$numberInputs.on('clear', function () {
				self.slider.update({
					from: self.min,
					to: self.max
				});
			});
		} else {
			self.slider.update({
				from: self.minValue,
				to: self.maxValue,
				min: self.min,
				max: self.max,
			});
		}
	};
	
	self.update = function(){
		self.updateInputs();
		if (isRangeSlider) self.updateSlider();
	};
	
	// init
	{
		self.update();
		
		if (typeof currency !== 'undefined') {
			currency.on('update', function () {
				self.update();
				smartFilter.keyup($minInput.get(0));
			});
		}
		

		$minInput.on('input', function(event) {
		    event.preventDefault();
			
			if ($(this).val() === '') {
				self.minValue = self.min;
				$(this).data('real-value', '');
			} else {
				self.minValue = +$minInput.autoNumeric('get');

				if (self.maxValue && self.minValue > self.maxValue) self.minValue = self.maxValue;
				if (self.minValue > self.max) self.minValue = self.max;

				$(this).autoNumeric('set', self.minValue);
				$minInput.data('real-value', self.minValue);
			}

			self.updateSlider();
			smartFilter.keyup(this);
		});

		$maxInput
			.on('input', function(event) {
				event.preventDefault();

				if ($(this).val() === '') {
					self.maxValue = self.max;
					$(this).data('real-value', '');
				} else {
					self.minValue = +$minInput.autoNumeric('get');
					self.maxValue = +$maxInput.autoNumeric('get');

					if (self.max && self.maxValue > self.max) self.maxValue = self.max;

					$(this).autoNumeric('set', self.maxValue);
					$(this).data('real-value', self.maxValue);
				}

				self.updateSlider();
				smartFilter.keyup(this);
			})
			.on('change', function(event) {
				if ($(this).val() !== '') {
					var inputMaxValue = +$(this).autoNumeric('get');
					var needUpdate = false;
					if (self.minValue && inputMaxValue < self.minValue) {
						self.maxValue = self.minValue;
						$(this).autoNumeric('set', self.maxValue);
						$(this).data('real-value', self.maxValue);
						needUpdate = true;
					}
					if (inputMaxValue < self.min) {
						self.maxValue = self.min;
						$(this).autoNumeric('set', self.maxValue);
						$(this).data('real-value', self.maxValue);
						needUpdate = true;
					}
					if ($minInput.val() !== '' && inputMaxValue && self.minValue > inputMaxValue) {
						self.minValue = self.maxValue;
						$minInput.autoNumeric('set', self.maxValue)
								.data('real-value', self.maxValue);
						needUpdate = true;
					}
					self.updateSlider();
					smartFilter.keyup(this);
				}
			});
	}
	
};
