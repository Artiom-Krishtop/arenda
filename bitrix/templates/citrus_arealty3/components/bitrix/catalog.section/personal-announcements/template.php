<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use \Bitrix\Main\Localization\Loc;
use Bitrix\Main\Page\Asset;

/**
 * @global CMain $APPLICATION
 * @var array $arParams
 * @var array $arResult
 * @var CatalogSectionComponent $component
 * @var CBitrixComponentTemplate $this
 * @var string $templateName
 * @var string $componentPath
 */

$this->setFrameMode(true);

$elementEdit = CIBlock::GetArrayByID($arParams['IBLOCK_ID'], 'ELEMENT_EDIT');
$elementDelete = CIBlock::GetArrayByID($arParams['IBLOCK_ID'], 'ELEMENT_DELETE');
$elementDeleteParams = array('CONFIRM' => GetMessage('CT_BCS_TPL_ELEMENT_DELETE_CONFIRM'));
?>

<?
	$APPLICATION->IncludeComponent(
		"bitrix:breadcrumb",
		"account",
		Array(
			"START_FROM" => "0", 
			"PATH" => "", 
			"SITE_ID" => SITE_ID 
		)
	);
?>

<div class="account-content__my">
	<h1 class="account-content__title"><?= GetMessage('PA_TITLE') ?></h1>
	<a class="account-content__my-btn account-content__btn" href="/account/announcements/action/"><?= GetMessage('PA_ADD_ANNOUNCEMETS')?></a>
</div>

<? if (!empty($arResult['ITEMS'])): ?>
	<div class="slider">
		<? $areaIds = array();

		foreach ($arResult['ITEMS'] as $item)
		{
			$uniqueId = $item['ID'].'_'.md5($this->randString().$component->getAction());
			$areaIds[$item['ID']] = $this->GetEditAreaId($uniqueId);
			$this->AddEditAction($uniqueId, $item['EDIT_LINK'], $elementEdit);
			$this->AddDeleteAction($uniqueId, $item['DELETE_LINK'], $elementDelete, $elementDeleteParams);

			if(!empty($item['PROPERTIES']['photo']['VALUE'])){
				foreach ($item['PROPERTIES']['photo']['VALUE'] as $key => $photoID) {
					$photoData = CFile::GetFileArray($photoID);
	
					if(stripos($photoData['CONTENT_TYPE'], 'video') !== false) {
						unset($item['PROPERTIES']['photo']['VALUE'][$key]);
					}
				}
			}

			if(empty($item['PREVIEW_PICTURE'] && empty($item['DETAIL_PICTURE']) && !empty($item['PROPERTIES']['photo']['VALUE']))){
				$photoID = array_shift($item['PROPERTIES']['photo']['VALUE']);
				$photoData = CFile::GetFileArray($photoID);
		
				$item['PREVIEW_PICTURE'] = $photoData;
				$item['DETAIL_PICTURE'] = $photoData;
			}
			
			$APPLICATION->IncludeComponent(
				'bitrix:catalog.item',
				'personal-announcements',
				array(
					'RESULT' => array(
						'ITEM' => $item,
						'AREA_ID' => $areaIds[$item['ID']],
						'TYPE' => '',
						'BIG_LABEL' => 'N',
						'BIG_DISCOUNT_PERCENT' => 'N',
						'BIG_BUTTONS' => 'N',
						'SCALABLE' => 'N'
					),
					'PARAMS' => array('SKU_PROPS' => $arResult['SKU_PROPS'][$item['IBLOCK_ID']])
				),
				$component,
				array('HIDE_ICONS' => 'Y')
			);
		}?>
	</div>
<? else: ?>
	<p class="account-content__text">
		У вас пока нет объявлений!
	</p>
<? endif; ?>

