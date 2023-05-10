<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
LocalRedirect($APPLICATION->GetCurDir() . 'stati/', false, "301 Moved permanently");
?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>