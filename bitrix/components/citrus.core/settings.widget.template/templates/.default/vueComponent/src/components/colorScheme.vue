<template>
	<div class="color-scheme">
		<div class="color-scheme__values">
			<div class="color-scheme__picker-wrapper">
				<div class="color-scheme__predefine"
				     :class="{'_selected': (valuesColor === color)}"
				     v-for="color in field.values"
				     :style="{'background-color': color}"
				     @click="changeValuesColor(color)"
				></div>
				
				<el-color-picker
					:title="$root.lang.COLORPICKER_HINT"
					v-model="pickerColor"
					:class="{'_selected': pickerColor}"
					@change="changePickerColor()"
				></el-color-picker>
				
				<i class="color-scheme__picker-loading-icon el-icon-loading" v-if="loading"></i>
			</div>
			
			<el-button style="margin-top: 10px;"
			           :disabled="!logo"
			           :title="!logo ? $root.lang.COLORPICKER_NO_LOGO_HINT : ''"
			           @click="pickFromLogo" class="btn-full-width" type="info" plain>{{$root.lang.LOGO_COLOR_BTN}}</el-button>
			
		</div>
	</div>
</template>

<script>
	import * as Vibrant from 'node-vibrant';
	import globalData from '../globalData';
	
	/**
	 * @property {Object} field
	 * @property {Array} field.values
	 */
	export default {
		name: "colorScheme",
		props: ['field'],
		data: function(){
			return {
				loading: false,
				// styleNode: document.createElement('style'),
				pickerColor: '',
				valuesColor: '',
			};
		},
		watch: {
			'pickerColor': function () {
			
			},
			'field.value': function (newVal, old) {
				if (this.field.values.includes(newVal)) {
					this.valuesColor = newVal;
					this.pickerColor = '';
				} else {
					this.valuesColor = '';
					this.pickerColor = newVal;
				}
				
				this.generateTheme(newVal);
			}
		},
		computed: {
			logo: function () {
				return globalData.getFieldByCode('LOGO').value;
			}
		},
		methods: {
			changeValuesColor: function(color){
				this.valuesColor = color;
				this.pickerColor = '';
				this.field.value = color;
			},
			changePickerColor: function(){
				this.valuesColor = '';
				this.field.value = this.pickerColor;
			},
			pickFromLogo: function () {
				if (this.logo) {
					Vibrant.from(this.logo).getPalette((err, palette) => {
						var maxPalette;
						for (var swatch in palette) {
							if (palette.hasOwnProperty(swatch) && palette[swatch]
								&& palette[swatch].getPopulation() > 0 &&
								(!maxPalette || palette[swatch].getPopulation() > maxPalette.getPopulation()) ) {
								
								maxPalette = palette[swatch];
							}
						}
						if(maxPalette) {
							let maxColor =  maxPalette.getHex();
							this.valuesColor = '';
							this.pickerColor = maxColor;
							this.field.value = maxColor;
						}
					});
				}
			},
			generateTheme: function(newColor){
				globalData.loading = true;
				$.ajax({
				    url: this.$root.arParams.actions.color,
				    type: 'POST',
				    dataType: 'json',
				    data: {color: newColor},
				})
				.done((data) => {
					if (!data) {
						console.error('error scheme generate');
						console.log(data);
					}
					this.field.settings['FILES'] = data['FILES'];
					this.field.settings['THEME_PATH'] = data['THEME_PATH'];
					
					this.$emit('change', this.field);
				})
				.fail(function() {
				    console.log("error scheme generate");
				})
				.always(function() {
					globalData.loading = false;
				});
			},
		},
		mounted: function () {
			this.valuesColor = (this.field.value && this.field.values && this.field.values.includes(this.field.value)) ?
				this.field.value : '';
			
			this.pickerColor = this.field.value && !this.valuesColor ? this.field.value : '';
		}
	}
</script>

<style >
	.el-color-picker {
		width: 33px;
		margin: 6px;
		height: 33px;
	}
	.el-color-picker__color {
		border: none;
		border-radius: 0;
	}
	.el-color-picker__trigger {
		width: 100%;
		height: 100%;
		padding: 0;
		border: 5px solid #fff;
		box-shadow: 0 0 0 1px #cccccc;
		border-radius: 0;
	}
	.el-color-picker._selected .el-color-picker__trigger {
		box-shadow: 0 0 8px 0 rgba(0, 0, 0, 0.5);
	}
	.el-color-dropdown__link-btn {
		padding: 0;
	}
	.btn-full-width {
		width: 100%;
	}
	.el-color-picker__mask {
		width: 99%;
	}
	.el-color-picker .el-color-picker__empty {
		height: 100%;
		background-repeat: no-repeat;
		background-position: center center;
		background-size: 70%;
		background-image: url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABIAAAASCAYAAABWzo5XAAABA0lEQVQ4jaXToU7DUBQG4K9kWTBTGAhPAQ4SDAKHQ4O6gnlgwRMcEpbNYDAEjYOEB8BgeAQchqCgG6It3C1bu45jTnrv7Zf796RJr9czRy0gQRpC+F2oU01cI8U33vr9/mZdqIk7HERry7iFZIZoCVbQxe6UM41GBdLCE9ZKzqQYVEXbqUDgIoQwrILWK/a7OKH8Y3dwigG+piDtEMKwDOrgPEf2sScbd1zHGBYPk6Bx5AariAfziI/4pfGpTUIOcRntP8smOVIxVIW0cTUhwUi0rf8gMdTK+/08CH/RlvK+gQds10GKGyU4i8DaSHGjRPYXv+IIn3jHy6xIAQ2wmPe56wc390NSFERYTQAAAABJRU5ErkJggg==');
		width: 100%;
	}
	.el-color-picker .el-color-picker__empty:before {
		content: '';
	}
	.el-color-picker .el-color-picker__icon {
		height: 100%;
		width: 100%;
		background-repeat: no-repeat;
		background-position: center center;
		background-size: 70%;
		background-image: url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABIAAAASCAYAAABWzo5XAAAA6klEQVQ4jaXUMU7CUADG8f+rgY0LcAsYSdyMgwMXIB6AAxg4hauOngFnr+DgzERMXIybCVHgz+AjeRToa/FLurT9fmm/NA0qZ6QAArBOTzRJG3iKwAr4AAYAqHWPtjrzMO9qLSio3RPILhc5pKO+VgCqKzXkNroGepl77gFzUD9z/RGY5Maexkdfqz9HXukh7kfV2CkyUofqbwnqpJ06COq4hLyUe02RiXqltqqgHDI+McMedPkfJIVuYuH5HCSFbmPpMw7ZCNlBQV14mNqISlALYAnMgTvgG/gC3jJf9V6Cfz+2Atg0KZazBWTG6F5kJppkAAAAAElFTkSuQmCC');
	}
	.el-color-picker .el-color-picker__icon:before {
		display: none;
	}
	
	
	
	.color-scheme__picker-wrapper {
		position: relative;
		display: flex;
		flex-wrap: wrap;
		margin: -5px;
	}
	.color-scheme__predefine {
		width: 33px;
		height: 33px;
		border: 5px solid #fff;
		box-shadow: 0 0 0 1px #cccccc;
		margin: 6px;
		cursor: pointer;
	}
	.color-scheme__predefine._selected {
		box-shadow: 0 0 8px 0 rgba(0, 0, 0, 0.5);
	}
	
	.color-scheme__picker-loading-icon {
		position: absolute;
		left: 100%;
		top: 0;
		bottom: 0;
		height: 1em;
		margin: auto 0 auto 2px;
	}
</style>