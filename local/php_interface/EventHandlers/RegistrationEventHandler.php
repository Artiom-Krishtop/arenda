<?php

namespace EventHandlers;

use \Bitrix\Main\Mail\Event;

class RegistrationEventHandler 
{
    public static $errors = array();

    public static function onBeforeUserRegister (&$arFields)
    {
        if($arFields['ACTIVE'] == 'Y'){
            $arFields['ACTIVE'] = 'N';
        }

        self::checkUserName($arFields);
        self::checkUserPhone($arFields);
        self::checkUserCompany($arFields);
        self::checkUserConcent($arFields);

        if(!empty(self::$errors)){
            self::setException();

            return false;
        }
    }   

    public static function onAfterUserRegister (&$arFields)
    {
        if($arFields["USER_ID"] > 0)
        {
            $arEventFields = array(
                "EVENT_NAME" => "NEW_USER_FOR_ADMIN",
                "LID" => $arFields['LID'],
                "C_FIELDS" => array(
                    "USER_ID" => $arFields["USER_ID"],
                    "EMAIL" => $arFields["EMAIL"],
                    "LOGIN" => $arFields["LOGIN"],
                    "NAME" => $arFields["NAME"],
                    "COMPANY" => $arFields["WORK_COMPANY"],
                    "LANG" => LANGUAGE_ID,
                )
            );

            Event::send($arEventFields);
        }

        return $arFields;
    }

    public static function checkUserName(&$arFields)
    {
        if(strlen($arFields['NAME']) < 2){
            self::$errors[] = 'Имя должно содержать более 2 символов';
        }
    }

    public static function checkUserPhone(&$arFields)
    {
        if(!empty($_REQUEST['USER_PHONE'])){
            $userPhone = preg_replace('/\D+/', '', $_REQUEST['USER_PHONE']);
        
            if(strlen($userPhone) < 7 || strlen($userPhone) > 12){
                self::$errors[] = 'Некорректный номер телефона';
            }else {
                $arFields['PERSONAL_PHONE'] = $userPhone;
            }
        }else {
            self::$errors[] = 'Номер телефона должен быть заполнен';
        }
    }

    public static function checkUserCompany(&$arFields)
    {
        if(!empty($_REQUEST['USER_COMPANY'])){
            $arFields['WORK_COMPANY'] = htmlspecialchars($_REQUEST['USER_COMPANY']);
        }else{
            self::$errors[] = 'Название компании должно быть заполнено';
        }
    }

    public static function checkUserConcent(&$arFields)
    {
        if(!empty($_REQUEST['USER_CONCENT']) && trim($_REQUEST['USER_CONCENT']) == 'Y'){
            $arFields['UF_CONCENT'] = 'Y';
        }else {
            self::$errors[] = 'Отсутствует согласие на обработку персональных данных';
        }
    } 

    public static function setException()
    {
        global $APPLICATION;

        foreach (self::$errors as $erorr) {
            $APPLICATION->ThrowException($erorr);    
        }
    }
}

        
