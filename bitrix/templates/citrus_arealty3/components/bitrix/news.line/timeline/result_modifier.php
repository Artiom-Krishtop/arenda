<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams Параметры, чтение, изменение. Не затрагивает одноименный член компонента, но изменения тут влияют на  в файле template.php. */
/** @var array $arResult Результат, чтение/изменение. Затрагивает одноименный член класса компонента. */
/** @var CBitrixComponentTemplate $this Текущий шаблон (объект, описывающий шаблон) */

$dbEnums = CIBlockPropertyEnum::GetList(array(), array("CODE" => "badge", "IBLOCK_ID" => $arParams["IBLOCK_ID"]));
$arResult["BADGES"] = array();
while ($enum = $dbEnums->Fetch())
{
	$arResult["BADGES"][$enum["ID"]] = $enum["XML_ID"];
}
