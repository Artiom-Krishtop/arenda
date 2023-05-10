<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

$APPLICATION->SetTitle("Мои объявления");

global $USER;

if(!$USER->IsAuthorized()){
    LocalRedirect('/auth/');
}

global $arrAnnouncementsFilter;

$userData = CUser::GetById($USER->GetId())->fetch();?>

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
        );?>
        <section class="account-content">
            <section class="account-content__inner account-content__inner-visible">
                <? if(!empty($userData['UF_RENTAL_COMPANY'])){
                    $arrAnnouncementsFilter = array('PROPERTY_RENTAL_COMPANY' => intval($userData['UF_RENTAL_COMPANY']));
                    
                    $APPLICATION->IncludeComponent(
                        "bitrix:catalog.section",
                        "personal-announcements",
                        Array(
                            "ACTION_VARIABLE" => "action",
                            "ADD_PICT_PROP" => "",
                            "ADD_PROPERTIES_TO_BASKET" => "N",
                            "ADD_SECTIONS_CHAIN" => "N",
                            "ADD_TO_BASKET_ACTION" => "",
                            "AJAX_MODE" => "N",
                            "AJAX_OPTION_ADDITIONAL" => "",
                            "AJAX_OPTION_HISTORY" => "N",
                            "AJAX_OPTION_JUMP" => "N",
                            "AJAX_OPTION_STYLE" => "Y",
                            "BACKGROUND_IMAGE" => "",
                            "BASKET_URL" => "",
                            "BRAND_PROPERTY" => "",
                            "BROWSER_TITLE" => "-",
                            "CACHE_FILTER" => "N",
                            "CACHE_GROUPS" => "Y",
                            "CACHE_TIME" => "36000000",
                            "CACHE_TYPE" => "A",
                            "COMPATIBLE_MODE" => "Y",
                            "CONVERT_CURRENCY" => "Y",
                            "CURRENCY_ID" => \Citrus\Arealty\Helper::getSelectedCurrency(),
                            "CUSTOM_FILTER" => "",
                            "DATA_LAYER_NAME" => "",
                            "DETAIL_URL" => "",
                            "DISABLE_INIT_JS_IN_COMPONENT" => "N",
                            "DISCOUNT_PERCENT_POSITION" => "",
                            "DISPLAY_BOTTOM_PAGER" => "N",
                            "DISPLAY_TOP_PAGER" => "N",
                            "ELEMENT_SORT_FIELD" => "DATE_CREATE",
                            "ELEMENT_SORT_FIELD2" => "shows",
                            "ELEMENT_SORT_ORDER" => "DESC",
                            "ELEMENT_SORT_ORDER2" => "desc",
                            "ENLARGE_PRODUCT" => "",
                            "ENLARGE_PROP" => "",
                            "FILTER_NAME" => "arrAnnouncementsFilter",
                            "HIDE_NOT_AVAILABLE" => "N",
                            "HIDE_NOT_AVAILABLE_OFFERS" => "N",
                            "IBLOCK_ID" => "13",
                            "IBLOCK_TYPE" => "realty",
                            "INCLUDE_SUBSECTIONS" => "N",
                            "LABEL_PROP" => array(),
                            "LABEL_PROP_MOBILE" => array(),
                            "LABEL_PROP_POSITION" => "",
                            "LAZY_LOAD" => "N",
                            "LINE_ELEMENT_COUNT" => "1",
                            "LOAD_ON_SCROLL" => "N",
                            "PAGER_BASE_LINK_ENABLE" => "N",
                            "PAGER_DESC_NUMBERING" => "N",
                            "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
                            "PAGER_SHOW_ALL" => "N",
                            "PAGER_SHOW_ALWAYS" => "N",
                            "PAGER_TEMPLATE" => ".default",
                            "PAGER_TITLE" => "Мои объявления",
                            "PAGE_ELEMENT_COUNT" => "100",
                            "PARTIAL_PRODUCT_PROPERTIES" => "N",
                            "PRICE_CODE" => array("BASE"),
                            "PRICE_VAT_INCLUDE" => "Y",
                            "PRODUCT_BLOCKS_ORDER" => "",
                            "PRODUCT_DISPLAY_MODE" => "Y",
                            "PRODUCT_PROPERTIES" => array(),
                            "PRODUCT_PROPS_VARIABLE" => "prop",
                            "PRODUCT_QUANTITY_VARIABLE" => "quantity",
                            "PRODUCT_ROW_VARIANTS" => "",
                            "PRODUCT_SUBSCRIPTION" => "Y",
                            "PROPERTY_CODE" => array(
                                0 => 'district',
                                1 => 'cost',
                                2 => 'rooms_area',
                                3 => 'commercial_features',
                            ),
                            "PROPERTY_CODE_MOBILE" => array(),
                            "RCM_PROD_ID" => $_REQUEST["PRODUCT_ID"],
                            "RCM_TYPE" => "personal",
                            "SECTION_ID" => '',
                            "SECTION_CODE" => 'offers',
                            "SECTION_URL" => '/predlozhenija/#SECTION_CODE_PATH#/',
                            "DETAIL_URL" => '/predlozhenija/#SECTION_CODE_PATH#/#ELEMENT_CODE#/',
                            "SECTION_ID_VARIABLE" => "SECTION_ID",
                            "PRODUCT_ID_VARIABLE" => "ELEMENT_ID",
                            "SEF_MODE" => "Y",
                            "SET_BROWSER_TITLE" => "Y",
                            "SET_LAST_MODIFIED" => "Y",
                            "SET_META_DESCRIPTION" => "N",
                            "SET_META_KEYWORDS" => "N",
                            "SET_STATUS_404" => "Y",
                            "SET_TITLE" => "N",
                            "SHOW_404" => "Y",
                            "SHOW_ALL_WO_SECTION" => "Y",
                            "SHOW_CLOSE_POPUP" => "N",
                            "SHOW_DISCOUNT_PERCENT" => "Y",
                            "SHOW_FROM_SECTION" => "N",
                            "SHOW_MAX_QUANTITY" => "N",
                            "SHOW_OLD_PRICE" => "N",
                            "SHOW_PRICE_COUNT" => "1",
                            "SHOW_SLIDER" => "Y",
                            "SLIDER_INTERVAL" => "3000",
                            "SLIDER_PROGRESS" => "N",
                            "TEMPLATE_THEME" => "",
                            "USE_ENHANCED_ECOMMERCE" => "N",
                            "USE_MAIN_ELEMENT_SECTION" => "N",
                            "USE_PRICE_COUNT" => "N",
                            "USE_PRODUCT_QUANTITY" => "N",
                            "USE_FILTER" => "Y"
                        )
                    );
                }else{?>
                    <p class="account-content__text">
                        Вы не можете добавлять объявления так как у Вас не указано название компании! Обратитесь к администратору сайта.
                    </p>
                <? } ?>
            </section>
        </section>
    </div>
</div>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>