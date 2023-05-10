<?php

namespace travelsoft;

/**
 * Description of Gateway
 *
 * @author dimabresky
 */
class Gateway
{
    /**
     * @param string $method
     * @param array $parameters
     * @return bool|false|mixed|string
     */
    private static function sendRequest(string $method, array $parameters)
    {
        $url = Fields::getApiUrl() . $method . '?' . http_build_query($parameters);

        //error_reporting(~0); ini_set('display_errors', 1);

        $result = file_get_contents($url, false, stream_context_create(['http' => ['method' => "GET", 'header' => 'Accept-Charset: UTF-8, *;q=0']]));

        $result = mb_convert_encoding($result, "utf-8", "windows-1251");
        $result = mb_convert_encoding($result, "HTML-ENTITIES", "UTF-8");
        return $result;
    }

    /**
     * @param $city
     * @return string
     */
    public static function getBcHtml($city)
    {
        return self::sendRequest('', [
            'id' => 152,
            'search' => 1,
            's_area' => $city,
        ]);
    }


    public static function getBcHtmlOnRegion($city, $region)
    {
        return self::sendRequest('', [
            'id' => 152,
            'search' => 1,
            's_area' => $city,
            's_raion[]' => $region,
        ]);
    }

    public static function getBcHtmlBuilding($buildingId)
    {
        return self::sendRequest('', [
            'id' => 152,
            'bld' => $buildingId
        ]);
    }
}
