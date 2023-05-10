<?php

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

/** @var array $arParams Параметры, чтение, изменение. Не затрагивает одноименный член компонента, но изменения тут влияют на $arParams в файле template.php. */
/** @var array $arResult Результат, чтение/изменение. Затрагивает одноименный член класса компонента. */
/** @var CBitrixComponentTemplate $this Текущий шаблон (объект, описывающий шаблон) */

if ($USER->IsAuthorized())
{
	if ($APPLICATION->GetShowIncludeAreas() && \Bitrix\Main\Loader::includeModule("iblock"))
	{
		$arButtons = CIBlock::GetPanelButtons(
			$arParams['IBLOCK_ID'],
			$arResult["ID"],
			0,
			array("SECTION_BUTTONS"=>false, "SESSID"=>false)
		);
		$arResult["PANEL"]["EDIT_LINK"] = $arButtons["edit"]["edit_element"]["ACTION_URL"];

		if ($APPLICATION->GetShowIncludeAreas() && !$this->__component->getParent())
			$this->__component->AddIncludeAreaIcons(CIBlock::GetComponentMenu($APPLICATION->GetPublicShowMode(), $arButtons));
	}
}
