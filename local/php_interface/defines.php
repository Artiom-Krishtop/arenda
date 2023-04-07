<?php

$dbRes = CGroup::GetList($by = "c_sort", $order = "asc", array('STRING_ID' => 'RENTAL_USER'));

if($group = $dbRes->Fetch()){
    define('RENT_USER_GROUP', $group['ID']);
}

define('YANDEX_MAP_API_KEY', 'a85e2ad9-7d8b-4016-8410-515818625a06');
