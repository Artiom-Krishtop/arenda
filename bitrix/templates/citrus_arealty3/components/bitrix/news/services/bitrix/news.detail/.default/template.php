<?php

use Bitrix\Main\Localization\Loc;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

$this->setFrameMode(true);

/** @var CBitrixComponent $component Текущий вызванный компонент */
/** @var CBitrixComponentTemplate $this Текущий шаблон (объект, описывающий шаблон) */
/** @var array $arResult Массив результатов работы компонента */
/** @var array $arParams Массив входящих параметров компонента, может использоваться для учета заданных параметров при выводе шаблона (например, отображении детальных изображений или ссылок). */
/** @var string $templateFile Путь к шаблону относительно корня сайта, например /bitrix/components/bitrix/iblock.list/templates/.default/template.php) */
/** @var string $templateName Имя шаблона компонента (например: .dеfault) */
/** @var string $templateFolder Путь к папке с шаблоном от DOCUMENT_ROOT (например /bitrix/components/bitrix/iblock.list/templates/.default) */
/** @var array $templateData Массив для записи, обратите внимание, таким образом можно передать данные из template.php в файл component_epilog.php, причем эти данные попадают в кеш, т.к. файл component_epilog.php исполняется на каждом хите */
/** @var string $parentTemplateFolder Папка родительского шаблона. Для подключения дополнительных изображений или скриптов (ресурсов) удобно использовать эту переменную. Ее нужно вставлять для формирования полного пути относительно папки шаблона */
/** @var string $componentPath Путь к папке с компонентом от DOCUMENT_ROOT (напр. /bitrix/components/bitrix/iblock.list) */

?>

<? if ($arParams["DISPLAY_DATE"] != "N" && $arResult["DISPLAY_ACTIVE_FROM"]): ?>
	<span class="b-news-date b-news-detail-date"><?=$arResult["DISPLAY_ACTIVE_FROM"]?></span>
<? endif; ?>

<? if ($arParams["DISPLAY_PICTURE"] != "N" && is_array($arResult["DETAIL_PICTURE"])):
	$arSmallPicture = CFile::ResizeImageGet(
		$arResult["DETAIL_PICTURE"]["ID"],
		array(
			'width' => (int)$arParams['RESIZE_IMAGE_WIDTH'] <= 0 ? 150 : (int)$arParams['RESIZE_IMAGE_WIDTH'],
			'height' => (int)$arParams['RESIZE_IMAGE_HEIGHT'] <= 0 ? 150 : (int)$arParams['RESIZE_IMAGE_HEIGHT'],
		),
		BX_RESIZE_IMAGE_PROPORTIONAL,
		true
	); ?>
	<a rel="news-detail-photo" class="popup" href="<?=$arResult["DETAIL_PICTURE"]["SRC"]?>" title="<?=$arResult["DETAIL_PICTURE"]["DESCRIPTION"]?>"><img class="b-news-preview-picture b-news-detail-preview-picture" border="0" src="<?=$arSmallPicture["src"]?>" width="<?=$arSmallPicture["width"]?>" height="<?=$arSmallPicture["height"]?>" alt="<?=$arResult["NAME"]?>" title="<?=$arResult["DETAIL_PICTURE"]["DESCRIPTION"]?>"/></a>
<? endif ?>
<?php

if ($arParams["MORE_PHOTO"] && array_key_exists($arParams["MORE_PHOTO"], $arResult["PROPERTIES"]))
{
	$arMorePhotos = $arResult["PROPERTIES"][$arParams["MORE_PHOTO"]]["VALUE"];
	$arDescription = $arResult["PROPERTIES"][$arParams["MORE_PHOTO"]]["DESCRIPTION"];
	if (!is_array($arMorePhotos))
	{
		$arMorePhotos = Array();
	}
	if (count($arMorePhotos) > 0)
	{
		?>
		<div class="b-news-detail-photos"><?php

		foreach ($arMorePhotos as $idx => $photoID)
		{
			$arFile = CFile::GetFileArray($photoID);
			if (!is_array($arFile) || strlen($arFile["SRC"]) <= 0)
			{
				continue;
			}
			$arSmallPicture = CFile::ResizeImageGet(
				$photoID,
				array(
					'width' => (int)$arParams['RESIZE_IMAGE_WIDTH'] <= 0 ? 150 : (int)$arParams['RESIZE_IMAGE_WIDTH'],
					'height' => (int)$arParams['RESIZE_IMAGE_HEIGHT'] <= 0 ? 150 : (int)$arParams['RESIZE_IMAGE_HEIGHT'],
				),
				BX_RESIZE_IMAGE_PROPORTIONAL,
				true
			); ?>
			<a rel="news-detail-photo" class="popup" href="<?=$arFile["SRC"]?>" title="<?=$arDescription[$idx]?>"><img class="b-news-preview-picture b-news-detail-preview-picture" border="0" src="<?=$arSmallPicture["src"]?>" width="<?=$arSmallPicture["width"]?>" height="<?=$arSmallPicture["height"]?>" alt="<?=$arDescription[$idx]?>" title="<?=$arDescription[$idx]?>"/></a>
			<?php

		}
		?></div><?php
	}
}

$replaceSiteDir = function ($text) {
    return str_replace('#SITE_DIR#', SITE_DIR, $text);
};
?>

<div class="b-news-text b-news-detail-text">
	<? if ($arParams["DISPLAY_PREVIEW_TEXT"] != "N" && $arResult["FIELDS"]["PREVIEW_TEXT"]): ?>
		<blockquote><?=$replaceSiteDir($arResult["FIELDS"]["PREVIEW_TEXT"]);
			unset($arResult["FIELDS"]["PREVIEW_TEXT"]);?></blockquote>
	<? endif; ?>

	<? if ($arResult["NAV_RESULT"]): ?>
		<? if ($arParams["DISPLAY_TOP_PAGER"]): ?><?=$arResult["NAV_STRING"]?><br/><? endif; ?>
		<?=$replaceSiteDir($arResult["NAV_TEXT"])?>
		<? if ($arParams["DISPLAY_BOTTOM_PAGER"]): ?><br/><?=$arResult["NAV_STRING"]?><? endif; ?>
	<? elseif (strlen($arResult["DETAIL_TEXT"]) > 0): ?>
		<?=$replaceSiteDir($arResult["DETAIL_TEXT"])?>
	<? else: ?>
		<?=$replaceSiteDir($arResult["PREVIEW_TEXT"])?>
	<? endif ?>

<?

$el = function (...$attrs) {
    return \Spatie\HtmlElement\HtmlElement::render(...func_get_args());
};

$mortgageDir = $_SERVER['DOCUMENT_ROOT'] . SITE_DIR . 'ipoteka';
$searchableContent = ToUpper($arResult['NAME'] . '|' . $arResult['PREVIEW_TEXT'] . '|' . $arResult['DETAIL_TEXT']);
$hasMortgage = strpos($searchableContent, Loc::getMessage('CITRUS_TEMPLATE_MORTGAGE_KEYWORD')) !== false  && file_exists($mortgageDir) && is_dir($mortgageDir);

echo $el('div.btn-row.btn-row--sm-center.btn-row--xs-center', [

    $el('a.btn.btn-primary.btn-md-if', [
        'href' => SITE_DIR . 'ajax/request.php',
        'rel' => 'nofollow',
        'data-toggle' => 'modal',
    ], Loc::getMessage('CITRUS_TEMPLATE_LEAVE_REQUEST')),

    $hasMortgage
        ? $el('a.btn.btn-secondary.btn-md-if', [
        'href' => SITE_DIR . 'ipoteka/',
    ], Loc::getMessage('CITRUS_TEMPLATE_MORTGAGE_CALC'))
        : '',

]);

?>
<p><br>
    <a href="<?=$arResult["LIST_PAGE_URL"]?>" class="all-items-link">
        <span class="fa fa-long-arrow-left"></span>
        <span class=""><?=GetMessage("T_NEWS_DETAIL_BACK1")?> <?=ToLower($arParams["PAGER_TITLE"])?></span>
    </a>
</p>

</div>

<? foreach ($arResult["FIELDS"] as $code => $value): ?>
	<?=GetMessage("IBLOCK_FIELD_" . $code)?>:&nbsp;<?=$value;?>
	<br/>
<? endforeach; ?>
<?

$displayProperties = array_diff_key($arResult["DISPLAY_PROPERTIES"], [$arParams["MORE_PHOTO"] => 1, 'contact' => 1]);
if (count($displayProperties) > 0)
{
	?><?$APPLICATION->IncludeComponent(
		'citrus.arealty:properties',
		'',
		[
			'PROPERTIES' => $arResult['DISPLAY_PROPERTIES'],
			'DISPLAY_PROPERTIES' => array_keys($displayProperties),
			'CSS_CLASS' => 'services-detail__properties'
		],
		$component,
		['HIDE_ICONS' => 'Y']
	);?><?
}
