<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

/**
 * @global CMain $APPLICATION
 */

global $APPLICATION;

//delayed function must return a string
if(empty($arResult))
	return "";

$strReturn = '';


$strReturn .= '<div class="account-content__breadcrumbs">';
$lastKey = array_key_last($arResult);

foreach ($arResult as $key => $section) {
	if($key != $lastKey){
        $strReturn .= '<a class="account-content__breadcrumbs-link" href="' . $section['LINK'] . '">' . $section['TITLE'] . '</a>';
	}else {
		$strReturn .= '<span class="account-content__breadcrumbs-link account-content__breadcrumbs-link--active">' . $section['TITLE'] . '</span>';
	}
}

$strReturn .= '</div>';

return $strReturn;?>
    
