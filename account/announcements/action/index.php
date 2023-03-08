<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

global $USER;

if(!$USER->IsAuthorized()){
    LocalRedirect('/auth/');
}

$title = 'Новое объявление';

if(!empty($_REQUEST['CODE'])){
    $title = 'Редактирование объявления';
}

$APPLICATION->SetTitle($title);
?>

<div class="container-account">
    <div class="account">
        <? $APPLICATION->IncludeComponent(
            "bitrix:main.include",
            "",
            Array(
                "AREA_FILE_SHOW" => "sect", 
                "AREA_FILE_SUFFIX" => "inc_menu", 
                "AREA_FILE_RECURSIVE" => "Y", 
                "EDIT_TEMPLATE" => "standard.php" 
            )
        );

        $APPLICATION->IncludeComponent(
            "itg-soft:announcements.add.form",
            "personal",
            Array(
                "SEF_MODE" => "Y",
                "IBLOCK_TYPE" => "realty",
                "IBLOCK_ID" => "13",
                "PROPERTY_CODES" => array(),
                "PROPERTY_CODES_REQUIRED" => array(
                    'NAME'
                ),
                "GROUPS" => array("1", "5"),
                "STATUS_NEW" => "N",
                "STATUS" => array(""),
                "LIST_URL" => "",
                "ELEMENT_ASSOC" => "PROPERTY_ID",
                "ELEMENT_ASSOC_PROPERTY" => "RENTAL_COMPANY",
                "MAX_USER_ENTRIES" => "100000",
                "MAX_LEVELS" => "100000",
                "LEVEL_LAST" => "Y",
                "USE_CAPTCHA" => "N",
                "USER_MESSAGE_EDIT" => "Изменения успешно сохранены",
                "USER_MESSAGE_ADD" => "Объявление успешно добавлено",
                "DEFAULT_INPUT_SIZE" => "30",
                "RESIZE_IMAGES" => "Y",
                "MAX_FILE_SIZE" => "0",
                "PREVIEW_TEXT_USE_HTML_EDITOR" => "Y",
                "DETAIL_TEXT_USE_HTML_EDITOR" => "Y",
                "SEF_FOLDER" => "/",
                "VARIABLE_ALIASES" => Array(
                ),
                "ORGANIZATION_PROPS" => 'UF_RENTAL_COMPANY',
            )
        );
?>
    </div>
</div>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>