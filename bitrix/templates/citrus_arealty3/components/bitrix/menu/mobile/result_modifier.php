<?

/**
 * hide items more then $arParams['MAX_LEVEL']
 * @var array $arResult
 */
foreach ($arResult as $key => &$arItem) {
	if ($arItem['DEPTH_LEVEL'] === $arParams['MAX_LEVEL']) $arItem['IS_PARENT'] = false;
	if ($arItem['DEPTH_LEVEL'] > $arParams['MAX_LEVEL']) unset($arResult[$key]);
}