<?php

namespace EventHandlers;

use \Bitrix\Main\Mail\Event;
use CUser;

class UserAuthEventHandler 
{
    public static function onAfterUserLogin(&$arParams)
    {       
        if($arParams['USER_ID'] == 0 && !empty($arParams['LOGIN'])){
            $dbUser = CUser::GetByLogin(trim($arParams['LOGIN']));

            if($user = $dbUser->Fetch()){
                if($user['ACTIVE'] == 'N'){
                    $arParams['RESULT_MESSAGE']['MESSAGE'] = 'Пользователь заблокирован';

                    return $arParams;
                }

                if(
                    !isset($_SESSION['SESS_AUTH']['ALLOW_AUTH']) || 
                    (!empty($_SESSION['SESS_AUTH']['ALLOW_AUTH']) && 
                    $_SESSION['SESS_AUTH']['ALLOW_AUTH']['LOGIN'] != $user['LOGIN'])
                ){
                    $_SESSION['SESS_AUTH']['ALLOW_AUTH'] = array(
                        'LOGIN' => $user['LOGIN'],
                        'COUNT_POSSIBLE_LOGIN' => 4,
                    );
                }else {
                    $_SESSION['SESS_AUTH']['ALLOW_AUTH']['COUNT_POSSIBLE_LOGIN']--;
                }
    
                if($_SESSION['SESS_AUTH']['ALLOW_AUTH']['COUNT_POSSIBLE_LOGIN'] == 0){
                    $obUser = new CUser;
                    $obUser->Update($user['ID'], array('ACTIVE' => 'N'));

                    unset($_SESSION['SESS_AUTH']['ALLOW_AUTH']);

                    $arParams['RESULT_MESSAGE']['MESSAGE'] = 'Пользователь заблокирован';

                    return $arParams;
                }

                $arParams['RESULT_MESSAGE']['MESSAGE'] = 'Неверный логин или пароль. Осталось попыток: ' . $_SESSION['SESS_AUTH']['ALLOW_AUTH']['COUNT_POSSIBLE_LOGIN'];

                return $arParams;
            }
        }else{
            unset($_SESSION['SESS_AUTH']['ALLOW_AUTH']);
        }
    }
}