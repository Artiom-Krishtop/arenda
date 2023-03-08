<?php

/** @var CBitrixComponent $component ������� ��������� ��������� */
/** @var CBitrixComponentTemplate $this ������� ������ (������, ����������� ������) */
/** @var array $arResult ������ ����������� ������ ���������� */
/** @var array $arParams ������ �������� ���������� ����������, ����� �������������� ��� ����� �������� ���������� ��� ������ ������� (��������, ����������� ��������� ����������� ��� ������). */
/** @var string $templateFile ���� � ������� ������������ ����� �����, �������� /bitrix/components/bitrix/iblock.list/templates/.default/template.php) */
/** @var string $templateName ��� ������� ���������� (��������: .d�fault) */
/** @var string $templateFolder ���� � ����� � �������� �� DOCUMENT_ROOT (�������� /bitrix/components/bitrix/iblock.list/templates/.default) */
/** @var array $templateData ������ ��� ������, �������� ��������, ����� ������� ����� �������� ������ �� template.php � ���� component_epilog.php, ������ ��� ������ �������� � ���, �.�. ���� component_epilog.php ����������� �� ������ ���� */
/** @var string $parentTemplateFolder ����� ������������� �������. ��� ����������� �������������� ����������� ��� �������� (��������) ������ ������������ ��� ����������. �� ����� ��������� ��� ������������ ������� ���� ������������ ����� ������� */
/** @var string $componentPath ���� � ����� � ����������� �� DOCUMENT_ROOT (����. /bitrix/components/bitrix/iblock.list) */

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

if (method_exists($this, 'setFrameMode'))
{
	$this->setFrameMode(true);
}

$this->SetViewTarget('footer-before');


if ($arResult['ACTION']['status'] == 'error')
{
	ShowError($arResult['ACTION']['message']);
}
elseif ($arResult['ACTION']['status'] == 'ok')
{
	ShowNote($arResult['ACTION']['message']);
}
?>

<section class="section theme--bg-color">
    <div class="w hello-world">
        <div class="subscribe_line">
            <div class="subscribe__text">
                <div class="subscribe__text-1 font-2"><?=Loc::getMessage("CITRUS_SUBSCRIBE_TITLE1")?></div>
                <div class="subscribe__text-2"><?=Loc::getMessage("CITRUS_SUBSCRIBE_TITLE2")?></div>
            </div>

            <form action="<?=POST_FORM_ACTION_URI?>" method="post" id="citrus_subscribe_form" class="subscribe-form">
				<?=bitrix_sessid_post()?>
                <input type="hidden" name="citrus_subscribe" value="Y"/>
                <input type="hidden" name="charset" value="<?=SITE_CHARSET?>"/>
                <input type="hidden" name="site_id" value="<?=SITE_ID?>"/>
                <input type="hidden" name="citrus_format" value="<?=$arParams['FORMAT']?>"/>
                <input type="hidden" name="citrus_not_confirm" value="<?=$arParams['NO_CONFIRMATION']?>"/>
                <input type="hidden" name="citrus_key"
                       value="<?=md5($arParams['JS_KEY'] . $arParams['NO_CONFIRMATION'])?>"/>
                <input type="hidden" name="fz152" value="Y">

                <div class="input-container">

                    <div class="subscribe-row">
                        <input data-valid="required email" class="subscribe_input" type="text" name="citrus_email"
                               value="" placeholder="<?=GetMessage("CITRUS_SUBSCRIBE_PLACEHOLDER")?>"/>
                        <button class="btn btn-white" type="submit" name="citrus_submit" id="citrus_subscribe_submit"
                                value="<?=GetMessage("CITRUS_SUBSCRIBE_BUTTON")?>" data-noLoad="true"><span
                                    class="display-sm-n icon-mail"></span><span
                                    class="display-xs-n display-sm-i"><?=Loc::getMessage("CITRUS_SUBSCRIBE_BTN")?></span>
                        </button>
                    </div>

                    <div class="error help-block"></div>
                </div>
            </form>

            <div id="citrus_subscribe_res" class="subscribe-message" style="display: none;"></div>
        </div><!-- .subscribe -->
    </div>

    <div class="subscribe-footer">
        <div class="w"><?=Loc::getMessage("CITRUS_SUBSCRIBE_FZ152_NOTICE")?>
            <a href="<?=SITE_DIR?>agreement/"><?=Loc::getMessage("CITRUS_SUBSCRIBE_FZ152_LINK")?></a></div>
    </div>
</section>
