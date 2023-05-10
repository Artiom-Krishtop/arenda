<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);


ob_start();

?>

<div class="manager-row">
<div class="manager-left print-no-break">
    <?
    if ($arParams['DISPLAY_PICTURE'] !== 'N')
    {
        ?>
        <div class="manager-img-container">
            <img class="manager-img"
                 src="<?=$arResult['SMALL_PICTURE']['src']?>"
                 alt="<?=$arResult['NAME']?>" title="<?=$arResult['NAME']?>">
        </div>
        <?php
    }
    ?>
    <div class="manager__content">
        <div class="manager__properties">
            <?
            $APPLICATION->IncludeComponent(
                'citrus.arealty:properties',
                '',
                [
                    'PROPERTIES' => $arResult['PROPERTIES'],
                    'DISPLAY_PROPERTIES' => array_diff($arParams["PROPERTY_CODE"] ?: ['phone', 'email', 'schedule'],
                        ['position']),
                ],
                $component,
                ['HIDE_ICONS' => 'Y']
            );
            ?>

            <? if ($arParams["DISPLAY_PREVIEW_TEXT"] != "N" && $arResult["PREVIEW_TEXT"]): ?>
                <div class="succinctly_manager">
                    <div class="about_title"><?=Loc::getMessage("CITRUS_AREALTY_STAFF_ABOUT")?></div>
                    <div class="about_text">
                        <?=$arResult["PREVIEW_TEXT"]?>
                    </div>
                </div>
            <? endif; ?>
        </div>
    </div>
</div>
<div class="manager-right print-hidden">
    <div class="section-footer display-lg-n ta-xs-c">
        <a href="javascript:void(0);" data-href="<?=SITE_DIR?>ajax/order_call.php"
           data-toggle="modal"
           class="btn btn-primary btn-stretch"><?=Loc::getMessage("CITRUS_AREALTY_STAFF_DETAIL_ORDER_CALL")?></a>
    </div>
    <div class="display-xs-n display-lg-b">
        <div class="h4">
            <?=Loc::getMessage("CITRUS_AREALTY_STAFF_DETAIL_ORDER_CALL")?>
        </div>
	    <? $APPLICATION->IncludeComponent(
		    "citrus.core:include",
		    ".default",
		    array(
			    "AREA_FILE_SHOW" => "sect",
			    "AREA_FILE_SUFFIX" => "order_call",
			    "AREA_FILE_RECURSIVE" => "Y",
			    "PAGE_SECTION" => "N",
		    ),
		    $component
	    ); ?>
    </div>
</div>

<? $APPLICATION->IncludeComponent(
	"citrus.core:include",
	"",
	[
		"AREA_FILE_SHOW" => "HTML",
		"HTML" => ob_get_clean(),
		"h" => "h1",
		"TITLE" => $arParams["DISPLAY_NAME"] != "N" && $arResult["NAME"] ? $arResult['NAME'] : '',
		"DESCRIPTION" => \Citrus\Core\array_get($arResult, 'PROPERTIES.position.VALUE', ''),
		"PAGE_SECTION" => "Y",
		"PADDING" => "Y",
		"BG_COLOR" => "N",
		"CLASS" => "about_manager",
	],
	$component,
	['HIDE_ICONS' => 'Y']
); ?><?php
