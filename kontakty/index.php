<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("description", "Сайт агентства недвижимости. Контакты");
$APPLICATION->SetPageProperty('title', "Контакты");
$APPLICATION->SetTitle("Контактная информация");
?>


<?$APPLICATION->IncludeComponent(
	"citrus:realty.contacts", 
	"offices", 
	array(
		"COMPONENT_TEMPLATE" => "offices",
		"OFFICE" => ""
	),
	false
);?>



<? $APPLICATION->IncludeComponent(
	"citrus.core:include",
	"",
	[
		"AREA_FILE_SHOW" => "sect",
		"AREA_FILE_SUFFIX" => "block_contact_us",
		"AREA_FILE_RECURSIVE" => "Y",
        "h" => "h2.h2",
		"TITLE" => "Написать нам",
		"DESCRIPTION" => "Если у вас появились вопросы, напишите нам!",
		"PAGE_SECTION" => "Y",
		"PADDING" => "Y",
		"BG_COLOR" => "N",
		"CLASS" => 'section--width-mid form_review',
	],
	$component
); ?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>