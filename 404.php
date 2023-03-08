<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/urlrewrite.php');

CHTTP::SetStatus("404 Not Found");
@define("ERROR_404","Y");

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

$APPLICATION->SetTitle("");
$APPLICATION->SetPageProperty("TITLE", "Страница не найдена (404)");
$APPLICATION->SetPageProperty("NOT_SHOW_NAV_CHAIN", "Y");
$APPLICATION->SetPageProperty("SHOW_TITLE", "Y");
$APPLICATION->SetPageProperty("SHOW_FOOTER_CALLOUT", "N");

?>

<? $APPLICATION->IncludeComponent(
	"citrus:template",
	"page-404",
	array(
		'LINK_1' => SITE_DIR . "predlozhenija/",
		'LINK_1_TITLE' => "Каталог недвижимости",
	),
	$component,
	array("HIDE_ICONS" => "Y")
); ?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>