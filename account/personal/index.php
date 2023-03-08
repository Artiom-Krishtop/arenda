<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

$APPLICATION->SetTitle("Личные данные");

global $USER;

if(!$USER->IsAuthorized()){
    LocalRedirect('/auth/');
}?>
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
            "itg-soft:main.profile",
            "",
            Array(
                "USER_PROPERTY_NAME" => "",
                "USER_FIELDS" => array(
                    "LOGIN",
                    "NAME",
                    "PERSONAL_PHONE",
                    "EMAIL",
                    "WORK_COMPANY",
                ),
                "SET_TITLE" => "N", 
                "AJAX_MODE" => "N", 
                "USER_PROPERTY" => Array(), 
                "SEND_INFO" => "Y", 
                "CHECK_RIGHTS" => "Y",  
                "AJAX_OPTION_JUMP" => "N", 
                "AJAX_OPTION_STYLE" => "Y", 
                "AJAX_OPTION_HISTORY" => "N" 
            )
        );?>
    
    </div>
</div>
 
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>