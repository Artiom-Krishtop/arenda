<?
use Bitrix\Main\Localization\Loc;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$this->setFrameMode(true);

// TODO �������� ���������� ��������� � �������� ����� �� ��������� �������������� �� ������ ����

/** @var CBitrixComponent $component ������� ��������� ��������� */
/** @var CBitrixComponentTemplate $this ������� ������ (������, ����������� ������) */
/** @var array $arResult ������ ����������� ������ ���������� */
/** @var array $arParams ������ �������� ���������� ����������, ����� �������������� ��� ����� �������� ���������� ��� ������ ������� (��������, ����������� ��������� ����������� ��� ������). */
/** @var string $templateFile ���� � ������� ������������ ����� �����, �������� /bitrix/components/bitrix/iblock.list/templates/.default/template.php) */
/** @var string $templateName ��� ������� ���������� (��������: .d�fault) */
/** @var string $templateFolder ���� � ����� � �������� �� DOCUMENT_ROOT (�������� /bitrix/components/bitrix/iblock.list/templates/.default) */
/** @var array $templateData ������ ��� ������, �������� ��������, ����� ������� ����� �������� ������ �� template.php � ���� component_epilog.php, ������ ��� ������ �������� � ���, �.�. ���� component_epilog.php ����������� �� ������ ���� */
/** @var string $parentTemplateFolder ����� ������������� �������. ��� ����������� �������������� ����������� ��� �������� (��������) ������ ������������ ��� ����������. �� ����� ��������� ��� ������������ ������� ���� ������������ ����� ������� */
/** @var string $componentPath ���� � ����� � ����������� �� DOCUMENT_ROOT (����. /bitrix/components/bitrix/iblock.list) */


$jsParams = array(
	"id" => $arResult["MAP_ID"], // id ���������� ��� �����
	"assetsPath" => SITE_TEMPLATE_PATH, // ����� � ���������� ��� �����
	'theme' => \Citrus\Arealty\Helper::getTheme(),
	"items" => array() // ������ �������
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
