<?php

namespace EventHandlers;

use \Bitrix\Main\Mail\Event;
use CUser;

class UserModeration 
{
    public static function onBeforeUserUpdate(&$arFields)
    {
        $userOldData = CUser::GetByID($arFields['ID'])->fetch();

        if($userOldData['ACTIVE'] != $arFields['ACTIVE'] && $arFields['ACTIVE'] == 'Y'){
            foreach ($arFields['GROUP_ID'] as $group) {               
                if($group['GROUP_ID'] == RENT_USER_GROUP && !empty($arFields['EMAIL'])){
                    $arEventFields = array(
                        "EVENT_NAME" => "SUCCESS_MODERATION_USER",
                        "LID" => $arFields['LID'],
                        "C_FIELDS" => array(
                            "EMAIL" => $arFields["EMAIL"],
                        )
                    );
        
                    Event::send($arEventFields);
                }
            }
        }
    }

    public static function onUserDelete($id)
    {
        $userData = CUser::GetByID($id)->fetch();

        if(!empty($userData['EMAIL']) && $userData['ACTIVE'] == 'N'){
            $arEventFields = array(
                "EVENT_NAME" => "REMOVE_USER",
                "LID" => $userData['LID'],
                "C_FIELDS" => array(
                    "EMAIL" => $userData["EMAIL"],
                )
            );

            Event::send($arEventFields);
        }
    }
}