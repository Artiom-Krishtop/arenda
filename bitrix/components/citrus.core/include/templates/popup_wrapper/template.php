<?php

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */

$this->setFrameMode(true);

$showContent = function() use ($arResult, $arParams)
{
	global $APPLICATION;

	if ($arParams["AREA_FILE_SHOW"] === 'view_content')
	{
		$APPLICATION->ShowViewContent($arParams['VIEW_CONTENT_ID']);
	}
	else
	{
		echo $arResult['FILE_CONTENT'];
	}
};

$wrapperAdditionalParameters = '';

$wrapperClass = ['modal-content'];
if ($arParams['MODAL_CONTAINER_CLASS'])
{
	if (is_array($arParams['MODAL_CONTAINER_CLASS']))
	{
		$wrapperClass = array_merge($wrapperClass, $arParams['MODAL_CONTAINER_CLASS']);
	}
	else
	{
		$wrapperClass[] = $arParams['MODAL_CONTAINER_CLASS'];
	}
}
else
{
	$wrapperClass[] = 'modal-w-400';
}

$isAjax = \Bitrix\Main\Context::getCurrent()->getRequest()->isAjaxRequest();

/**
 * ������� ��� magnificPopup ����������� ������ ���� � ����� ����������,
 * ����� ������ ����� ����������� closeOnBgClick (http://dimsemenov.com/plugins/magnific-popup/documentation.html#closeonbgclick)
 *
 * https://github.com/dimsemenov/Magnific-Popup/blob/c8f6b8549ebff2306c5f1179c9d112308185fe2c/dist/jquery.magnific-popup.js#L731-L738
 */
?>
<div class="<?=implode(' ', $wrapperClass)?>"<?=$wrapperAdditionalParameters?>>
	<?php

	if ($isAjax)
	{
		echo '<!-- head -->';
		$APPLICATION->ShowCSS(true, false);
		$APPLICATION->ShowHeadStrings();
		$APPLICATION->ShowHeadScripts();
		echo '<!-- .head -->';
	}

	?>
	<div class="modal-header">
		<div class="modal-title"><?=($arParams['TITLE'] ?: $APPLICATION->GetTitle(false))?></div>
		<button class="btn modal-close-btn" <?=($isAjax ? 'data-dismiss="modal"' : 'onclick="window.close();"')?>>
			<span class="fa fa-times"></span>
		</button>
	</div>
	<div class="modal-body">
		<?$showContent();?>
	</div><!-- .modal-body -->
</div><!-- .modal-content -->
<?php

if ($isAjax)
{
	//require $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_after.php";
	\CMain::finalActions();
	die;
}

require $_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php";
