<?php

use Citrus\Arealty\Helper;

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

/** @var $this CBitrixComponent */

if (!\Bitrix\Main\Loader::includeModule("citrus.arealty"))
{
	return;
}

$iblockId = $arParams["IBLOCK_ID"];
if (!is_numeric($iblockId))
{
	try
	{
		$iblockId = (int)Helper::getIblock($iblockId, isset($_GET['src_site']) ? $_GET['src_site'] : null);
	}
	catch (\Exception $e)
	{

	}
}
else
{
	$iblockId = (int)$iblockId;
}

$arResult['IBLOCK_ID'] = $iblockId;

$ib = CIBlock::GetByID($iblockId)->Fetch();
$arResult['IBLOCK_TYPE'] = $ib['IBLOCK_TYPE_ID'];

$this->IncludeComponentTemplate();
