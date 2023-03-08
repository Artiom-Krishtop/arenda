<?
$APPLICATION->IncludeComponent(
    'bitrix:main.calendar',
    '',
    array(
        'FORM_NAME' => $arResult['FORM_ID'],
        'INPUT_NAME' => $fieldInfo["FIELD_NAME"],
        'INPUT_VALUE' => $value,
    ),
    null,
    array('HIDE_ICONS' => 'Y')
);?>