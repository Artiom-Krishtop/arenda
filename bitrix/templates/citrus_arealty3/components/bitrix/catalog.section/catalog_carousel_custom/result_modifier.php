<?

namespace Citrus\Arealty;

use Citrus\Arealty\Object\Address;

/** @var array $arParams Параметры, чтение, изменение. Не затрагивает одноименный член компонента, но изменения тут влияют на  в файле template.php. */
/** @var array $arResult Результат, чтение/изменение. Затрагивает одноименный член класса компонента. */
/** @var \CBitrixComponentTemplate $this Текущий шаблон (объект, описывающий шаблон) */

$obEnum = new \CUserFieldEnum();
if ($arResult["UF_TYPE"] && ($enum = $obEnum->GetList(array(), array("ID" => $arResult["UF_TYPE"]))->Fetch()))
	$arResult["UF_TYPE_XML_ID"] = $enum["XML_ID"];
else
	$arResult["UF_TYPE_XML_ID"] = false;

// columns
$displayProperties = new DisplayProperties($arResult['IBLOCK_ID']);
foreach ($arResult["ITEMS"] as &$item)
	{
	$item['DISPLAY_COLUMNS'] = $displayProperties->getForElement($item['ID'], $item['DISPLAY_COLUMNS_DEFAULT']);
	$item['ADDRESS'] = Address::createFromFields($item);
}
if (isset($item))
		{
	unset($item);
	}

$arResult['DISPLAY_COLUMNS'] = $displayProperties->getForSection($arResult['ID'], $arResult['DISPLAY_COLUMNS_DEFAULT']);

$arResult["MAP_ID"] = uniqid('citrus-objects-map-');

$iblock = \CIBlock::GetByID($arResult["IBLOCK_ID"])->GetNext();
$arResult['IBLOCK_LIST_LINK'] = str_replace('#SITE_DIR#', SITE_DIR, $iblock['LIST_PAGE_URL']);