<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Политика конфиденциальности");

$APPLICATION->IncludeFile(SITE_DIR . "include/politika.php", Array(), Array(
    "MODE"      => "html",                                           // будет редактировать в веб-редакторе
    "NAME"      => "Редактировать политику конфиденциальности",      // текст всплывающей подсказки на иконке
));

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>