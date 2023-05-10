<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

$footerCallout = $APPLICATION->GetProperty("SHOW_FOOTER_CALLOUT");

?>
<?$APPLICATION->IncludeComponent(
	"citrus.arealty:callout",
	".default",
	array(
		"IBLOCK_ID" => "callout",
		"ID" => $footerCallout == 'Y'
			? 'ne-nashli-podkhodyashchego-varianta'
			: $footerCallout,
		"COMPONENT_TEMPLATE" => "callout",
		"IBLOCK_TYPE" => "info",
		"COMPOSITE_FRAME_MODE" => "A",
		"COMPOSITE_FRAME_TYPE" => "AUTO"
	),
	false
);?>

