<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

/** @var $this CBitrixComponent */

if ($this->startResultCache(3600000, array($arParams["IBLOCK_ID"])))
{
	if (!\Bitrix\Main\Loader::includeModule("iblock"))
	{
		$this->abortResultCache();
		return;
	}
	if (!\Bitrix\Main\Loader::includeModule("citrus.arealty"))
	{
		$this->abortResultCache();
		return;
	}

	if (is_numeric($arParams["IBLOCK_ID"]))
	{
		$rsIBlock = CIBlock::GetList(array(), array(
			"ACTIVE" => "Y",
			"ID" => $arParams["IBLOCK_ID"],
		));
	} else
	{
		$rsIBlock = CIBlock::GetList(array(), array(
			"ACTIVE" => "Y",
			"CODE" => $arParams["IBLOCK_ID"],
			"SITE_ID" => SITE_ID,
		));
	}
	if ($arResult = $rsIBlock->GetNext())
	{
		$sections = CIBlockSection::GetList(
			["LEFT_MARGIN" => "ASC"],
			[
				"IBLOCK_ID" => $arResult["ID"],
				"ACTIVE" => "Y",
				"GLOBAL_ACTIVE" => "Y",
				"<=DEPTH_LEVEL" => $arParams['DEPTH_LEVEL']
			]
		);

		$aNewMenuLinks = $elements = [];
		$prev_level = 1;

		$rsElement = CIBlockElement::GetList(
			Array("SORT" => "ASC"),
			Array("IBLOCK_ID" => $arResult["ID"], "ACTIVE" => "Y"),
			$arGroupBy = false,
			$arNavStartParams = false,
			$arSelectFields = Array("ID", "NAME", "DETAIL_PAGE_URL", "PROPERTY_title", "SECTION_ID")
		);
		$rsElement->SetUrlTemplates();
		while ($arElement = $rsElement->GetNext())
		{
			$elementSectionsIterator = CIBlockElement::GetElementGroups($arElement['ID'], true, array('ID'));
			while ($elementSection = $elementSectionsIterator->Fetch())
			{
				$elements[$elementSection['ID']][] = $arElement;
			}
		}

		while ($section = $sections->GetNext())
		{
			if ($prev_level < $section['DEPTH_LEVEL'])
			{
				$aNewMenuLinks[count($aNewMenuLinks) - 1][3]['IS_PARENT'] = true;
			}
			$prev_level = $section['DEPTH_LEVEL'];

			$aNewMenuLinks[] = [
				$section['NAME'],
				$section['SECTION_PAGE_URL'],
				[],
				[
					"FROM_IBLOCK" => true,
					"IS_PARENT" => false,
					"DEPTH_LEVEL" => $section['DEPTH_LEVEL'],
					"LEVEL" => $section['DEPTH_LEVEL']
				]
			];

			if (is_array($elements[$section['ID']])
				&& count($elements[$section['ID']]))
			{
				$aNewMenuLinks[count($aNewMenuLinks) - 1][3]['IS_PARENT'] = true;
				foreach ($elements[$section['ID']] as $element)
				{
					$aNewMenuLinks[] = [
						$element["PROPERTY_TITLE_VALUE"] ?: $element["NAME"],
						$element["DETAIL_PAGE_URL"],
						[],
						[
							"FROM_IBLOCK" => true,
							"IS_PARENT" => false,
							"DEPTH_LEVEL" => $section['DEPTH_LEVEL'] + 1,
							"LEVEL" => $section['DEPTH_LEVEL'] + 1
						]
					];
				}
			}
		}
		$arResult["LINKS"] = $aNewMenuLinks;

		$this->endResultCache();
	}
	else
	{
		$this->abortResultCache();
		ShowError("Iblock {$arParams['IBLOCK_ID']} not found!");
		@define("ERROR_404", "Y");
		CHTTP::SetStatus("404 Not Found");
	}
}

return is_array($arResult["LINKS"]) ? $arResult["LINKS"] : [];
