<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main\Localization\Loc;
use Citrus\Forms;
use Bitrix\Main;

Loc::loadMessages(__FILE__);

if (!Main\Loader::includeModule('citrus.forms'))
{
	ShowError(Loc::getMessage("CITRUS_MISSING_MODULE", array('#ID#' => 'citrus.forms')));
	return false;
}

$arAvalibleGroup = array('RECAPTCHA',"BASE","FIELDS","MAIL_SETTINGS","VISUAL");

if($arCurrentValues['USE_MAIN_SETTINGS'] == "Y" || !array_key_exists('USE_MAIN_SETTINGS',$arCurrentValues)) {
	$arCurrentValues['SEND_MESSAGE'] = "N";
	$arAvalibleGroup = array("BASE","FIELDS","VISUAL",'RECAPTCHA');
}

/*
$parenComponentName = 'citrus:smartform.user.registration';
CBitrixComponent::includeComponentClass($parenComponentName);
if(class_exists('CBCitrusUserRegForm')) {
	$obj = new CBCitrusUserRegForm;
	$obj->initComponent($parenComponentName);
	$arCompParam = $obj->GetComponentParametrs($arAvalibleGroup,$arCurrentValues);
	$arCompParam['PARAMETERS']['FIELDS'] = $obj->GetParametrsFieldInput($arCurrentValues, 'citrus:smartform');
}
*/

$componentName = Forms\getComponentName();
CBitrixComponent::includeComponentClass($componentName);
if(class_exists('\Citrus\Forms\UserRegistrationComponent'))
{
	$obj = new Forms\UserRegistrationComponent();
	$obj->initComponent($componentName, $arCurrentValues['COMPONENT_TEMPLATE']);
	$arCompParam = $obj->GetComponentParametrs($arAvalibleGroup, $arCurrentValues);
	$arCompParam['PARAMETERS']['FIELDS'] = [
		"LOGIN" => array(
			"ORIGINAL_TITLE" => "Логин (мин. 3 символа)",
			"TITLE" => "Логин (мин. 3 символа)",
			"IS_REQUIRED" => "Y",
			"HIDE_FIELD" => "N",
			"DEFAULT" => "",
			"TEMPLATE_ID" => ".default",
		),
		"PASSWORD" => array(
			"ORIGINAL_TITLE" => "Пароль",
			"TITLE" => "Пароль",
			"IS_REQUIRED" => "Y",
			"HIDE_FIELD" => "N",
			"TEMPLATE_ID" => ".default",
		),
		"CONFIRM_PASSWORD" => array(
			"ORIGINAL_TITLE" => "Подтверждение пароля",
			"TITLE" => "Подтверждение пароля",
			"IS_REQUIRED" => "Y",
			"HIDE_FIELD" => "N",
			"TEMPLATE_ID" => ".default",
		),
		"EMAIL" => array(
			"ORIGINAL_TITLE" => "Адрес e-mail",
			"TITLE" => "Адрес e-mail",
			"IS_REQUIRED" => "Y",
			"HIDE_FIELD" => "N",
			"TEMPLATE_ID" => ".default",
		),
	];
}

$res = Bitrix\Main\GroupTable::getList(array(
	"filter" => array("ACTIVE" => "Y"),
	"select" => array("NAME","ID")
));
while($arGr = $res->fetch()) {
	$arGroup[$arGr['ID']] = "[{$arGr['ID']}] {$arGr['NAME']}";
}

$arComponentParameters = array(
	"GROUPS" => array(
		"USER" => array(
			"NAME" => Loc::getMessage("PAR_UREGFORM_G_USER"),
			"SORT" => 150
		)
	),
	"PARAMETERS" => array(
		"USE_MAIN_SETTINGS" => array(
			"PARENT" => "USER",
			"NAME" => Loc::getMessage("PAR_UREGFORM_F_USE_MAIN_SETTINGS"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
			"REFRESH" => "Y"
		)
	)
);

if($arCurrentValues['USE_MAIN_SETTINGS'] == "N") {
	$arComponentParameters['PARAMETERS']['ADD_ACTIVE'] = array(
		"PARENT" => "USER",
		"NAME" => Loc::getMessage("PAR_UREGFORM_F_ADD_ACTIVE"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "N",
		"REFRESH" => "N"
	);
	$arComponentParameters['PARAMETERS']['ADD_GROUP'] = array(
		"PARENT" => "USER",
		"NAME" => Loc::getMessage("PAR_UREGFORM_F_ADD_GROUP"),
		"TYPE" => "LIST",
		"DEFAULT" => 2,
		"MULTIPLE" => "Y",
		"VALUES" => $arGroup
	);
}

if($arCurrentValues['USE_MAIN_SETTINGS'] == "Y") {
	$arComponentParameters['PARAMETERS']['USE_MAIN_Y_OKMESSAGE'] = array(
		"PARENT" => "USER",
		"NAME" => Loc::getMessage("PAR_UREGFORM_F_USE_MAIN_Y_OKMESSAGE"),
		"TYPE" => "STRING",
		"DEFAULT" => "",
		"REFRESH" => "N"
	);
}


if(false !== $arCompParam) {
	$arComponentParameters['GROUPS'] = array_merge($arComponentParameters['GROUPS'],$arCompParam['GROUPS']);
	$arComponentParameters['PARAMETERS'] = array_merge($arComponentParameters['PARAMETERS'],$arCompParam['PARAMETERS']);
}
?>
