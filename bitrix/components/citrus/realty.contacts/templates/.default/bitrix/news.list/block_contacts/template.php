<?php

use Bitrix\Main\Localization\Loc;

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$this->setFrameMode(true);

$arResult["LIST_PAGE_URL"] = str_replace('//', '/', CComponentEngine::MakePathFromTemplate($arResult["LIST_PAGE_URL"]));

foreach ($arResult["ITEMS"] as $key=>$arItem)
{
    $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
    $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));

    ?>
    <div class="contacts-block content-col" id="<?=$this->GetEditAreaId($arItem['ID'])?>">
        <h3 class="contacts-title"><?=$arItem["NAME"]?></h3>
        <?
        if ($arItem["PREVIEW_TEXT"])
            echo '<p>' . $arItem["PREVIEW_TEXT"] . '</p>';
        ?>
        <div>
            <a href="javascript:void(0);" class="contacts-map-link map-link" rel="nofollow" data-address="<?=$arItem["PROPERTIES"]["address"]["VALUE"]?>">
                <span class="contacts-map-link-icon fa fa-fw fa-map-marker"></span>
                <span class="contacts-map-link-label"><?=Loc::getMessage("CITRUS_REALTY_SHOW_ON_MAP")?></span>
            </a>
         </div>

        <dl class="dl-menu">
        <?
        if (count($arItem["DISPLAY_PROPERTIES"]) > 0)
        {
            $schedule = $phones = '';
            foreach ($arItem["DISPLAY_PROPERTIES"] as $pid => $arProperty)
            {
                echo "<dt>{$arProperty["NAME"]}</dt>";

                if ($arProperty["PROPERTY_TYPE"] == 'F')
                {
                    if (!is_array($arProperty['VALUE']))
                    {
                        $arProperty['VALUE'] = array($arProperty['VALUE']);
                        $arProperty['DESCRIPTION'] = array($arProperty['DESCRIPTION']);
                    }
                    $arProperty["DISPLAY_VALUE"] = Array();
                    foreach ($arProperty["VALUE"] as $idx => $value)
                    {
                        $path = CFile::GetPath($value);
                        $desc = strlen($arProperty["DESCRIPTION"][$idx]) > 0 ? $arProperty["DESCRIPTION"][$idx] : bx_basename($path);
                        if (strlen($path) > 0)
                        {
                            $ext = pathinfo($path, PATHINFO_EXTENSION);
                            $fileinfo = '';
                            if ($arFile = CFile::GetByID($value)->Fetch())
                                $fileinfo .= ' (' . $ext . ', ' . round($arFile['FILE_SIZE'] / 1024) . GetMessage('FILE_SIZE_Kb') . ')';
                            $arProperty["DISPLAY_VALUE"][] = "<a href=\"{$path}\" class=\"file file-{$ext}\">" . $desc . "</a>" . $fileinfo;
                        }
                    }
                    $val = is_array($arProperty["DISPLAY_VALUE"]) ? implode(', ', $arProperty["DISPLAY_VALUE"]) : $arProperty['DISPLAY_VALUE'];
                } else
                {
                    if (!is_array($arProperty["DISPLAY_VALUE"]))
                        $arProperty["DISPLAY_VALUE"] = Array($arProperty["DISPLAY_VALUE"]);
                    $ar = $ar2 = array();
                    foreach ($arProperty["DISPLAY_VALUE"] as $idx => $value)
                    {
                        $ar[] = $value . (strlen($arProperty["DESCRIPTION"][$idx]) > 0 ? ' (' . $arProperty["DESCRIPTION"][$idx] . ')' : '');
                        $ar2[] = (strlen($arProperty["DESCRIPTION"][$idx]) > 0 ? $arProperty["DESCRIPTION"][$idx] . ': ' : '') . $value;
                    }

                    $val = implode(', ', $ar);
                    if ($arProperty["CODE"] == 'schedule')
                        $schedule = implode('<br>', $ar2);
                    elseif ($arProperty["CODE"] == 'phones')
                        $val = $phones = Citrus\Arealty\Helper::formatPhoneNumber($val);
                }


                ?><dd><?= $val ?></dd><?
            }
        }
        ?>
        </dl>
    </div>
    <?
}
