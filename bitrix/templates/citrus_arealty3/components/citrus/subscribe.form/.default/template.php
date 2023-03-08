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
<div class="f-t__title"><?=GetMessage("CITRUS_SUBSCRIBE_TITLE")?></div>

<form action="<?= POST_FORM_ACTION_URI?>" method="post" id="citrus_subscribe_form" class="subscribe">

	<div id="citrus_subscribe_res" style="display: none;"></div>
	<?= bitrix_sessid_post()?>
	<input type="hidden" name="citrus_subscribe" value="Y" />
	<input type="hidden" name="charset" value="<?= SITE_CHARSET?>" />
	<input type="hidden" name="site_id" value="<?= SITE_ID?>" />
	<input type="hidden" name="citrus_format" value="<?= $arParams['FORMAT']?>" />
	<input type="hidden" name="citrus_not_confirm" value="<?= $arParams['NO_CONFIRMATION']?>" />
	<input type="hidden" name="citrus_key" value="<?= md5($arParams['JS_KEY'].$arParams['NO_CONFIRMATION'])?>" />

	<div class="input-container subscribe__input-container">
		<input data-valid="required email" class="subscribe-input" type="text" name="citrus_email" value="" placeholder="<?=GetMessage("CITRUS_SUBSCRIBE_PLACEHOLDER")?>" required="required" />
		<button
			tabindex="-1"
			class="btn btn-link btn-subscribe"
			type="submit"
			name="citrus_submit"
			id="citrus_subscribe_submit"
			value="<?=GetMessage("CITRUS_SUBSCRIBE_BUTTON")?>"
			data-noLoad="true"><span class="icon-send"></span></button>
		<div class="error help-block"></div>
	</div>
	<div class="subscribe__agree-block input-container">
		<label class="subscribe__agree-label">
			<input type="checkbox" data-valid="required" name="fz152" class="subscribe__agree-input" value="Y">
			<span class="subscribe__agree-checkmark"></span>
			<span class="subscribe__agree-label-text"><?=GetMessage('CITRUS_SUBSCRIBE_FZ152_CHECKBOX', array('#URL#' => SITE_DIR.'agreement/'))?></a></span>
		</label>
	</div>
</form>

