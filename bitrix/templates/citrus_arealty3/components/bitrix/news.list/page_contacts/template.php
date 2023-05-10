<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

$this->setFrameMode(true);

Loc::loadMessages(__FILE__);

$arResult["LIST_PAGE_URL"] = str_replace('//', '/', CComponentEngine::MakePathFromTemplate($arResult["LIST_PAGE_URL"]));

?>

<div class="row-ib row-grid ">
    <div class="col-lg-6">
		<?php
		$jsParams = array(
			"id" => "contacts-map",
			"links" => ".contacts-item-name",
			"items" => array()
		);
		foreach ($arResult["ITEMS"] as $key=>$arItem)
		{
			$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'],
				CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
			$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'],
				CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"),
				["CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')]);

			?>
            <div class="contacts-block content-col" id="<?=$this->GetEditAreaId($arItem['ID'])?>">
                <div class="contacts-block__title"><?=$arItem['NAME']?></div>
	            <?php
	            if ($arItem["PREVIEW_TEXT"])
	            {
		            echo '<p class="contacts-block__description">' . $arItem["PREVIEW_TEXT"] . '</p>';
	            }

                if (is_array($arItem["DISPLAY_PROPERTIES"])
                    && !empty($arItem["DISPLAY_PROPERTIES"]))
	            {
		            $schedule = $phones = '';
		            foreach (\Citrus\Core\array_only($arItem["DISPLAY_PROPERTIES"], ['schedule', 'phones']) as $pid => $arProperty)
		            {
		                $val = is_array($arProperty["DISPLAY_VALUE"]) ? $arProperty["DISPLAY_VALUE"] : [$arProperty["DISPLAY_VALUE"]];
			            $desc = is_array($arProperty["DESCRIPTION"]) ? $arProperty["DESCRIPTION"] : [$arProperty["DESCRIPTION"]];

                        if ($arProperty["CODE"] == 'schedule')
                        {
	                        $schedule = array_reduce(array_keys($val), function ($result, $k) use ($val, $desc) {
	                            return (strlen($result) ? $result . '<br>' : '') . (($desc[$k] ? $desc[$k] . ': ': '') . $val[$k]);
                            }, '');
                        }
                        elseif ($arProperty["CODE"] == 'phones')
                        {
                            $phones = Citrus\Arealty\Helper::formatPhoneNumber(implode(', ', $val));
                        }
		            }

		            $APPLICATION->IncludeComponent(
			            'citrus.arealty:properties',
			            '',
			            [
				            'PROPERTIES' => $arItem['DISPLAY_PROPERTIES'],
				            'DISPLAY_PROPERTIES' => array_keys($arItem["DISPLAY_PROPERTIES"]),
                            'CSS_CLASS' => 'contacts-block__properties'
			            ],
			            $component,
			            ['HIDE_ICONS' => 'Y']
		            );
	            }
	            ?>
            </div>
			<?php

			if ($address = \Citrus\Core\array_get($arItem, "PROPERTIES.address.VALUE"))
			{
				$jsParams["items"][$key] = [
                    'address' => $address,
                    'code' => $arItem['CODE'],
                    'header' => $arItem['NAME'],
                    'name' => $arItem['NAME'],
                    'body' => $address . '<br>' . $phones,
                    'footer' => $schedule,
                ];
			}
		}
		?>
        <div class="btn-row btn-row--xs-center btn-row--md-start">
            <a href="javascript:void(0)" class="js_link_form_review btn btn-primary"><?= Loc::getMessage("CITRUS_AREALTY_CONTACTS_FORM_LINK") ?></a>
        </div>
    </div>
    <div class="col-lg-6">
        <div id="contacts-map" style="width: 100%; height: 500px;"></div>
    </div>
    <script data-src="/bitrix/">
        $().citrusObjectsMap(<?=CUtil::PhpToJSObject($jsParams)?>);

        $(function () {
            $('.js_link_form_review').click(function () {

                var elementClick = $('.form_review');
                var destination = $(elementClick).offset().top;

                $('html').animate({ scrollTop: destination }, 600);
                return false;
            });
        });
    </script>
</div>
