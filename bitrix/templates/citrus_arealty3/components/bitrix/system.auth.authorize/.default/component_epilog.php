<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

CModule::IncludeModule('citrus.forms');

\Citrus\Arealty\Template\includeFormStyles();
CJSCore::Init(['citrus_form', 'citrus_validator']);

