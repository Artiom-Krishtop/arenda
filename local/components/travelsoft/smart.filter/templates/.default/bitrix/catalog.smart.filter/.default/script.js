function JCSmartFilter(ajaxURL, viewMode, params)
{
	var self = this;
	this.params = params;
	this.ajaxURL = ajaxURL;
	this.form = document.getElementById('smartfilter') || null;
	this.timer = null;
	this.cacheKey = '';
	this.cache = [];
	this.viewMode = viewMode;
	if (params && params.SEF_SET_FILTER_URL)
	{
		this.bindUrlToButton('set_filter', params.SEF_SET_FILTER_URL);
		this.sef = true;
	}
	if (params && params.SEF_DEL_FILTER_URL)
	{
		this.bindUrlToButton('del_filter', params.SEF_DEL_FILTER_URL);
	}

	// init
	(function () {
		cui.clickOff($('.citrus-sf-field'), function ($el) {
			self.toggleValues();
		});
		$(document).on('keyup', function (event) {
			if (event.key === 'Escape') self.toggleValues();
		});
		//update values
		self.updateLabelValues($(self.form).find('.citrus-sf-field'));
	
		if (typeof currency !== 'undefined')
			currency.updateHtml($(self.form));
	
		//update duplicate values
		self.updateDuplicate();

		$(self.form).on('submit', function(event) {
			$(self.form).find('[data-real-value]').each(function (index, item) {
				$(item).val($(item).data('real-value'));
			});
			return true;
		});

		$(document).on('change', '.sf-duplicate__checkbox', function(event) {
		    event.preventDefault();

		    var propertyCode = $(this).data('property-code-for'),
			    arInputId = $(this).data('id').split(';'),
			    $primaryInput = $('#' + arInputId.join(',#')),
		        $primaryBlock = $('[data-property-code="'+propertyCode+'"]'),
		        template = $primaryBlock.data('template'),
		        needUpdate = true;

		    switch (template) {
			    case 'NUMBERS':
				    $primaryInput.val('');
				    $primaryInput.trigger('input');
				    needUpdate = false;
				    break;
			    case 'DROPDOWN':
			    case 'LINE_CHECKBOX':
			    default:
				    $primaryInput.prop('checked', false).trigger('change');
		            break;
		    }
		    if (needUpdate) {
			    self.updateLabelValues($primaryBlock);
		    }
			self.click($primaryInput.get(0));
		});


	}());
}

JCSmartFilter.prototype.keyup = function(input)
{
	var $fieldBlock = $(input).parents('.citrus-sf-field');
	this.updateLabelValues($fieldBlock);

	this.click(input);
};

JCSmartFilter.prototype.clickLabel = function(event, labelNode)
{
	event.preventDefault();

	var $label = $(labelNode),
		$fieldBlock = $label.parents('.citrus-sf-field'),
		$input = $label.find('input');

	if ($label.hasClass('disabled') && !$input.prop('checked')) return;
	$input.prop('checked', !$input.prop('checked'));

	this.updateLabelValues($fieldBlock);
	this.click($input.get(0));
};

JCSmartFilter.prototype.click = function(input)
{
	this.updateDuplicate();
	if(!!this.timer) {
		clearTimeout(this.timer);
	}

	this.timer = setTimeout(BX.delegate(function(){
		this.reload(input);
	}, this), 500);
};

JCSmartFilter.prototype.reload = function(input)
{
	var input = input || $(this.form).find('');

	if (this.cacheKey !== '')
	{
		//Postprone backend query
		if(!!this.timer)
		{
			clearTimeout(this.timer);
		}
		this.timer = setTimeout(BX.delegate(function(){
			this.reload(input);
		}, this), 1000);
		return;
	}
	this.cacheKey = '|';

	this.position = BX.pos(input, true);
	//this.form = BX.findParent(input, {'tag':'form'});

	if (this.form)
	{
		var values = [];
		values[0] = {name: 'ajax', value: 'y'};
		this.gatherInputsValues(values, BX.findChildren(this.form, {'tag': new RegExp('^(input|select)$', 'i')}, true));

		for (var i = 0; i < values.length; i++)
			this.cacheKey += values[i].name + ':' + values[i].value + '|';

		if (this.cache[this.cacheKey])
		{
			this.curFilterinput = input;
			this.postHandler(this.cache[this.cacheKey], true);
		}
		else
		{

			if (this.sef)
			{
				var set_filter = BX('set_filter');
				set_filter.disabled = true;
			}

			this.curFilterinput = input;
			BX.ajax.loadJSON(
				this.ajaxURL,
				this.values2post(values),
				BX.delegate(this.postHandler, this)
			);
		}
	}
};

JCSmartFilter.prototype.updateItem = function (PID, arItem)
{
	if (arItem.PROPERTY_TYPE === 'N' || arItem.PRICE)
	{
		var trackBar = window['trackBar' + PID];
		if (!trackBar && arItem.ENCODED_ID)
			trackBar = window['trackBar' + arItem.ENCODED_ID];

		if (trackBar && arItem.VALUES)
		{
			if (arItem.VALUES.MIN)
			{
				if (arItem.VALUES.MIN.FILTERED_VALUE)
					trackBar.setMinFilteredValue(arItem.VALUES.MIN.FILTERED_VALUE);
				else
					trackBar.setMinFilteredValue(arItem.VALUES.MIN.VALUE);
			}

			if (arItem.VALUES.MAX)
			{
				if (arItem.VALUES.MAX.FILTERED_VALUE)
					trackBar.setMaxFilteredValue(arItem.VALUES.MAX.FILTERED_VALUE);
				else
					trackBar.setMaxFilteredValue(arItem.VALUES.MAX.VALUE);
			}
		}
	}
	else if (arItem.VALUES)
	{
		for (var i in arItem.VALUES)
		{
			if (arItem.VALUES.hasOwnProperty(i))
			{
				var value = arItem.VALUES[i];
				var control = BX(value.CONTROL_ID);

				if (!!control)
				{
					var label = document.querySelector('[data-role="label_'+value.CONTROL_ID+'"]') || control.parentNode;
					$(label)[(value.DISABLED && !control.checked) ? 'addClass': 'removeClass']('no-clicked');
					if (value.DISABLED)
					{
						if (label) BX.addClass(label, 'disabled');
					}
					else
					{
						if (label) BX.removeClass(label, 'disabled');
					}

					if (value.hasOwnProperty('ELEMENT_COUNT'))
					{
						label = document.querySelector('[data-role="count_'+value.CONTROL_ID+'"]');
						if (label)
							label.innerHTML = value.ELEMENT_COUNT;
					}
				}
			}
		}
	}
};

JCSmartFilter.prototype.postHandler = function (result, fromCache)
{
	var hrefFILTER, url;
	var modef_num = BX('modef_num');

	if (!!result && !!result.ITEMS)
	{
		for(var PID in result.ITEMS)
		{
			if (result.ITEMS.hasOwnProperty(PID))
			{
				this.updateItem(PID, result.ITEMS[PID]);
			}
		}

		if (!!modef_num)
		{
			modef_num.innerHTML = result.ELEMENT_COUNT;

			if (result.INSTANT_RELOAD && result.COMPONENT_CONTAINER_ID)
			{
				url = BX.util.htmlspecialcharsback(result.FILTER_AJAX_URL);
				BX.ajax.insertToNode(url, result.COMPONENT_CONTAINER_ID);
			}
			else
			{
				if (result.SEF_SET_FILTER_URL)
				{
					this.bindUrlToButton('set_filter', result.SEF_SET_FILTER_URL);
				}
			}
		}
	}

	if (this.sef)
	{
		var set_filter = BX('set_filter');
		set_filter.disabled = false;
	}

	if (!fromCache && this.cacheKey !== '')
	{
		this.cache[this.cacheKey] = result;
	}
	this.cacheKey = '';
};

JCSmartFilter.prototype.bindUrlToButton = function (buttonId, url)
{
	var button = BX(buttonId);
	if (button)
	{
		var proxy = function(j, func)
		{
			return function()
			{
				return func(j);
			}
		};

		if (button.type == 'submit')
			button.type = 'button';

		BX.bind(button, 'click', proxy(url, function(url)
		{
			window.location.href = url;
			return false;
		}));
	}
};

JCSmartFilter.prototype.gatherInputsValues = function (values, elements)
{
	if(elements)
	{
		for(var i = 0; i < elements.length; i++)
		{
			var el = elements[i];
			if (el.disabled || !el.type || !el.name)
				continue;

			switch(el.type.toLowerCase())
			{
				case 'text':
				case 'textarea':
				case 'password':
				case 'hidden':
				case 'select-one':
					var val = el.value;
					
					if ($(el).data('real-value')) {
						val = $(el).data('real-value');
					}
					
					if(el.value.length)
						values[values.length] = {name : el.name, value : val};
					break;
				case 'radio':
				case 'checkbox':
					if(el.checked)
						values[values.length] = {name : el.name, value : el.value};
					break;
				case 'select-multiple':
					for (var j = 0; j < el.options.length; j++)
					{
						if (el.options[j].selected)
							values[values.length] = {name : el.name, value : el.options[j].value};
					}
					break;
				default:
					break;
			}
		}
	}
};

JCSmartFilter.prototype.values2post = function (values)
{
	var post = [];
	var current = post;
	var i = 0;

	while(i < values.length)
	{
		var p = values[i].name.indexOf('[');
		if(p == -1)
		{
			current[values[i].name] = values[i].value;
			current = post;
			i++;
		}
		else
		{
			var name = values[i].name.substring(0, p);
			var rest = values[i].name.substring(p+1);
			if(!current[name])
				current[name] = [];

			var pp = rest.indexOf(']');
			if(pp == -1)
			{
				//Error - not balanced brackets
				current = post;
				i++;
			}
			else if(pp == 0)
			{
				//No index specified - so take the next integer
				current = current[name];
				values[i].name = '' + current.length;
			}
			else
			{
				//Now index name becomes and name and we go deeper into the array
				current = current[name];
				values[i].name = rest.substring(0, pp) + rest.substring(pp+1);
			}
		}
	}
	return post;
};

JCSmartFilter.prototype.hideFilterProps = function(element)
{
	var obj = element.parentNode,
		filterBlock = obj.querySelector("[data-role='bx_filter_block']"),
		propAngle = obj.querySelector("[data-role='prop_angle']");

	if(BX.hasClass(obj, "bx-active"))
	{
		new BX.easing({
			duration : 300,
			start : { opacity: 1,  height: filterBlock.offsetHeight },
			finish : { opacity: 0, height:0 },
			transition : BX.easing.transitions.quart,
			step : function(state){
				filterBlock.style.opacity = state.opacity;
				filterBlock.style.height = state.height + "px";
			},
			complete : function() {
				filterBlock.setAttribute("style", "");
				BX.removeClass(obj, "bx-active");
			}
		}).animate();

		BX.addClass(propAngle, "fa-angle-down");
		BX.removeClass(propAngle, "fa-angle-up");
	}
	else
	{
		filterBlock.style.display = "block";
		filterBlock.style.opacity = 0;
		filterBlock.style.height = "auto";

		var obj_children_height = filterBlock.offsetHeight;
		filterBlock.style.height = 0;

		new BX.easing({
			duration : 300,
			start : { opacity: 0,  height: 0 },
			finish : { opacity: 1, height: obj_children_height },
			transition : BX.easing.transitions.quart,
			step : function(state){
				filterBlock.style.opacity = state.opacity;
				filterBlock.style.height = state.height + "px";
			},
			complete : function() {
			}
		}).animate();

		BX.addClass(obj, "bx-active");
		BX.removeClass(propAngle, "fa-angle-down");
		BX.addClass(propAngle, "fa-angle-up");
	}
};



/**
 * citrus custom function
 * */
//add values to label
JCSmartFilter.prototype.updateLabelValues = function ($fieldBlock) {
	if (!$fieldBlock.length) return;

	$fieldBlock.each(function () {
		var $block = $(this),
			template = $block.data('template'),
			$labelValue = $block.find('.citrus-sf-label_value');

		var addValue = function (value) {
			var html = '';
			if (value) html = '('+value+')';
			$block[value ? 'addClass' : 'removeClass']('has-value');

			$labelValue.html(html);
		};

		switch(template){
			case 'DROPDOWN':
				var $checkedText = $block.find('input:checked').parent().find('.citrus-select__item-name');
				var arText = [];
				$checkedText.each(function (index, item) {
					var text = $(item).html().trim();
					if (text.length) arText.push(text);
				});
				addValue(arText.join(', '));
				break;
			case 'LINE_CHECKBOX':
				var $checkedText = $block.find('input:checked').next('.line-checkbox__item-label');
				var arText = [];
				$checkedText.each(function (index, item) {
					var text = $(item).html().trim();
					if (text.length) arText.push(text);
				});
				addValue(arText.join(', '));
				break;
      case 'NUMBERS':
				var text = '',
					$minInput = $block.find('.filter-numbers_input._min'),
					$maxInput = $block.find('.filter-numbers_input._max'),
					minValue = $minInput.val(),
					maxValue = $maxInput.val();
				if (minValue || maxValue) {
					text += minValue ? minValue : $minInput.data('default-value');
					text += ' &ndash; ';
					text += maxValue ? maxValue : $maxInput.data('default-value');
				}
				addValue(text);
				break;
		}
		var $label = $block.find('.citrus-sf-label');
	});
};
//dropdown search
JCSmartFilter.prototype.searchDropdownItem = function (event, el) {
	var $searchInput = $(el),
		$container = $searchInput.parents('.citrus-sf-field'),
		searchText = $searchInput.val().trim(),
		searchParts = searchText.toLowerCase().replace(/,/g, "").split(' '),
		$items = $container.find('.citrus-select__item'),
		$selectAllLink = $container.find('.citrus-select__chose-all'),
		foundItems = 0;
	
	if (searchText.length && searchParts.length) {
		$items.each(function (index, item) {
			
			var $item = $(item),
				matched = searchParts.reduce(function(matched, searchPart) {
					return matched && $item.find('.citrus-select__item-name').text().toLocaleLowerCase().indexOf(searchPart) > -1;
				}, true);
			
			$item[matched ? 'removeClass' : 'addClass']('_search-filtered');
			if (matched) foundItems++;
		});
	}
	else {
		$items.removeClass('_search-filtered');
	}
	
	$selectAllLink[foundItems && searchText.length ? 'removeClass' : 'addClass']('hidden');
};
JCSmartFilter.prototype.choseAllDropdownItems = function(event, el){
	var $link = $(el),
		$container = $link.parents('.citrus-sf-field'),
		$foundItemCheckbox = $container.find('.citrus-select__item:not(._search-filtered):not(.disabled) input'),
		alreadyChecked = $foundItemCheckbox.length === $foundItemCheckbox.filter(':checked').length;
	
	$foundItemCheckbox.prop('checked', !alreadyChecked);
	this.click($foundItemCheckbox.first().get(0));
	this.updateLabelValues($container);
};
JCSmartFilter.prototype.toggleExpanded = function (e) {
	$(this.form).toggleClass("_open");
};
JCSmartFilter.prototype.clearField = function($fieldBlock){
	$fieldBlock.find('input:checkbox').prop('checked', false).trigger('clear');
	$fieldBlock.find('input:text').val('').trigger('clear');
	if ($fieldBlock.find('.citrus-select__search-input').length) $fieldBlock.find('.citrus-select__search-input').trigger('keyup');

	this.click($fieldBlock.find('input:first').get(0));
};
JCSmartFilter.prototype.toggleValues = function (el, event) {
	var $clearFieldBtn = $('.citrus-sf-label_close');
	var $field = $(el).parents('.citrus-sf-field');

	if (cui.ifdefined(event) && ($clearFieldBtn.is(event.target) || $clearFieldBtn.has(event.target).length)) {
		this.clearField($field);
		this.updateLabelValues($field);
		return;
	}

	if(cui.ifdefined(el)) {
		$('.citrus-sf-field').not($field).removeClass('_open');
		$field.toggleClass('_open');

		if ($field.hasClass('_open') && $field.find('.citrus-select__search-input').length ) $field.find('.citrus-select__search-input').focus();
	} else {
		$('.citrus-sf-field._open').removeClass('_open');
	}

};

JCSmartFilter.prototype.updateDuplicate = function () {
	var self = this;
	var $fieldBlock = $(this.form).find('.citrus-sf-field'),
		selectedPropertyValues = [],
		$duplicate = $('#sf-duplicate');

	var updateView = function (data) {
		$duplicate[ data.length ? 'removeClass':'addClass']('hidden');
		var html = '<div class="sf-duplicate__property-list">';
		
		$(data).each(function (index, property) {
			
			if (property.items.length > 10) {
				property.items = [
					{
						'name': self.params.LANG.VALUES + ' <b>'+property.items.length+'</b>',
						'id': property.items.reduce(function (str, item) {
							if (str.length) str += ';';
							return str+=item.id
						},'')
					}
				]
			}
			
			html +=
			'<div class="sf-duplicate__property-item">' +
				'<div class="sf-duplicate__property-name">'+property.title+':</div>' +
				'<div class="sf-duplicate__value-list">';

				$(property.items).each(function (index, item) {
					html +=
						'<div class="sf-duplicate__value-item">'+
							'<label class="sf-duplicate__value-label">'+
								'<input class="hidden sf-duplicate__checkbox" type="checkbox" data-id="'+item.id+'" checked="checked" data-property-code-for="'+property.code+'"/>'+
								'<span class="filter-checkmark"></span>'+
								'<div class="sf-duplicate__value-name">'+item.name+'</div>'+
							'</label>'+
						'</div>';
				});
			html +=
				'</div>' +
			'</div>';
		});
		html += '</div>';
		$duplicate.html(html);
	};

	var addValues = function (index, item) {
		var $item = $(item),
			isCombine = $item.data('combine');

		if (isCombine) {
			$item.find('[data-property-code]').each(addValues);
			return;
		}

		var propertyCode = $item.data('property-code'),
			template = $item.data('template'),
			itemSelectedPropertyValues = {
				code: propertyCode,
				title: $item.data('name') || $item.find('.citrus-sf-label_name').html(),
				items: []
			};

		switch (template) {
			case 'NUMBERS':
				var $minInput = $item.find('.filter-numbers_input._min'),
					$maxInput = $item.find('.filter-numbers_input._max'),
					minValue = $minInput.val(),
					maxValue = $maxInput.val();

				if (minValue) {
					itemSelectedPropertyValues.items.push({
						id: $minInput.prop('id'),
						name: self.params.LANG.FROM+' '+minValue
					});
				}
				if (maxValue) {
					itemSelectedPropertyValues.items.push({
						id: $maxInput.prop('id'),
						name: self.params.LANG.TO+' '+maxValue
					});
				}
				break;
			default:
				var $checkedInputs = $item.find('input:checked');
				$checkedInputs.each(function (index, checkbox) {
					var selectedItem = {
						id: $(checkbox).prop('id'),
						name: ($(checkbox).data('name')+'').trim()
					};
					itemSelectedPropertyValues.items.push(selectedItem);
				});
				break;
		}
		if (itemSelectedPropertyValues.items.length) selectedPropertyValues.push(itemSelectedPropertyValues);
	};

	$fieldBlock.each(addValues);
	updateView(selectedPropertyValues);
};



BX.namespace("BX.Iblock.SmartFilter");
BX.Iblock.SmartFilter = (function()
{
	var SmartFilter = function(arParams)
	{
		if (typeof arParams === 'object')
		{
			this.leftSlider = BX(arParams.leftSlider);
			this.rightSlider = BX(arParams.rightSlider);
			this.tracker = BX(arParams.tracker);
			this.trackerWrap = BX(arParams.trackerWrap);

			this.minInput = BX(arParams.minInputId);
			this.maxInput = BX(arParams.maxInputId);

			this.minPrice = parseFloat(arParams.minPrice);
			this.maxPrice = parseFloat(arParams.maxPrice);

			this.curMinPrice = parseFloat(arParams.curMinPrice);
			this.curMaxPrice = parseFloat(arParams.curMaxPrice);

			this.fltMinPrice = arParams.fltMinPrice ? parseFloat(arParams.fltMinPrice) : parseFloat(arParams.curMinPrice);
			this.fltMaxPrice = arParams.fltMaxPrice ? parseFloat(arParams.fltMaxPrice) : parseFloat(arParams.curMaxPrice);

			this.precision = arParams.precision || 0;

			this.priceDiff = this.maxPrice - this.minPrice;

			this.leftPercent = 0;
			this.rightPercent = 0;

			this.fltMinPercent = 0;
			this.fltMaxPercent = 0;

			this.colorUnavailableActive = BX(arParams.colorUnavailableActive);//gray
			this.colorAvailableActive = BX(arParams.colorAvailableActive);//blue
			this.colorAvailableInactive = BX(arParams.colorAvailableInactive);//light blue

			this.isTouch = false;

			this.init();

			if ('ontouchstart' in document.documentElement)
			{
				this.isTouch = true;

				BX.bind(this.leftSlider, "touchstart", BX.proxy(function(event){
					this.onMoveLeftSlider(event)
				}, this));

				BX.bind(this.rightSlider, "touchstart", BX.proxy(function(event){
					this.onMoveRightSlider(event)
				}, this));
			}
			else
			{
				BX.bind(this.leftSlider, "mousedown", BX.proxy(function(event){
					this.onMoveLeftSlider(event)
				}, this));

				BX.bind(this.rightSlider, "mousedown", BX.proxy(function(event){
					this.onMoveRightSlider(event)
				}, this));
			}

			BX.bind(this.minInput, "keyup", BX.proxy(function(event){
				this.onInputChange();
			}, this));

			BX.bind(this.maxInput, "keyup", BX.proxy(function(event){
				this.onInputChange();
			}, this));
		}
	};

	SmartFilter.prototype.init = function()
	{
		var priceDiff;

		if (this.curMinPrice > this.minPrice)
		{
			priceDiff = this.curMinPrice - this.minPrice;
			this.leftPercent = (priceDiff*100)/this.priceDiff;

			this.leftSlider.style.left = this.leftPercent + "%";
			this.colorUnavailableActive.style.left = this.leftPercent + "%";
		}

		this.setMinFilteredValue(this.fltMinPrice);

		if (this.curMaxPrice < this.maxPrice)
		{
			priceDiff = this.maxPrice - this.curMaxPrice;
			this.rightPercent = (priceDiff*100)/this.priceDiff;

			this.rightSlider.style.right = this.rightPercent + "%";
			this.colorUnavailableActive.style.right = this.rightPercent + "%";
		}

		this.setMaxFilteredValue(this.fltMaxPrice);
	};

	SmartFilter.prototype.setMinFilteredValue = function (fltMinPrice)
	{
		this.fltMinPrice = parseFloat(fltMinPrice);
		if (this.fltMinPrice >= this.minPrice)
		{
			var priceDiff = this.fltMinPrice - this.minPrice;
			this.fltMinPercent = (priceDiff*100)/this.priceDiff;

			if (this.leftPercent > this.fltMinPercent)
				this.colorAvailableActive.style.left = this.leftPercent + "%";
			else
				this.colorAvailableActive.style.left = this.fltMinPercent + "%";

			this.colorAvailableInactive.style.left = this.fltMinPercent + "%";
		}
		else
		{
			this.colorAvailableActive.style.left = "0%";
			this.colorAvailableInactive.style.left = "0%";
		}
	};

	SmartFilter.prototype.setMaxFilteredValue = function (fltMaxPrice)
	{
		this.fltMaxPrice = parseFloat(fltMaxPrice);
		if (this.fltMaxPrice <= this.maxPrice)
		{
			var priceDiff = this.maxPrice - this.fltMaxPrice;
			this.fltMaxPercent = (priceDiff*100)/this.priceDiff;

			if (this.rightPercent > this.fltMaxPercent)
				this.colorAvailableActive.style.right = this.rightPercent + "%";
			else
				this.colorAvailableActive.style.right = this.fltMaxPercent + "%";

			this.colorAvailableInactive.style.right = this.fltMaxPercent + "%";
		}
		else
		{
			this.colorAvailableActive.style.right = "0%";
			this.colorAvailableInactive.style.right = "0%";
		}
	};

	SmartFilter.prototype.getXCoord = function(elem)
	{
		var box = elem.getBoundingClientRect();
		var body = document.body;
		var docElem = document.documentElement;

		var scrollLeft = window.pageXOffset || docElem.scrollLeft || body.scrollLeft;
		var clientLeft = docElem.clientLeft || body.clientLeft || 0;
		var left = box.left + scrollLeft - clientLeft;

		return Math.round(left);
	};

	SmartFilter.prototype.getPageX = function(e)
	{
		e = e || window.event;
		var pageX = null;

		if (this.isTouch && event.targetTouches[0] != null)
		{
			pageX = e.targetTouches[0].pageX;
		}
		else if (e.pageX != null)
		{
			pageX = e.pageX;
		}
		else if (e.clientX != null)
		{
			var html = document.documentElement;
			var body = document.body;

			pageX = e.clientX + (html.scrollLeft || body && body.scrollLeft || 0);
			pageX -= html.clientLeft || 0;
		}

		return pageX;
	};

	SmartFilter.prototype.recountMinPrice = function()
	{
		var newMinPrice = (this.priceDiff*this.leftPercent)/100;
		newMinPrice = (this.minPrice + newMinPrice).toFixed(this.precision);

		if (newMinPrice != this.minPrice)
			this.minInput.value = newMinPrice;
		else
			this.minInput.value = "";
		smartFilter.keyup(this.minInput);
	};

	SmartFilter.prototype.recountMaxPrice = function()
	{
		var newMaxPrice = (this.priceDiff*this.rightPercent)/100;
		newMaxPrice = (this.maxPrice - newMaxPrice).toFixed(this.precision);

		if (newMaxPrice != this.maxPrice)
			this.maxInput.value = newMaxPrice;
		else
			this.maxInput.value = "";
		smartFilter.keyup(this.maxInput);
	};

	SmartFilter.prototype.onInputChange = function ()
	{
		var priceDiff;
		if (this.minInput.value)
		{
			var leftInputValue = this.minInput.value;
			if (leftInputValue < this.minPrice)
				leftInputValue = this.minPrice;

			if (leftInputValue > this.maxPrice)
				leftInputValue = this.maxPrice;

			priceDiff = leftInputValue - this.minPrice;
			this.leftPercent = (priceDiff*100)/this.priceDiff;

			this.makeLeftSliderMove(false);
		}

		if (this.maxInput.value)
		{
			var rightInputValue = this.maxInput.value;
			if (rightInputValue < this.minPrice)
				rightInputValue = this.minPrice;

			if (rightInputValue > this.maxPrice)
				rightInputValue = this.maxPrice;

			priceDiff = this.maxPrice - rightInputValue;
			this.rightPercent = (priceDiff*100)/this.priceDiff;

			this.makeRightSliderMove(false);
		}
	};

	SmartFilter.prototype.makeLeftSliderMove = function(recountPrice)
	{
		recountPrice = (recountPrice !== false);

		this.leftSlider.style.left = this.leftPercent + "%";
		this.colorUnavailableActive.style.left = this.leftPercent + "%";

		var areBothSlidersMoving = false;
		if (this.leftPercent + this.rightPercent >= 100)
		{
			areBothSlidersMoving = true;
			this.rightPercent = 100 - this.leftPercent;
			this.rightSlider.style.right = this.rightPercent + "%";
			this.colorUnavailableActive.style.right = this.rightPercent + "%";
		}

		if (this.leftPercent >= this.fltMinPercent && this.leftPercent <= (100-this.fltMaxPercent))
		{
			this.colorAvailableActive.style.left = this.leftPercent + "%";
			if (areBothSlidersMoving)
			{
				this.colorAvailableActive.style.right = 100 - this.leftPercent + "%";
			}
		}
		else if(this.leftPercent <= this.fltMinPercent)
		{
			this.colorAvailableActive.style.left = this.fltMinPercent + "%";
			if (areBothSlidersMoving)
			{
				this.colorAvailableActive.style.right = 100 - this.fltMinPercent + "%";
			}
		}
		else if(this.leftPercent >= this.fltMaxPercent)
		{
			this.colorAvailableActive.style.left = 100-this.fltMaxPercent + "%";
			if (areBothSlidersMoving)
			{
				this.colorAvailableActive.style.right = this.fltMaxPercent + "%";
			}
		}

		if (recountPrice)
		{
			this.recountMinPrice();
			if (areBothSlidersMoving)
				this.recountMaxPrice();
		}
	};

	SmartFilter.prototype.countNewLeft = function(event)
	{
		var pageX = this.getPageX(event);

		var trackerXCoord = this.getXCoord(this.trackerWrap);
		var rightEdge = this.trackerWrap.offsetWidth;

		var newLeft = pageX - trackerXCoord;

		if (newLeft < 0)
			newLeft = 0;
		else if (newLeft > rightEdge)
			newLeft = rightEdge;

		return newLeft;
	};

	SmartFilter.prototype.onMoveLeftSlider = function(e)
	{
		if (!this.isTouch)
		{
			this.leftSlider.ondragstart = function() {
				return false;
			};
		}

		if (!this.isTouch)
		{
			document.onmousemove = BX.proxy(function(event) {
				this.leftPercent = ((this.countNewLeft(event)*100)/this.trackerWrap.offsetWidth);
				this.makeLeftSliderMove();
			}, this);

			document.onmouseup = function() {
				document.onmousemove = document.onmouseup = null;
			};
		}
		else
		{
			document.ontouchmove = BX.proxy(function(event) {
				this.leftPercent = ((this.countNewLeft(event)*100)/this.trackerWrap.offsetWidth);
				this.makeLeftSliderMove();
			}, this);

			document.ontouchend = function() {
				document.ontouchmove = document.touchend = null;
			};
		}

		return false;
	};

	SmartFilter.prototype.makeRightSliderMove = function(recountPrice)
	{
		recountPrice = (recountPrice !== false);

		this.rightSlider.style.right = this.rightPercent + "%";
		this.colorUnavailableActive.style.right = this.rightPercent + "%";

		var areBothSlidersMoving = false;
		if (this.leftPercent + this.rightPercent >= 100)
		{
			areBothSlidersMoving = true;
			this.leftPercent = 100 - this.rightPercent;
			this.leftSlider.style.left = this.leftPercent + "%";
			this.colorUnavailableActive.style.left = this.leftPercent + "%";
		}

		if ((100-this.rightPercent) >= this.fltMinPercent && this.rightPercent >= this.fltMaxPercent)
		{
			this.colorAvailableActive.style.right = this.rightPercent + "%";
			if (areBothSlidersMoving)
			{
				this.colorAvailableActive.style.left = 100 - this.rightPercent + "%";
			}
		}
		else if(this.rightPercent <= this.fltMaxPercent)
		{
			this.colorAvailableActive.style.right = this.fltMaxPercent + "%";
			if (areBothSlidersMoving)
			{
				this.colorAvailableActive.style.left = 100 - this.fltMaxPercent + "%";
			}
		}
		else if((100-this.rightPercent) <= this.fltMinPercent)
		{
			this.colorAvailableActive.style.right = 100-this.fltMinPercent + "%";
			if (areBothSlidersMoving)
			{
				this.colorAvailableActive.style.left = this.fltMinPercent + "%";
			}
		}

		if (recountPrice)
		{
			this.recountMaxPrice();
			if (areBothSlidersMoving)
				this.recountMinPrice();
		}
	};

	SmartFilter.prototype.onMoveRightSlider = function(e)
	{
		if (!this.isTouch)
		{
			this.rightSlider.ondragstart = function() {
				return false;
			};
		}

		if (!this.isTouch)
		{
			document.onmousemove = BX.proxy(function(event) {
				this.rightPercent = 100-(((this.countNewLeft(event))*100)/(this.trackerWrap.offsetWidth));
				this.makeRightSliderMove();
			}, this);

			document.onmouseup = function() {
				document.onmousemove = document.onmouseup = null;
			};
		}
		else
		{
			document.ontouchmove = BX.proxy(function(event) {
				this.rightPercent = 100-(((this.countNewLeft(event))*100)/(this.trackerWrap.offsetWidth));
				this.makeRightSliderMove();
			}, this);

			document.ontouchend = function() {
				document.ontouchmove = document.ontouchend = null;
			};
		}

		return false;
	};

	return SmartFilter;
})();