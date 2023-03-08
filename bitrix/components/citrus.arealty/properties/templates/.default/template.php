<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

/** @var \Citrus\Arealty\PropertesComponent $component ������� ��������� ��������� */
/** @var CBitrixComponentTemplate $this ������� ������ (������, ����������� ������) */
/** @var array $arResult ������ ����������� ������ ���������� */
/** @var array $arParams ������ �������� ���������� ����������, ����� �������������� ��� ����� �������� ���������� ��� ������ ������� (��������, ����������� ��������� ����������� ��� ������). */
/** @var string $templateFile ���� � ������� ������������ ����� �����, �������� /bitrix/components/bitrix/iblock.list/templates/.default/template.php) */
/** @var string $templateName ��� ������� ���������� (��������: .d�fault) */
/** @var string $templateFolder ���� � ����� � �������� �� DOCUMENT_ROOT (�������� /bitrix/components/bitrix/iblock.list/templates/.default) */
/** @var array $templateData ������ ��� ������, �������� ��������, ����� ������� ����� �������� ������ �� template.php � ���� component_epilog.php, ������ ��� ������ �������� � ���, �.�. ���� component_epilog.php ����������� �� ������ ���� */
/** @var string $parentTemplateFolder ����� ������������� �������. ��� ����������� �������������� ����������� ��� �������� (��������) ������ ������������ ��� ����������. �� ����� ��������� ��� ������������ ������� ���� ������������ ����� ������� */
/** @var string $componentPath ���� � ����� � ����������� �� DOCUMENT_ROOT (����. /bitrix/components/bitrix/iblock.list) */

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