<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();
/** @var CBitrixComponentTemplate $this */
/** @var array $arParams */
/** @var array $arResult */

use Citrus\Arealty\Yamap,
	Bitrix\Main\Web\Json;

// bitrix:catalog.section неправильно выводит ссылки с SECTION_CODE_PATH (берет для ссылки текущий раздел вместо реального раздела элемента)
// закрываем косяк стандартного компонента — добавим <link rel="canonical"> с правильной ссылкой (нужного раздела)
// https://support.google.com/webmasters/answer/139066?hl=ru
$arResult["CANONICAL"] = false;
$this->__component->setResultCacheKeys(array("CANONICAL", "DEAL_TYPE", "CONTACT"));
if (intval($arResult["ID"]) > 0)
{
	$rsElement = CIBlockElement::GetList(
		Array("SORT" => "ASC"),
		Array("IBLOCK_ID" => $arResult["IBLOCK_ID"], "ID" => $arResult["ID"]),
		$arGroupBy = false,
		$arNavStartParams = false,
		$arSelectFields = Array("ID", "NAME", "DETAIL_PAGE_URL")
	);
	$rsElement->SetUrlTemplates();
	if ($arElement = $rsElement->GetNext())
	{
		$serverName = SITE_SERVER_NAME ? SITE_SERVER_NAME : (COption::GetOptionString('main', 'server_name', $_SERVER['HTTP_HOST']));
		$arResult["CANONICAL"] = $APPLICATION->IsHTTPS() ? 'https://' : 'http://' . $serverName . $arElement["DETAIL_PAGE_URL"];
	}
}

$arResult["CONTACT"] = false;
if ($contact = is_array($arResult["PROPERTIES"]["contact"]) ? $arResult["PROPERTIES"]["contact"]["VALUE"] : false)
{
	$arResult["CONTACT"] = \Citrus\Arealty\Helper::getContactInfo($contact);
}

// если контакт для предложения не указан или не найден, выберем первый контакт из списка, будем использовать его
if (!$arResult["CONTACT"])
{
	$arResult["CONTACT"] = \Citrus\Arealty\Helper::getContactInfo();
}

if (isset($arResult['PROPERTIES']['deal_type']) && $arResult['PROPERTIES']['deal_type']['VALUE'])
{
	$dealType = $arResult['PROPERTIES']['deal_type']['VALUE_ENUM_ID'];
	$arResult['DEAL_TYPE'] = is_array($dealType) ? reset($dealType) : $dealType;
}

$arResult['COORD'] = Yamap::getCoord($arResult);
if ($arResult['COORD']) $arResult['JS_COORD'] = Json::encode($arResult['COORD']);

if ($arResult["PROPERTIES"]["cost"]["VALUE"] && $arParams['CURRENCY']) {
	$arResult["PROPERTIES"]["cost"]["VALUE"] = \Citrus\Arealty\Entity\CurrenciesTable::convertFromBase($arResult["PROPERTIES"]["cost"]["VALUE"], $arParams['CURRENCY']);
	$arResult["PROPERTIES"]["cost"]['CURRENCY'] = \Citrus\Arealty\Entity\CurrenciesTable::getCurrencyData($arParams['CURRENCY']);
}

// clear styles
$reStyles = <<<RESTYLES
{style=[\"\'][^\'\"]+[\'\"]}si
RESTYLES;
$arResult["DETAIL_TEXT"] = preg_replace($reStyles, '', $arResult["DETAIL_TEXT"]);
$arResult["PREVIEW_TEXT"] = preg_replace($reStyles, '', $arResult["PREVIEW_TEXT"]);
// TODO clear other HTML properties

$arResult['ADDRESS'] = \Citrus\Arealty\Object\Address::createFromFields($arResult);
