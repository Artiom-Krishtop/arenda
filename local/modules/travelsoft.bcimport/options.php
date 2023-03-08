<?php
if (!$USER->isAdmin())
    return;

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

$mid = "travelsoft.bcimport";

global $APPLICATION;

function renderOptions($arOptions, $mid)
{
    $options = '';
    foreach ($arOptions as $name => $arValues) {

        $cur_opt_val = htmlspecialcharsbx(Bitrix\Main\Config\Option::get($mid, $name));
        $name = htmlspecialcharsbx($name);

        $options .= '<tr>';
        $options .= '<td width="40%">';
        $options .= '<label for="' . $name . '">' . $arValues['DESC'] . ':</label>';
        $options .= '</td>';
        $options .= '<td width="60%">';
        if ($arValues['TYPE'] == 'select') {

            $options .= '<select id="' . $name . '" name="' . $name . '">';
            $options .= '<option>-</option>';
            foreach ($arValues['VALUES'] as $key => $value) {
                $options .= '<option ' . ($cur_opt_val == $key ? 'selected' : '') . ' value="' . $key . '">' . $value . '</option>';
            }
            $options .= '</select>';
        } elseif ($arValues['TYPE'] == 'text') {

            $options .= '<input type="text" name="' . $name . '" value="' . $cur_opt_val . '">';
        }
        $options .= '</td>';
        $options .= '</tr>';
    }
    echo $options;
}

CModule::IncludeModule("iblock");

$arIBlocks = array();
$db_iblock = CIBlock::GetList(["SORT" => "ASC"]);
while ($arRes = $db_iblock->Fetch())
    $arIBlocks[$arRes["ID"]] = "[" . $arRes["ID"] . "] " . $arRes["NAME"];

$module_options = \Bitrix\Main\Config\Option::getForModule($mid);

$options = [
    'API_URL' => [
        'DESC' => '',
        'TYPE' => 'text',
    ],
    'CITY_STORE_ID' => [
        'DESC' => '',
        'TYPE' => 'select',
        'VALUES' => $arIBlocks,
    ]
];
if (!empty($module_options)) {
    foreach (array_keys($module_options) as $name) {
        $options[$name]['DESC'] = Loc::getMessage("TRAVELSOFT_OPTION_" . $name);
    }
}

$main_options["TOTAL"] = $options;

$tabs = [
    [
        "DIV" => "edit1",
        "TAB" => Loc::getMessage("TRAVELSOFT_OPTIONS_TAB"),
        "ICON" => "",
        "TITLE" => Loc::getMessage("TRAVELSOFT_OPTIONS_TAB_TITLE"),
    ]
];

$o_tab = new CAdminTabControl("TravelsoftTabControl", $tabs);
if ($_SERVER['REQUEST_METHOD'] == "POST" && check_bitrix_sessid()) {

    foreach ($main_options as $arBlockOption) {

        foreach ($arBlockOption as $name => $arValues) {
            if (isset($_REQUEST[$name])) {
                \Bitrix\Main\Config\Option::set($mid, $name, $_REQUEST[$name]);
            }
        }
    }

    LocalRedirect($APPLICATION->GetCurPage() . "?mid=" . urlencode($mid) . "&lang=" . urlencode(LANGUAGE_ID) . "&" . $o_tab->ActiveTabParam());
}
$o_tab->Begin();
?>

<form method="post"
      action="<? echo $APPLICATION->GetCurPage() ?>?mid=<?= urlencode($mid) ?>&amp;lang=<? echo LANGUAGE_ID ?>">
    <? foreach ($main_options as $arOption) {
        $o_tab->BeginNextTab();
        renderOptions($arOption, $mid);
    }
    $o_tab->Buttons();
    ?>
    <input type="submit" name="save" value="Сохранить" title="Сохранить" class="adm-btn-save">
    <input type="submit" name="reset" title="Сбросить" OnClick="return confirm('Сбросить')" value="Сбросить">
    <?= bitrix_sessid_post(); ?>
    <? $o_tab->End(); ?>
</form>
