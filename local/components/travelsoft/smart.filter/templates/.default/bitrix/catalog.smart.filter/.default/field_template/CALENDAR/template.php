<div class="bx_filter_parameters_box_container_block">
	<div class="bx_filter_input_container bx_filter_calendar_container">
		<? $APPLICATION->IncludeComponent(
			'bitrix:main.calendar',
			'',
			array(
				'FORM_NAME' => $arResult["FILTER_NAME"] . "_form",
				'SHOW_INPUT' => 'Y',
				'INPUT_ADDITIONAL_ATTR' => 'class="calendar" placeholder="' . FormatDate("SHORT", $arItem["VALUES"]["MIN"]["VALUE"]) . '" onkeyup="smartFilter.keyup(this)" onchange="smartFilter.keyup(this)"',
				'INPUT_NAME' => $arItem["VALUES"]["MIN"]["CONTROL_NAME"],
				'INPUT_VALUE' => $arItem["VALUES"]["MIN"]["HTML_VALUE"],
				'SHOW_TIME' => 'N',
				'HIDE_TIMEBAR' => 'Y',
			),
			null,
			array('HIDE_ICONS' => 'Y')
		); ?>
	</div>
</div>
<div class="bx_filter_parameters_box_container_block">
	<div class="bx_filter_input_container bx_filter_calendar_container">
		<? $APPLICATION->IncludeComponent(
			'bitrix:main.calendar',
			'',
			array(
				'FORM_NAME' => $arResult["FILTER_NAME"] . "_form",
				'SHOW_INPUT' => 'Y',
				'INPUT_ADDITIONAL_ATTR' => 'class="calendar" placeholder="' . FormatDate("SHORT", $arItem["VALUES"]["MAX"]["VALUE"]) . '" onkeyup="smartFilter.keyup(this)" onchange="smartFilter.keyup(this)"',
				'INPUT_NAME' => $arItem["VALUES"]["MAX"]["CONTROL_NAME"],
				'INPUT_VALUE' => $arItem["VALUES"]["MAX"]["HTML_VALUE"],
				'SHOW_TIME' => 'N',
				'HIDE_TIMEBAR' => 'Y',
			),
			null,
			array('HIDE_ICONS' => 'Y')
		); ?>
	</div>
</div>