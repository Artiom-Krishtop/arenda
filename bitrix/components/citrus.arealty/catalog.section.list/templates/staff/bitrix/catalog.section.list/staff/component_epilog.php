<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

$APPLICATION->SetPageProperty('citrus.arealty_staff-sections', $arResult['SECTIONS']);
$APPLICATION->SetPageProperty('pageSectionClass', '_compact');
CJSCore::Init(['jquery', 'swiper']);
