<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$arResult = array(
	'COUNT' => \ITG\Custom\Favourites::getCount(),
	'LIST' => \ITG\Custom\Favourites::getList(),
);
