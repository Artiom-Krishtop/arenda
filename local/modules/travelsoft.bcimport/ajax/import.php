<?php
define("STOP_STATISTICS", true);
define("NO_KEEP_STATISTIC", "Y");
define("NO_AGENT_STATISTIC", "Y");
define("DisableEventsCheck", true);
define("BX_SECURITY_SHOW_MESSAGE", true);
define("PUBLIC_AJAX_MODE", true);

require($_SERVER["DOCUMENT_ROOT"] . '/bitrix/modules/main/include/prolog_before.php');
@set_time_limit(0);
@ignore_user_abort(true);

\Bitrix\Main\Loader::includeModule("travelsoft.bcimport");

$city = $_REQUEST['city'] ?? '';

if(!empty($city)){

    \travelsoft\Tools::import($city);

    //travelsoft\Utils::jsonResponse($result);
}
