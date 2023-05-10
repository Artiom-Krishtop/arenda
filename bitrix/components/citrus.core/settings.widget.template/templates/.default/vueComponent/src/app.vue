
<template>
	<div class="settings-widget print-hidden">
		<a href="javascript:void(0);" @click="open()" class="settings-widget__open-link">
			<svg class="svg-icon" viewBox="0 0 217.794 217.794"><use xlink:href="#svg-icon--settings"/></svg>
		</a>
		<div class="settings-widget-panel" :class="{'_open': isOpen}">
			<div class="settings-widget__header">
				<div class="settings-widget__title">{{lang.TITLE}}</div>
				<a
					@click="close()"
					class="settings-widget__close"
					href="javascript:void(0);">
					<svg class="svg-icon" viewBox="0 0 357 357"><use xlink:href="#svg-icon--close"/></svg>
				</a>
			</div>
			<div class="settings-widget__body">
				<div class="settings-widget__tabs-list">
					<div
						@click = 'selectedTabIndex = tabIndex'
						:class="{'_active': tabIndex === selectedTabIndex}"
						v-for="(tab, tabIndex) in tabs"
						class="settings-widget__tab">{{tab.title}}</div>
				</div>
				<div class="settings-widget__content">
					<div class="settings-widget__content-body">
						<keep-alive>
							<tab
								v-if="tabIndex === selectedTabIndex"
								v-for="(tab, tabIndex) in tabs"
								:tab="tab"
								:key="tabIndex"
							/>
						</keep-alive>
					</div>
					<div class="settings-widget__content-footer">
						<div class="settings-widget__content-footer__inner">
							<el-button @click="save()" :loading="loading" type="primary">{{lang.SAVE_BTN}}</el-button>
							<a class="settings-widget__reset-link"
							   href="javascript:void(0);"
							   @click="reset(true)">{{lang.RESET_BTN}}</a>
							
							<div class="settings-widget__extention" v-html="lang.WARNING"></div>
						</div>
					</div>
				</div>
			</div><!-- .settings-widget-body -->
		
		</div><!-- .settings-widget-panel -->
	</div>
</template>


<style src="./app.css" scoped></style>
<style>
	.settings-widget a {
		color: #cd4117;
	}
	
	body {
		transition: .15s linear;
	}
	body.citrus-widget-open {
		padding-left: 390px;
	}
	.settings-widget__extention a {
		color: #ef6704;
		text-decoration: underline;
	}
	.settings-widget__extention a:hover {
		text-decoration: none;
	}
	.el-checkbox__input.is-checked .el-checkbox__inner, .el-checkbox__input.is-indeterminate .el-checkbox__inner {
		background-color: #cd4117;
		border-color: #cd4117;
	}
	.el-checkbox__input.is-checked+.el-checkbox__label {
		color: #cd4117;
	}
	@media (max-width: 1440px) {
		body.citrus-widget-open {
			padding-left: 0;
		}
	}
</style>
<script src="./app.js"></script>