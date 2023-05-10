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

if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();

if (method_exists($this, 'setFrameMode')) {
	$this->setFrameMode(true);
}

if ($arResult['ACTION']['status']=='error') {
	ShowError($arResult['ACTION']['message']);
} elseif ($arResult['ACTION']['status']=='ok') {
	ShowNote($arResult['ACTION']['message']);
}
?>
<div class="footer-title"><?=GetMessage("CITRUS_SUBSCRIBE_TITLE")?></div>

<form action="<?= POST_FORM_ACTION_URI?>" method="post" id="citrus_subscribe_form" class="subscriptions">
	<?= bitrix_sessid_post()?>
	<input type="hidden" name="citrus_subscribe" value="Y" />
	<input type="hidden" name="charset" value="<?= SITE_CHARSET?>" />
	<input type="hidden" name="site_id" value="<?= SITE_ID?>" />
	<input type="hidden" name="citrus_format" value="<?= $arParams['FORMAT']?>" />
	<input type="hidden" name="citrus_not_confirm" value="<?= $arParams['NO_CONFIRMATION']?>" />
	<input type="hidden" name="citrus_key" value="<?= md5($arParams['JS_KEY'].$arParams['NO_CONFIRMATION'])?>" />
	<input class="subscriptions-input" type="text" name="citrus_email" value="" placeholder="<?=GetMessage("CITRUS_SUBSCRIBE_PLACEHOLDER")?>" required="required" />
    <div class="fz152">
        <div class="field-form-name"></div>
        <div class="field-checks">
            <input class="f-line-input-val" type="checkbox" value="Y" id="citrus_subscribe_fz152" name="fz152" required>
            <label class="pull-left lh-0 gray-6t" for="citrus_subscribe_fz152"><?=GetMessage('CITRUS_SUBSCRIBE_FZ152_CHECKBOX', array('#URL#' => '/bitrix/components/citrus/iblock.element.form/agreement.php?site=' . SITE_ID))?></label>
        </div>
    </div>
	<button class="subscriptions-button" type="submit" name="citrus_submit" id="citrus_subscribe_submit" value="<?=GetMessage("CITRUS_SUBSCRIBE_BUTTON")?>" data-noLoad="true"><span class="icon-mail"></span></button>
</form>
<div id="citrus_subscribe_res" style="display: none;"></div>
