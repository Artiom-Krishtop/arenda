<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$arRes = $GLOBALS["USER_FIELD_MANAGER"]->GetUserFields("USER", 0, LANGUAGE_ID);
$userProp = array();
if (!empty($arRes))
{
	foreach ($arRes as $key => $val)
		$userProp[$val["FIELD_NAME"]] = (strLen($val["EDIT_FORM_LABEL"]) > 0 ? $val["EDIT_FORM_LABEL"] : $val["FIELD_NAME"]);
}

$userFields = array(
	"NAME" => GetMessage('PROFILE_NAME'),
	"EMAIL" => GetMessage('PROFILE_EMAIL'),
	"LOGIN" => GetMessage('PROFILE_LOGIN'),
	"PERSONAL_PHONE" => GetMessage('PROFILE_PERSONAL_PHONE'),
	"PERSONAL_BIRTHDAY" => GetMessage('PROFILE_PERSONAL_BIRTHDAY'),
	"WORK_COMPANY" => GetMessage('PROFILE_WORK_COMPANY'),
);

$arComponentParameters = array(
	"PARAMETERS" => array(
		"USER_FIELDS" => array(
			"PARENT" => "ADDITIONAL_SETTINGS",
			"NAME" => GetMessage("USER_FIELDS"),
			"TYPE" => "LIST",
			"VALUES" => $userFields,
			"MULTIPLE" => "Y",
			"DEFAULT" => array(),
		),
		"SET_TITLE" => array(),
		"USER_PROPERTY"=>array(
			"PARENT" => "ADDITIONAL_SETTINGS",
			"NAME" => GetMessage("USER_PROPERTY"),
			"TYPE" => "LIST",
			"VALUES" => $userProp,
			"MULTIPLE" => "Y",
			"DEFAULT" => array(),
		),
		"SEND_INFO"=>array(
			"PARENT" => "ADDITIONAL_SETTINGS",
			"NAME" => GetMessage("SEND_INFO"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
		),
		"CHECK_RIGHTS"=>array(
			"PARENT" => "ADDITIONAL_SETTINGS",
			"NAME" => GetMessage("CHECK_RIGHTS"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
		),
	),
);
?>