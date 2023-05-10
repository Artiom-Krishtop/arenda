<template>
	<div class="tab-content">
		<div
			v-for="section in tab.sections"
			tabindex="-9999"
			v-focus
			class="section">
			
			<div class="section-title">
				{{section.title}}
			</div>
			
			<div class="section-fields">
				<div
					v-for="field in getSectionFields(section.code)"
					:data-field-code="field.code"
					class="field">
					<el-checkbox
							@change="change(field)"
							v-model="field.checked"
					        v-if="field.type === 'checkbox'">
						{{field.title}}
					</el-checkbox>
					<template
						v-if="field.type === 'select'">
						<div class="field-title">{{field.title}}:</div>
						<el-select v-model="field.value"
						           @change="change(field)"
						           :placeholder="field.title">
							<el-option
								v-for="item in field.values"
								:label="item.label"
								:key="field.code + item.value"
								:value="item.value">
							</el-option>
						</el-select>
					</template>
					<el-input
							@keyup.native="change(field)"
							@change="change(field)"
							v-if="field.type === 'text'"
							:placeholder="field.title"
							clearable
					        v-model="field.value"></el-input>
					<div v-if="field.type === 'blocks'"
					     class="block-checkbox"
						 v-for="item in field.values">
						<el-checkbox
							@change="change(field, item)"
							v-model="item.checked">
							<div class="block-checkbox__label">
								<div class="block-checkbox__label-name">{{item.label}}</div>
								<div class="block-checkbox__label-image"><img :src="item.image" alt=""></div>
							</div>
						</el-checkbox>
					</div>
					<template v-if="field.type === 'colorScheme'">
						<div class="field-title">{{field.title}}:</div>
						<color-scheme @change="change(field)" :field="field"></color-scheme>
					</template>
					<avatar @change="change(field)" v-if="field.type === 'avatar'" :field="field"></avatar>
					
					<template v-if="field.type === 'html'">
						<div class="field-title">{{field.title}}:</div>
						<div class="field-html-block" v-html="field.value"></div>
					</template>
						
				</div>
			</div> <!-- .section-fields -->
		</div> <!-- .section -->
	</div><!-- .tab-content -->
</template>

<script>
	import { Checkbox, Input, Select, Option, ColorPicker } from 'element-ui';
	Vue.component(Checkbox.name, Checkbox);
	Vue.component(Select.name, Select);
	Vue.component(Option.name, Option);
	Vue.component(Input.name, Input);
	Vue.component(ColorPicker.name, ColorPicker);
	
	import lang from 'element-ui/lib/locale/lang/ru-RU'
	import locale from 'element-ui/lib/locale';
	
	import colorScheme from './colorScheme';
	import avatar from './avatar';
	import globalData from '../globalData';
	
	export default {
		name: "tab",
		props: ['tab'],
		data: function(){
			return {
			
			}
		},
		computed: {
			fields: function () {
				return globalData.fields;
			}
		},
		methods: {
			getSectionFields: function (sectionCode) {
				return this.fields.filter(field => {
					return field.section === sectionCode;
				});
			},
			/**
			 * Выбранный элемент у массива
			 * @param {Array} select
			 * @return {Object}
			 */
			getSelectedItem: function(select){
				return select.values.filter(valueItem => {
					return valueItem.value === select.value;
				})[0];
			},
			change: function (field, item, fromReset) {
				
				if (!fromReset && !globalData.changedFieldCodes.includes(field.code))
					globalData.changedFieldCodes.push(field.code);
				
				switch (field.code) {
					case ('LOGO'):
					case ('SCHEME'):
					case ('FAVICON'):
						if (!!fromReset && field.code === 'SCHEME') return;
						
						let fieldLogo = globalData.getFieldByCode('LOGO');
						let fieldScheme = globalData.getFieldByCode('SCHEME');
						let fieldFavicon = globalData.getFieldByCode('FAVICON');
						
						if (field.code === 'SCHEME' || field.code === 'LOGO') {
							let schemeLogoField = {
								code: 'SCHEME_LOGO',
								value: fieldLogo.value ||
								(fieldScheme.settings.FILES &&
								fieldScheme.settings.FILES.png &&
								fieldScheme.settings.FILES.png['logo.png'] ?
									fieldScheme.settings.FILES.png['logo.png'] : '')
							};
							globalData.$emit('change', schemeLogoField);
						}
						
						if ((field.code === 'SCHEME' || field.code === 'FAVICON') && fieldFavicon) {
							let schemeFaviconField = {
								code: 'SCHEME_FAVICON',
								value: (fieldFavicon ? fieldFavicon.value : false)  ||
								(fieldScheme.settings.FILES &&
								fieldScheme.settings.FILES.png &&
								fieldScheme.settings.FILES.png['logo.png'] ?
									fieldScheme.settings.FILES.png['logo.png'] : '')
							};
							globalData.$emit('change', schemeFaviconField);
						}
						break;
					case 'CURRENCY_FACTOR':
						let currencyField = globalData.getFieldByCode('CURRENCY');
						this.getSelectedItem(currencyField)['factor'] = field.value;
						break;
					case 'CURRENCY':
						let currencyFactorField = globalData.getFieldByCode('CURRENCY_FACTOR');
						currencyFactorField.value = this.getSelectedItem(field)['factor'];
						break;
					default:
						break;
				}
				globalData.$emit('change', field, item);
			},
		},
		mounted: function(){
			globalData.$on('reset', () => {
				this.fields.forEach(field => {
					if (globalData.changedFieldCodes.includes(field.code))
						this.change(field, undefined, true);
				});
				globalData.changedFieldCodes = [];
			});
		},
		directives: {
			focus: {
				// определение директивы
				inserted: function (el) {
					el.focus()
				}
			}
		},
		created: function(){
			lang.el.colorpicker.clear = this.$root.lang.COLORPICKER_CLEAR;
			// configure language
			locale.use(lang);
		},
		components: {colorScheme, avatar}
	}
</script>

<style scoped>
	@media all {
		.tab-content {
			width: 300px;
			overflow: hidden;
		}
		.section {
			padding: 25px 20px;
		}
		.section + .section {
			border-top: 1px solid #cccccc;
		}
		.section-title {
			font-weight: 600;
			font-size: 14px;
			text-transform: uppercase;
			color: #333333;
			margin-bottom: 14px;
		}
		.section-fields {
		
		}
		.field + .field {
			margin-top: 15px;
		}
		.field-title {
			font-size: 13px;
			color: #666666;
			margin-bottom: 5px;
		}
		.field-html-block {
			font-size: 12px;
			line-height: 1.2;
			color: #cd4117;
		}
	}
	/*fields*/
	@media all {
		.el-select {
			width: 100%;
			font-size: 13px;
		}
		.el-input {
			font-size: 13px;
		}
	}
</style>

<style>
	.field-html-block input[type='button'] {
		padding: 12px 20px;
		font-size: 14px;
		border-radius: 4px;
		font-weight: 500;
		border: 1px solid #d3d4d6;
		display: inline-block;
		line-height: 1;
		white-space: nowrap;
		cursor: pointer;
		
		color: #909399;
		background: #f4f4f5;
	}
	.field-html-block input[type='button']:hover {
		 background: #909399;
		 border-color: #909399;
		 color: #fff;
	 }
	.field-html-block input[type='button']:active {
		background: #82848a;
		border-color: #82848a;
		color: #fff;
		outline: 0;
	}
	.el-checkbox__label {
		font-size: 13px;
	}
	.el-input {
		font-size: 13px;
	}
	.el-select-dropdown__item {
		font-size: 13px;
	}
	.block-checkbox + .block-checkbox {
		margin-top: 5px;
	}
	.block-checkbox__label-image {
		margin-top: 2px;
	}
	.block-checkbox .el-checkbox__input {
		vertical-align: top;
		margin-top: 3px;
	}
	/*.el-checkbox.is-checked .block-checkbox__label-image img{
		outline: 1px solid;
	}*/
</style>