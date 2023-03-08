<?php

use travelsoft\stores\City;

define("STOP_STATISTICS", true);
define("NO_KEEP_STATISTIC", "Y");
define("NO_AGENT_STATISTIC", "Y");
define("DisableEventsCheck", true);
define("BX_SECURITY_SHOW_MESSAGE", true);
define("PUBLIC_AJAX_MODE", true);

require($_SERVER["DOCUMENT_ROOT"] . '/bitrix/modules/main/include/prolog_before.php');
\Bitrix\Main\Loader::includeModule("travelsoft.bcimport");

$cities = City::get();

$response = array_column($cities, 'ID');

travelsoft\Utils::jsonResponse(json_encode($response));
