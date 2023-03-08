<?
if (!\Bitrix\Main\Loader::includeModule("citrus.arealty"))
{
	ShowError(GetMessage("CITRUS_REALTY_MODULE_NOT_FOUND"));
	return;
}
