<?php

/** @var array $fieldInfo */

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

$code = str_replace('[]', '', $fieldInfo['CODE']);
$value = $fieldInfo['VALUE'] ?: $fieldInfo['OLD_VALUE'];
$isMultiple = $fieldInfo['MULTIPLE'] == 'Y';

if (!class_exists('\Bitrix\Main\UI\FileInput'))
{
	require __DIR__ . '/../input/template.php';
	return;
}

if ($isMultiple)
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

echo Bitrix\Main\UI\FileInput::createInstance(array(
    "name" => $code . ($isMultiple ? '[n#IND#]' : ''),
    "description" => $fieldInfo['WITH_DESCRIPTION'] == 'Y',
    "upload" => true,
    "allowUpload" => "F",
    "allowUploadExt" => $fieldInfo["FILE_TYPE"],
    "medialib" => false,
    "fileDialog" => true,
    "cloud" => false,
    "delete" => true,
    "maxCount" => $fieldInfo['LIMIT'],
    "maxSize" => 0,
))->show($inputValue);
