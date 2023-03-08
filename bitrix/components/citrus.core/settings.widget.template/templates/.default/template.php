<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$this->setFrameMode(true);

/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var \Citrus\Core\Components\CoreSettingsWidgetComponentTemplate $component */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Web\Json;

?>
<style>
	.hidden-svg-icons {
		visibility: hidden;
		position: absolute;
		display: none;
	}
	.hidden-svg-icons path, .hidden-svg-icons polygon {
		fill: currentColor;
	}
	.svg-icon {
		width: 1em;
		height: 1em;
		fill: currentColor;
	}
	.svg-icon path {
		fill: currentColor;
	}
	.svg-icon polygon {
		fill: currentColor;
	}
</style>

<?#svg icons?>
<div class="hidden-svg-icons">
	<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
	     viewBox="0 0 217.794 217.794">
		<g id="svg-icon--settings">
		<path d="M113.595,133.642l-5.932-13.169c5.655-4.151,10.512-9.315,14.307-15.209l13.507,5.118c2.583,0.979,5.469-0.322,6.447-2.904
			l4.964-13.103c0.47-1.24,0.428-2.616-0.117-3.825c-0.545-1.209-1.547-2.152-2.788-2.622l-13.507-5.118
			c1.064-6.93,0.848-14.014-0.637-20.871l13.169-5.932c1.209-0.545,2.152-1.547,2.622-2.788c0.47-1.24,0.428-2.616-0.117-3.825
			l-5.755-12.775c-1.134-2.518-4.096-3.638-6.612-2.505l-13.169,5.932c-4.151-5.655-9.315-10.512-15.209-14.307l5.118-13.507
			c0.978-2.582-0.322-5.469-2.904-6.447L93.88,0.82c-1.239-0.469-2.615-0.428-3.825,0.117c-1.209,0.545-2.152,1.547-2.622,2.788
			l-5.117,13.506c-6.937-1.07-14.033-0.849-20.872,0.636L55.513,4.699c-0.545-1.209-1.547-2.152-2.788-2.622
			c-1.239-0.469-2.616-0.428-3.825,0.117L36.124,7.949c-2.518,1.134-3.639,4.094-2.505,6.612l5.932,13.169
			c-5.655,4.151-10.512,9.315-14.307,15.209l-13.507-5.118c-1.239-0.469-2.615-0.427-3.825,0.117
			c-1.209,0.545-2.152,1.547-2.622,2.788L0.326,53.828c-0.978,2.582,0.322,5.469,2.904,6.447l13.507,5.118
			c-1.064,6.929-0.848,14.015,0.637,20.871L4.204,92.196c-1.209,0.545-2.152,1.547-2.622,2.788c-0.47,1.24-0.428,2.616,0.117,3.825
			l5.755,12.775c0.544,1.209,1.547,2.152,2.787,2.622c1.241,0.47,2.616,0.429,3.825-0.117l13.169-5.932
			c4.151,5.656,9.314,10.512,15.209,14.307l-5.118,13.507c-0.978,2.582,0.322,5.469,2.904,6.447l13.103,4.964
			c0.571,0.216,1.172,0.324,1.771,0.324c0.701,0,1.402-0.147,2.054-0.441c1.209-0.545,2.152-1.547,2.622-2.788l5.117-13.506
			c6.937,1.069,14.034,0.849,20.872-0.636l5.931,13.168c0.545,1.209,1.547,2.152,2.788,2.622c1.24,0.47,2.617,0.429,3.825-0.117
			l12.775-5.754C113.607,139.12,114.729,136.16,113.595,133.642z M105.309,86.113c-4.963,13.1-17.706,21.901-31.709,21.901
			c-4.096,0-8.135-0.744-12.005-2.21c-8.468-3.208-15.18-9.522-18.899-17.779c-3.719-8.256-4-17.467-0.792-25.935
			c4.963-13.1,17.706-21.901,31.709-21.901c4.096,0,8.135,0.744,12.005,2.21c8.468,3.208,15.18,9.522,18.899,17.778
			C108.237,68.434,108.518,77.645,105.309,86.113z M216.478,154.389c-0.896-0.977-2.145-1.558-3.469-1.615l-9.418-0.404
			c-0.867-4.445-2.433-8.736-4.633-12.697l6.945-6.374c2.035-1.867,2.17-5.03,0.303-7.064l-6.896-7.514
			c-0.896-0.977-2.145-1.558-3.47-1.615c-1.322-0.049-2.618,0.416-3.595,1.312l-6.944,6.374c-3.759-2.531-7.9-4.458-12.254-5.702
			l0.404-9.418c0.118-2.759-2.023-5.091-4.782-5.209l-10.189-0.437c-2.745-0.104-5.091,2.023-5.209,4.781l-0.404,9.418
			c-4.444,0.867-8.735,2.433-12.697,4.632l-6.374-6.945c-0.896-0.977-2.145-1.558-3.469-1.615c-1.324-0.054-2.618,0.416-3.595,1.312
			l-7.514,6.896c-2.035,1.867-2.17,5.03-0.303,7.064l6.374,6.945c-2.531,3.759-4.458,7.899-5.702,12.254l-9.417-0.404
			c-2.747-0.111-5.092,2.022-5.21,4.781l-0.437,10.189c-0.057,1.325,0.415,2.618,1.312,3.595c0.896,0.977,2.145,1.558,3.47,1.615
			l9.417,0.403c0.867,4.445,2.433,8.736,4.632,12.698l-6.944,6.374c-0.977,0.896-1.558,2.145-1.615,3.469
			c-0.057,1.325,0.415,2.618,1.312,3.595l6.896,7.514c0.896,0.977,2.145,1.558,3.47,1.615c1.319,0.053,2.618-0.416,3.595-1.312
			l6.944-6.374c3.759,2.531,7.9,4.458,12.254,5.702l-0.404,9.418c-0.118,2.759,2.022,5.091,4.781,5.209l10.189,0.437
			c0.072,0.003,0.143,0.004,0.214,0.004c1.25,0,2.457-0.468,3.381-1.316c0.977-0.896,1.558-2.145,1.615-3.469l0.404-9.418
			c4.444-0.867,8.735-2.433,12.697-4.632l6.374,6.945c0.896,0.977,2.145,1.558,3.469,1.615c1.33,0.058,2.619-0.416,3.595-1.312
			l7.514-6.896c2.035-1.867,2.17-5.03,0.303-7.064l-6.374-6.945c2.531-3.759,4.458-7.899,5.702-12.254l9.417,0.404
			c2.756,0.106,5.091-2.022,5.21-4.781l0.437-10.189C217.847,156.659,217.375,155.366,216.478,154.389z M160.157,183.953
			c-12.844-0.55-22.846-11.448-22.295-24.292c0.536-12.514,10.759-22.317,23.273-22.317c0.338,0,0.678,0.007,1.019,0.022
			c12.844,0.551,22.846,11.448,22.295,24.292C183.898,174.511,173.106,184.497,160.157,183.953z"/>
		</g>
	</svg>
	<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
	     width="357px" height="357px" viewBox="0 0 357 357">
		<g id="svg-icon--close">
			<polygon points="357,35.7 321.3,0 178.5,142.8 35.7,0 0,35.7 142.8,178.5 0,321.3 35.7,357 178.5,214.2 321.3,357 357,321.3
				214.2,178.5"/>
		</g>
	</svg>
</div>

<?

$settingsData = $arResult['DATA'];

$instructionLink = $arResult['LANG']['INSTRUCTION'] ?: 'http://citrus-soft.ru/docs/course/?COURSE_ID=5&LESSON_ID=67&LESSON_PATH=66.67';
$settingsData['lang'] = array(
	'TITLE' => Loc::getMessage("SETTINGS_WIDGET_TITLTE"),
	'LOGO_COLOR_BTN' => Loc::getMessage("SETTINGS_WIDGET_LOGO_PICK"),
	'LOAD_FILE' => Loc::getMessage("SETTINGS_WIDGET_LOAD_FILE"),
	'SIZE' => Loc::getMessage("SETTINGS_WIDGET_SIZE"),
	'FORMAT' => Loc::getMessage("SETTINGS_WIDGET_FORMAT"),
	'SAVE_BTN' => Loc::getMessage("SETTINGS_WIDGET_SAVE"),
	'RESET_BTN' => Loc::getMessage("SETTINGS_WIDGET_RESET"),
	'PICK_FROM_LOGO' => Loc::getMessage("SETTINGS_WIDGET_PICK_FROM_LOGO"),
	'WARNING' => Loc::getMessage("SETTINGS_WIDGET_WARNING", ["#LINK#" => $instructionLink]),
	'COLORPICKER_CLEAR' => Loc::getMessage("SETTINGS_WIDGET_COLORPICKER_CLEAR"),
	'SAVE_SUCCESS' => Loc::getMessage("SETTINGS_WIDGET_SAVE_SUCCESS"),
	'SAVE_ERROR' => Loc::getMessage("SETTINGS_WIDGET_SAVE_ERROR"),
	'SAVE_NO_DATA' => Loc::getMessage("SETTINGS_WIDGET_SAVE_NO_DATA"),
	'COLORPICKER_NO_LOGO_HINT' => Loc::getMessage("SETTINGS_WIDGET_COLORPICKER_NO_LOGO_HINT"),
	'COLORPICKER_HINT' => Loc::getMessage("SETTINGS_WIDGET_COLORPICKER_HINT")
);
?>

<div id="vue-settings-widget"></div>
<script>
	;(function(){
		new Vue({
			el: '#vue-settings-widget',
			data: <?=Json::encode($settingsData)?>,
			template: '<settings-widget :tabs="tabs" :fields="fields" :lang="lang" :arParams="arParams" />',
		});
	}());
</script>
