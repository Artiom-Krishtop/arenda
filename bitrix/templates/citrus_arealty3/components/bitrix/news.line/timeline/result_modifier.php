<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams ���������, ������, ���������. �� ����������� ����������� ���� ����������, �� ��������� ��� ������ ��  � ����� template.php. */
/** @var array $arResult ���������, ������/���������. ����������� ����������� ���� ������ ����������. */
/** @var CBitrixComponentTemplate $this ������� ������ (������, ����������� ������) */

$dbEnums = CIBlockPropertyEnum::GetList(array(), array("CODE" => "badge", "IBLOCK_ID" => $arParams["IBLOCK_ID"]));
$arResult["BADGES"] = array();
while ($enum = $dbEnums->Fetch())
{
	$arResult["BADGES"][$enum["ID"]] = $enum["XML_ID"];
}
