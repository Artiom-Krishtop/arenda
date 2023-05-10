<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

/** @var \Citrus\Arealty\PropertesComponent $component Текущий вызванный компонент */
/** @var CBitrixComponentTemplate $this Текущий шаблон (объект, описывающий шаблон) */
/** @var array $arResult Массив результатов работы компонента */
/** @var array $arParams Массив входящих параметров компонента, может использоваться для учета заданных параметров при выводе шаблона (например, отображении детальных изображений или ссылок). */
/** @var string $templateFile Путь к шаблону относительно корня сайта, например /bitrix/components/bitrix/iblock.list/templates/.default/template.php) */
/** @var string $templateName Имя шаблона компонента (например: .dеfault) */
/** @var string $templateFolder Путь к папке с шаблоном от DOCUMENT_ROOT (например /bitrix/components/bitrix/iblock.list/templates/.default) */
/** @var array $templateData Массив для записи, обратите внимание, таким образом можно передать данные из template.php в файл component_epilog.php, причем эти данные попадают в кеш, т.к. файл component_epilog.php исполняется на каждом хите */
/** @var string $parentTemplateFolder Папка родительского шаблона. Для подключения дополнительных изображений или скриптов (ресурсов) удобно использовать эту переменную. Ее нужно вставлять для формирования полного пути относительно папки шаблона */
/** @var string $componentPath Путь к папке с компонентом от DOCUMENT_ROOT (напр. /bitrix/components/bitrix/iblock.list) */

$this->setFrameMode(true);

$properties = $component->getProperties();

?>
<div class="properties <?=$component->getCssClass()?>">
	<?foreach ( $component->getDisplayProperties() as $propertyCode):

		$value = $properties->getValue($propertyCode);
		if (empty($value))
		{
			continue;
		}

		?>
        <div class="property__it<?=($arParams['SHOW_HEADINGS'] == 'Y' ? 'property__it--with-title' : '')?>" data-property-code="<?=$propertyCode?>">
	        <?php
	        if ($arParams['SHOW_HEADINGS'] == 'Y')
	        {
	        	?>
		        <div class="property__title <?=$arParams['HEADINGS_CLASS']?>">
			        <?=$properties->getName($propertyCode)?>
		        </div>
		        <?php
	        }
	        ?>
			<?if($icon = $component->getProperties()->getPropertyIcon($propertyCode)):?>
		        <div class="property__icon">
                    <span class="<?=$icon?>"></span>
	            </div>
			<?endif;?>

            <div class="property__value-list">
				<?
				$desc = $component->getProperties()->getDescription($propertyCode);
				$values = (is_array($value) && $value[0]) ? $value : [$value]
				?>
				<? foreach ($values as $key => $value): ?>
                    <div class="property__value-it">
                        <div class="property__value-it__value"><?=$component->getProperties()->formatValue($propertyCode, $value)?></div>
						<?if($description = $component->getProperties()->getDescription($propertyCode, $key)):?>
                            <div class="property__value-it__description"> - <?=$description;?></div>
						<?endif;?>
                    </div>
				<?
				endforeach;?>
            </div>
        </div>
	<?endforeach;?>
</div><!-- .properties -->