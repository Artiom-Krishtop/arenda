<?php 

function dd($text, $die = true){
    echo '<pre>' . print_r($text, 1) . '</pre>';
    if($die) die('_____END DEBUG_____');
}