<?php

use Bitrix\Main\Localization\Loc;

\CJSCore::Init('swiper', 'jquery');

if ($component->getParent() instanceof \Citrus\Core\IncludeComponent)
{
	Loc::loadMessages(__FILE__);

	if (empty($arParams['TITLE']) && !empty($arResult['SECTION']['PATH'][0]['NAME']))
	{
		$component->getParent()->arParams['TITLE'] = $arResult['SECTION']['PATH'][0]['NAME'];
	}
	if (empty($arParams['FOOTER_CONTENT']))
	{
		$url = CIBlock::GetArrayByID($component->arResult['ID'], 'LIST_PAGE_URL');
		$url = str_replace('//', '/', CComponentEngine::makePathFromTemplate($url));

		$blockFooter = \Spatie\HtmlElement\HtmlElement::render('a', [
			'href' => $url,
			'class' => 'btn btn-secondary',
		], Loc::getMessage("CITRUS_AREALTY3_STAFF_INDEX"));

		$component->getParent()->arParams['FOOTER_CONTENT'] = $blockFooter;
	}
}
