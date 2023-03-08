<?

if (isset($_GET['site']) && preg_match('#[a-z0-9]{2}#i', $_GET['site']))
{
    define('SITE_ID', strtolower($_GET['site']));
}

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle('');

?><?$APPLICATION->IncludeComponent("citrus:fz152.agreement", "", array());?><?

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");?>