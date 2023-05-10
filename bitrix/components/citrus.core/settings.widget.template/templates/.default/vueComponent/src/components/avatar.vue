<template>
	<div class="settings-widget__avatar-field">
		<div class="settings-widget__avatar">
			<input class="settings-widget__avatar-input" type="file"
			       :name="field.code"
			       accept=".jpg, .jpeg, .png"
			       @change="change($event.target)">
			<img ref="preview" class="settings-widget__avatar-preview" v-if="field.value" :src="field.value" alt="">
			<span class="settings-widget__avatar-bg" ></span>
			<span class="settings-widget__avatar-name" v-if="!field.value">{{$root.lang.LOAD_FILE}}</span>
			<span class="settings-widget__avatar-clear"
			      @click="field.value = ''"
			      v-if="field.value">
				<svg class="svg-icon" viewBox="0 0 357 357"><use xlink:href="#svg-icon--close"/></svg>
			</span>
		</div>
		<div class="settings-widget__avatar-description-block">
			<b class="settings-widget__avatar-title">{{field.title}}</b> <br>
			<div class="settings-widget__avatar-description">
				<template v-if="field.settings.size">{{$root.lang.SIZE}}: <nobr>{{field.settings.size[0]}}x{{field.settings.size[1]}} px</nobr>,</template>
				<template v-if="field.settings.accept">
					<br> {{field.settings.accept}}
				</template>
				<template v-if="field.code === 'FAVICON'">
					<br><br>
					<a
						class="settings-widget__avatar__logo-pick"
						:disabled="!logo"
					   @click="pickFromLogo()"
					   href="javascript:void(0);">{{ $root.lang['PICK_FROM_LOGO']}}</a>
				</template>
				
			</div>
		</div>
	</div>
</template>

<script>
	import globalData from '../globalData';
	/**
	 * @param this.$event
	 */
	export default {
		name: "avatar",
		props: ['field'],
		data: function(){
			return {
				dataImage: ''
			}
		},
		watch: {
			'field.value': function(val){
				this.$emit('change');
			}
		},
		computed: {
			'logo': function(){
				return globalData.getFieldByCode('LOGO').value;
			}
		},
		methods: {
			'pickFromLogo': function () {
				globalData.resize(this.logo, this.field.settings, src => {
					this.field.value = src;
				});
			},
			'change': function (input) {
				if (input.value) {
					let file = input.files[0];
					let reader = new FileReader();
					reader.readAsDataURL(file);
					reader.onload = e => {
						if (this.field.settings.resize) {
							globalData.resize(e.target.result, this.field.settings, src => {
								this.field.value = e.target.result;
							});
						}
						else {
							this.field.value = e.target.result;
						}
					}
				} else {
					this.field.value = '';
				}
			}
		},
	}
</script>

<style scoped>
	.settings-widget__avatar-field {
		display: flex;
		align-items: center;
	}
	.settings-widget__avatar-description {
		font-size: 12px;
		color: #666;
		line-height: 1.2;
		margin-top: 10px;
	}
	.settings-widget__avatar__logo-pick {
	
	}
	.settings-widget__avatar__logo-pick[disabled='disabled'] {
		opacity: 0.5;
		cursor: default;
		text-decoration: none;
	}
	.settings-widget__avatar-preview {
		position: absolute;
		left: 50%;
		top: 50%;
		transform: translate(-50%, -50%);
		max-width: 90%;
		max-height: 90%;
		pointer-events: none;
	}
	.settings-widget__avatar {
		margin-right: 20px;
		position: relative;
		width: 118px;
		height: 108px;
		background-color: #f9f8f5;
		border: 1px dashed #c3b89e;
		display: flex;
		align-items: center;
		justify-content: center;
		padding: 10px;
		cursor: pointer;
		background-size: contain;
		background-position: center;
		background-repeat: no-repeat;
		flex-shrink: 0;
	}
	.settings-widget__avatar-input {
		opacity: 0;
		position: absolute;
		top: 0;
		left: 0;
		width: 100%;
		height: 100%;
		cursor: pointer;
	}
	.settings-widget__avatar-name {
		font-weight: bold;
		color: #847f73;
		font-size: 14px;
		text-align: center;
		text-transform: uppercase;
	}
	.settings-widget__avatar-clear {
		position: absolute;
		top: -1.5em;
		right: -1.5em;
		font-size: 9px;
		background-color: white;
		width: 3em;
		height: 3em;
		display: flex;
		align-items: center;
		justify-content: center;
		border-radius: 50%;
		border: 1px solid #c3b89e;
		color: #333;
	}
</style>
