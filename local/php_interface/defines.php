<?php

$dbRes = CGroup::GetList($by = "c_sort", $order = "asc", array('STRING_ID' => 'RENTAL_USER'));

if($group = $dbRes->Fetch()){
    define('RENT_USER_GROUP', $group['ID']);
}
