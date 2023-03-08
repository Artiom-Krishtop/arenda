<?php
/**
 * Created by PhpStorm.
 * User: sun
 * Date: 16.01.19
 * Time: 11:59
 */

namespace travelsoft;


class Utils
{
    public static function jsonResponse($response)
    {
        header('Content-Type: application/json');
        echo $response;
    }
}
