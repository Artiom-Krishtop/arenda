<?php

$fields = ['USER_COMPANY', 'USER_PHONE'];

foreach ($_REQUEST as $key => $value) {
    if(in_array($key, $fields)){
        $arResult[$key] = $value;
    }
}
