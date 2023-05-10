<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

global $USER;

?>
<aside class="mobile-sidebar">
	<div class="mobile-sidebar__inner">
		<!--noindex-->
		<?$APPLICATION->IncludeComponent(
			"bitrix:search.form",
			"mobile",
			[
				"PAGE" => SITE_DIR . "search/",
			]
		);?>
		<!--/noindex-->

		<?$APPLICATION->IncludeComponent(
			"bitrix:menu",
			"mobile",
			[
				"ROOT_MENU_TYPE" => "top",
				"MAX_LEVEL" => "3",
				"CHILD_MENU_TYPE" => "left",
				"USE_EXT" => "Y",
				"MENU_CACHE_TYPE" => "A",
				"MENU_CACHE_TIME" => "36000000",
				"MENU_CACHE_USE_GROUPS" => "Y",
				"MENU_CACHE_GET_VARS" => "",
				"IS_AUTHORIZED" => $USER->IsAuthorized()
			]
		);?>

		<?$APPLICATION->IncludeComponent("citrus:currency", '', [], null, ['HIDE_ICONS' => 'Y']);?>
	</div>
</aside>
