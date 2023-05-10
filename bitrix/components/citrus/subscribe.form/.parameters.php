<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

$arComponentParameters = array(
	'PARAMETERS' => array(
		'INC_JQUERY' => array(
			'PARENT' => 'BASE',
			'NAME' => GetMessage('CITRUS_SUBSCRIBE_INCLUDE_JQUERY'),
			'TYPE' => 'CHECKBOX',
		),
		'NO_CONFIRMATION' => array(
			'PARENT' => 'BASE',
			'NAME' => GetMessage('CITRUS_SUBSCRIBE_NO_CONFIRMATION'),
			'TYPE' => 'CHECKBOX',
		),
		'FORMAT' => array(
			'PARENT' => 'BASE',
			'NAME' => GetMessage('CITRUS_SUBSCRIBE_FORMAT'),
			'TYPE' => 'LIST',
			'VALUES' => array(
				'text'=>'text',
				'html'=>'html'
			),
		),
	),
);