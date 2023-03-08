<?
/**
 * @global CMain $APPLICATION
 * @global CUser $USER
 * @global CUserTypeManager $USER_FIELD_MANAGER
 * @var array $arParams
 * @var CBitrixComponent $this
 */

use Bitrix\Main\Loader;

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

$this->setFrameMode(false);

global $USER_FIELD_MANAGER;

$arResult["ID"] = intval($USER->GetID());
$arResult["GROUP_POLICY"] = CUser::GetGroupPolicy($arResult["ID"]);

$arParams['SEND_INFO'] = $arParams['SEND_INFO'] == 'Y' ? 'Y' : 'N';
$arParams['CHECK_RIGHTS'] = $arParams['CHECK_RIGHTS'] == 'Y' ? 'Y' : 'N';

$arParams['EDITABLE_EXTERNAL_AUTH_ID'] = isset($arParams['EDITABLE_EXTERNAL_AUTH_ID']) && is_array($arParams['EDITABLE_EXTERNAL_AUTH_ID'])
	? $arParams['EDITABLE_EXTERNAL_AUTH_ID']
	: [];

if(!($arParams['CHECK_RIGHTS'] == 'N' || $USER->CanDoOperation('edit_own_profile')) || $arResult["ID"]<=0)
{
	$APPLICATION->ShowAuthForm("");
	return;
}

$strError = '';

if($_SERVER["REQUEST_METHOD"]=="POST" && ($_REQUEST["save"] <> '' || $_REQUEST["apply"] <> '') && check_bitrix_sessid())
{
	if(COption::GetOptionString('main', 'use_encrypted_auth', 'N') == 'Y')
	{
		//possible encrypted user password
		$sec = new CRsaSecurity();
		if(($arKeys = $sec->LoadKeys()))
		{
			$sec->SetKeys($arKeys);
			$errno = $sec->AcceptFromForm(array('NEW_PASSWORD', 'NEW_PASSWORD_CONFIRM'));
			if($errno == CRsaSecurity::ERROR_SESS_CHECK)
				$strError .= GetMessage("main_profile_sess_expired").'<br />';
			elseif($errno < 0)
				$strError .= GetMessage("main_profile_decode_err", array("#ERRCODE#"=>$errno)).'<br />';
		}
	}

	if($strError == '')
	{
		$bOk = false;
		$obUser = new CUser;

		$rsUser = CUser::GetByID($arResult["ID"]);
		$arUser = $rsUser->Fetch();

		$arEditFields = $arParams["USER_FIELDS"];

		$arFields = array();
		foreach($arEditFields as $field)
		{
			if(isset($_REQUEST[$field]))
			{
				$arFields[$field] = $_REQUEST[$field];
			}
		}

		$arResult['CAN_EDIT_PASSWORD'] = $arUser['EXTERNAL_AUTH_ID'] == ''
			|| in_array($arUser['EXTERNAL_AUTH_ID'], $arParams['EDITABLE_EXTERNAL_AUTH_ID'], true);

		if($_REQUEST["NEW_PASSWORD"] <> '' && $arResult['CAN_EDIT_PASSWORD'])
		{
			$arFields["PASSWORD"] = $_REQUEST["NEW_PASSWORD"];
			$arFields["CONFIRM_PASSWORD"] = $_REQUEST["NEW_PASSWORD_CONFIRM"];
		}

		if($arUser)
		{
			if($arUser['EXTERNAL_AUTH_ID'] <> '')
			{
				$arFields['EXTERNAL_AUTH_ID'] = $arUser['EXTERNAL_AUTH_ID'];
			}
		}

		$USER_FIELD_MANAGER->EditFormAddFields("USER", $arFields);
	
		if(!$obUser->Update($arResult["ID"], $arFields))
			$strError .= $obUser->LAST_ERROR;

		if(!empty($_REQUEST['WORK_COMPANY']) && !empty($arUser['UF_RENTAL_COMPANY'] && Loader::includeModule('iblock'))){
			$el = new CIBlockElement();
			$res = $el->Update(intval($arUser['UF_RENTAL_COMPANY']), array('NAME' => trim($_REQUEST['WORK_COMPANY'])));

			if(!$res){
				$strError = $el->LAST_ERROR;
			}	
		}
	}

	if($strError == '')
	{
		if($arParams['SEND_INFO'] == 'Y')
			$obUser->SendUserInfo($arResult["ID"], SITE_ID, GetMessage("main_profile_update"), true);

		$bOk = true;
	}
}

$rsUser = CUser::GetList($by="id", $orser="asc", ['ID' => $arResult["ID"]], ['FIELDS' => $arParams["USER_FIELDS"]]);

if(!$arResult["arUser"] = $rsUser->GetNext(false))
{
	$arResult["ID"] = 0;
}

if($strError <> '')
{
	static $skip = array("PERSONAL_PHOTO"=>1, "WORK_LOGO"=>1, "forum_AVATAR"=>1, "blog_AVATAR"=>1);
	foreach($_POST as $k => $val)
	{
		if(!isset($skip[$k]))
		{
			if(!is_array($val))
			{
				$val = htmlspecialcharsex($val);
			}
			if(strpos($k, "forum_") === 0)
			{
				$arResult["arForumUser"][substr($k, 6)] = $val;
			}
			elseif(strpos($k, "blog_") === 0)
			{
				$arResult["arBlogUser"][substr($k, 5)] = $val;
			}
			elseif(strpos($k, "student_") === 0)
			{
				$arResult["arStudent"][substr($k, 8)] = $val;
			}
			else
			{
				$arResult["arUser"][$k] = $val;
			}
		}
	}
}

$arResult["FORM_TARGET"] = $APPLICATION->GetCurPage();

$arResult["IS_ADMIN"] = $USER->IsAdmin();
$arResult['CAN_EDIT_PASSWORD'] = $arUser['EXTERNAL_AUTH_ID'] == ''
	|| in_array($arUser['EXTERNAL_AUTH_ID'], $arParams['EDITABLE_EXTERNAL_AUTH_ID'], true);

$arResult["strProfileError"] = $strError;
$arResult["BX_SESSION_CHECK"] = bitrix_sessid_post();

$arResult["DATE_FORMAT"] = CLang::GetDateFormat("SHORT");

$arResult["COOKIE_PREFIX"] = COption::GetOptionString("main", "cookie_name", "BITRIX_SM");
if (strlen($arResult["COOKIE_PREFIX"]) <= 0) 
	$arResult["COOKIE_PREFIX"] = "BX";

if($arParams["SET_TITLE"] == "Y")
	$APPLICATION->SetTitle(GetMessage("PROFILE_DEFAULT_TITLE"));

if($bOk) 
	$arResult['DATA_SAVED'] = 'Y';

$arResult["EMAIL_REQUIRED"] = (COption::GetOptionString("main", "new_user_email_required", "Y") <> "N");

//secure authorization
$arResult["SECURE_AUTH"] = false;
if(!CMain::IsHTTPS() && COption::GetOptionString('main', 'use_encrypted_auth', 'N') == 'Y')
{
	$sec = new CRsaSecurity();
	if(($arKeys = $sec->LoadKeys()))
	{
		$sec->SetKeys($arKeys);
		$sec->AddToForm('form1', array('NEW_PASSWORD', 'NEW_PASSWORD_CONFIRM'));
		$arResult["SECURE_AUTH"] = true;
	}
}

$this->IncludeComponentTemplate();
