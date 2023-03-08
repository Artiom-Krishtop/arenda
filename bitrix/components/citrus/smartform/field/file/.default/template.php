<?php

/** @var array $fieldInfo */

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

$code = preg_replace('#FIELDS\[([^\]]*?)\]#i', '$1', $fieldInfo['CODE']);
$value = $fieldInfo['VALUE'] ?: $fieldInfo['OLD_VALUE'];

if ($fieldInfo['MULTIPLE'] == 'Y')
{
    $value = is_array($value) ? $value : array($value);
    $inputValue = array();
    foreach ($value as $key => $val)
    {
	    $inputValue[$code . "[" . $key . "]"] =
		    is_array($val) && $val["VALUE"]
		        ? $val["VALUE"]
			    : $val;
    }
    $inputValue = array_filter($inputValue);
}
else
{
    $inputValue = $value;
}

global $APPLICATION;
$APPLICATION->IncludeComponent("bitrix:main.file.input", "drag_n_drop",
	array(
		"INPUT_NAME" => $code,
		"MULTIPLE" => $fieldInfo['MULTIPLE'],
		"MODULE_ID" => 'main',
		"MAX_FILE_SIZE" => 0,
		"ALLOW_UPLOAD" => "F",
		"ALLOW_UPLOAD_EXT" => $fieldInfo["FILE_TYPE"],
	),
	false,
	array('HIDE_ICONS' => 'Y')
);?>