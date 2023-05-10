<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$this->__component->setResultCacheKeys(['SECTION', 'SECTIONS_COUNT', 'SECTIONS', 'SHOW_INCLUDE_AREAS']);

$boolClear = false;
$arNewSections = array();
foreach ($arResult['SECTIONS'] ?: [] as &$arOneSection)
{
	if (1 < $arOneSection['RELATIVE_DEPTH_LEVEL'])
	{
		$boolClear = true;
		continue;
	}
	$arNewSections[] = $arOneSection;
}
unset($arOneSection);
if ($boolClear)
{
	$arResult['SECTIONS'] = $arNewSections;
	$arResult['SECTIONS_COUNT'] = count($arNewSections);
}

$arResult['SHOW_INCLUDE_AREAS'] = $APPLICATION->GetShowIncludeAreas();
if (!$APPLICATION->GetShowIncludeAreas() && $arParams['COUNT_ELEMENTS'])
{
	foreach ($arResult['SECTIONS'] as $i => $section)
	{
		if (empty($section['ELEMENT_CNT']) && $section['CODE'] !== $arParams['CURRENT_SECTION_CODE'])
		{
			unset($arResult['SECTIONS'][$i]);
		}
	}
	$arResult['SECTIONS_COUNT'] = count($arResult['SECTIONS']);
}
