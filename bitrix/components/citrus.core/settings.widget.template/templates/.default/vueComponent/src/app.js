import tab from './components/tab.vue'
import {Button, Message} from 'element-ui';
import globalData from './globalData'
Vue.component(Button.name, Button);

Vue.prototype.$message = Message;

/**
 * IE11 polyfills
 */
if (!String.prototype.includes) {
	Object.defineProperty(String.prototype, "includes", {
		enumerable: false,
		writable: true,
		value: function(search, start) {
			'use strict';
			if (typeof start !== 'number') {
				start = 0;
			}

			if (start + search.length > this.length) {
				return false;
			} else {
				return this.indexOf(search, start) !== -1;
			}
		}
	});
}
if (!Array.prototype.includes) {
	Object.defineProperty(Array.prototype, "includes", {
		enumerable: false,
		writable: true,
		value: function(searchElement /*, fromIndex*/ ) {
			'use strict';
			var O = Object(this);
			var len = parseInt(O.length) || 0;
			if (len === 0) {
				return false;
			}
			var n = parseInt(arguments[1]) || 0;
			var k;
			if (n >= 0) {
				k = n;
			} else {
				k = len + n;
				if (k < 0) {k = 0;}
			}
			var currentElement;
			while (k < len) {
				currentElement = O[k];
				if (searchElement === currentElement ||
					(searchElement !== searchElement && currentElement !== currentElement)) { // NaN !== NaN
					return true;
				}
				k++;
			}
			return false;
		}
	});
}

Vue.component('v-style', {
	render: function (createElement) {
		return createElement('style', this.$slots.default)
	}
});

export default {
	name: 'app',
	props: ['tabs', 'fields', 'lang', 'arParams'],
	data: function () {
		return {
			isOpen: false,
			selectedTabIndex: 0,
			mouseOver: false
		}
	},
	computed: {
		dataFields: function () {
			return globalData.fields;
		},
		loading: function () {
			return globalData.loading;
		}
	},
	methods: {
		save: function () {
			let changedFields = {};
			let fieldParameters = {};
			let isData = false;
			this.dataFields.forEach(function (field) {
				if (!globalData.changedFieldCodes.includes(field.code)) return;

				let fieldValue;
				isData = true;
				switch (field.type) {
					case 'checkbox':
						fieldValue = field.checked ? 'Y' : 'N';
						break;
					case 'blocks':
						fieldValue = {};
						field.values.forEach(function (valueItem) {
							fieldValue[valueItem.value] = valueItem.checked;
						});
						break;
					case 'colorScheme':
						fieldValue = field.value;
						fieldParameters[field.code] = {'THEME_PATH' : field.settings.THEME_PATH};
						break;
					default:
						fieldValue = field.value;
						break;
				}

				changedFields[field.code] = fieldValue;
			});

			if (isData) {
				globalData.loading = true;
				$.ajax({
				    url: this.arParams.actions.save,
				    type: 'POST',
				    dataType: 'json',
				    data: {fields: changedFields, fieldParameters: fieldParameters},
				})
				.done((data) => {
					this.$message({
						message: this.$root.lang.SAVE_SUCCESS,
						type: 'success',
						showClose: true,
					});
				})
				.fail((data) => {
					this.$message({
						message: (data && data.responseJSON) ? data.responseJSON.error : this.$root.lang.SAVE_ERROR,
						type: 'error',
						showClose: true,
					});
				})
				.always(() => {
					globalData.loading = false;
				});
			} else {
				this.$message({
					message: this.$root.lang.SAVE_NO_DATA,
					type: 'warning',
					showClose: true,
				});
			}
		},
		reset: function (callEvent) {
			let fields = this.cloneData(this.fields);
			fields = fields.map(field => {
				if (this.arParams && this.arParams.fieldSettings && this.arParams.fieldSettings[field.code] ) {
					field.settings = this.arParams.fieldSettings[field.code];
				}
				return field;
			});

			globalData.fields = fields;
			if (callEvent) globalData.$emit('reset');
		},
		cloneData: function (data) {
			let skipProperties = ['__ob__', 'reactiveGetter', 'reactiveSetter'];
			//array
			if (Array.isArray(data)) {
				let cloneArray = [];
				data.forEach((dataArrayItem, dataArrayIndex) => {
					if (skipProperties.indexOf(dataArrayIndex) > -1) return;
					cloneArray[dataArrayIndex] = this.cloneData(dataArrayItem);
				});
				return cloneArray;
			}
			//object
			if (typeof data === 'object' && ''+data == "[object Object]") {
				let cloneObject = {};
				let dataObjectItemKey;
				for (dataObjectItemKey in data) {
					if (skipProperties.indexOf(dataObjectItemKey) > -1) continue;
					cloneObject[dataObjectItemKey] = this.cloneData(data[dataObjectItemKey]);
				}
				return cloneObject;
			}
			return data;
		},
		open: function () {
			this.isOpen = true;
			$('body').addClass('citrus-widget-open');
			if (dispatchEvent in window) {
				setTimeout(()=>{
					window.dispatchEvent(new Event('resize'));
				}, 150);
			}
		},
		close: function () {
			this.isOpen = false;
			$('body').removeClass('citrus-widget-open');
			if (dispatchEvent in window) {
				setTimeout(()=>{
					window.dispatchEvent(new Event('resize'));
				}, 150);
			}
		},
	},
	created: function(){
		window.settingsWidget = globalData;
		BX.onCustomEvent('settings.widget');
		this.reset();
		globalData.$on('updateField', (fieldCode, updateFieldData) => {
			if (typeof fieldCode !== 'string' && typeof updateFieldData !== 'object') return;

			let field = globalData.getFieldByCode(fieldCode);
			for (let dataName in updateFieldData) {
				if (updateFieldData.hasOwnProperty(dataName))
					field[dataName] = updateFieldData[dataName];
			}
		});
	},
	mounted: function(){
		// click of widget
		$(document).on('click', (e) => {
			if ($(this.$el).has(e.target).length === 0 && !$(this.$el).is(e.target)){
				this.close();
			}
		});
	},
	components: {tab}
}
