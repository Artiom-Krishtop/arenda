<?
use Bitrix\Main\Localization\Loc;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$this->setFrameMode(true);

// TODO Добавить сохранение координат в инфоблок чтобы не проводить геокодирование на каждом хите

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


$jsParams = array(
	"id" => $arResult["MAP_ID"], // id контейнера для карты
	"assetsPath" => SITE_TEMPLATE_PATH, // папка с картинками для меток
	'theme' => \Citrus\Arealty\Helper::getTheme(),
	"items" => array() // список адресов
);

$this->SetViewTarget('under-footer');

?>
<div class="citrus-objects-map" id="<?=$arResult["MAP_ID"]?>">
	<?
	foreach ($arResult["ITEMS"] as $key => $arItem)
	{
		$address = \Citrus\Arealty\Object\Address::createFromFields($arItem);
		if ((string)$address)
		{
			ob_start();
			$img = CFile::ResizeImageGet($arItem["DETAIL_PICTURE"]["ID"], Array('width' => 190, 'height' => 170), BX_RESIZE_IMAGE_EXACT, $bInitSizes = true);
			?>
			<a href="<?=$arItem["DETAIL_PAGE_URL"]?>" class="citrus-objects-map-popup" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
				<?
				echo CFile::ShowImage($img['src'], $img['width'], $img['height'], ' class="citrus-objects-map-popup__image"');

				if ($arItem["PROPERTIES"]["cost"]["VALUE"])
				{
					?><span class="citrus-objects-map-popup__price"><?=number_format($arItem["PROPERTIES"]["cost"]["VALUE"], 0, ',', ' ')?>
					<small><i class="icon-ruble"></i></small></span><?
				}
				?>
				<span class="citrus-objects-map-popup__title"><?=$arItem["NAME"]?></span>
				<span class="citrus-objects-map-popup__desc"><?=(string)$address?></span>
			</a>
			<?
			$body = ob_get_contents();
			ob_end_clean();

			$jsParams["items"][$key] = array(
				'name' => $arItem["NAME"],
				'address' => (string)$address,
				'code' => $arItem['CODE'],
				'body' => $body,
				'coord' => $address->getCoordinates(),
			);
		}
	}
	?>
	<script data-src="/bitrix/">
		$().citrusObjectsMap(<?=CUtil::PhpToJSObject($jsParams)?>);
	</script>
</div>
