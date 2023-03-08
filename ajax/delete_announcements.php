<? require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

global $USER;

$response = array();
try
{
	if (!\Bitrix\Main\Loader::includeModule("iblock")){
		throw new \Exception("iblock module not found");
    }

    if(!$USER->IsAuthorized()){
        throw new \Exception("user not authorized");
    }

    if(empty($_REQUEST['ID'])){
        throw new \Exception("parameter ID not found");
    }

    $announcementsID = intval($_REQUEST['ID']);

    $arUser = CUser::GetByID($USER->GetId())->fetch();

    if(!empty($arUser['UF_RENTAL_COMPANY'])){
        $dbRes = CIBlockElement::GetByID($announcementsID);

        if($el = $dbRes->Fetch()){
            $db_props = CIBlockElement::GetProperty($el['IBLOCK_ID'], $el['ID'], array("sort" => "asc"), Array("CODE"=>"RENTAL_COMPANY"));

            if($prop = $db_props->Fetch()){
                if(empty($prop['VALUE']) || $prop['VALUE'] != $arUser['UF_RENTAL_COMPANY']){
                    throw new \Exception("access denied");
                }else {
                    $res = CIBlockElement::Delete($announcementsID);
                    $res = true;
                    if(!$res){
                        throw new \Exception("error delete");
                    }else {
                        $response['status'] = 'ok';
                    }
                }
            }
        }else{
            throw new \Exception("announcements not found");
        }
    }
}
catch (Exception $e)
{
    $response['status'] = 'error';
	$response['error'] = $e->getMessage();
}

$APPLICATION->RestartBuffer();
echo \Bitrix\Main\Web\Json::encode($response);

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");