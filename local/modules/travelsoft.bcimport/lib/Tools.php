<?php

namespace travelsoft;

use CFile;
use DOMDocument;
use DOMElement;
use DOMNodeList;
use DOMXPath;
use travelsoft\stores\City;
use travelsoft\stores\Building;
use travelsoft\stores\Offer;
use travelsoft\stores\Region;

/**
 * Tools class
 */
class Tools
{
    /**
     * @param mixed $var
     */
    public static function dump($var)
    {
        echo "<pre>" . print_r($var, 1) . "</pre>";
    }

    /**
     * @param mixed $var
     */
    public static function toLog($var)
    {
        ob_start();
        self::dump($var);
        \file_put_contents($_SERVER["DOCUMENT_ROOT"] . "/local/travelsoft.log.txt", ob_get_clean());
    }

    public static function importTest(){
        self::import(141);

        //echo "<pre>" . print_r($offers, 1) . "</pre>";
    }

    public static function import($cityId)
    {
        $offers = self::generateOffers($cityId);

        foreach ($offers as $offer) {
            $additionalProperties = [];
            foreach ($offer['PROPERTY_VALUES']['photo'] as $photo) {
                $additionalProperties['PROPERTY_VALUES']['photo'][] = CFile::MakeFileArray($photo);
            }
            unset($offer['PROPERTY_VALUES']['photo']);
            $additionalProperties['DETAIL_PICTURE'] = array_shift($additionalProperties['PROPERTY_VALUES']['photo']);

            $offerId = Offer::updateOrCreate(['XML_ID' => $offer['XML_ID'], 'IBLOCK_SECTION_ID' => 27], $offer, $additionalProperties);

            echo "<pre>" . print_r(['offer_id' => $offerId], 1) . "</pre>";
        }
    }

    private static function generateOffers($cityId)
    {
        $city = City::getById($cityId);

        $regions = Region::get(['filter' => ['PROPERTY_CITY' => $city['ID']]]);

        $offers = [];
        $offer = [];
        foreach ($regions as $region) {

            $html = Gateway::getBcHtmlOnRegion($city['XML_ID'], $region['XML_ID']);
            $dom = new DOMDocument;
            $dom->loadHTML($html);

            $xpath = new DomXPath($dom);
            $table = $xpath->query("//table[@class='tab100p']")->item(0);
            $trNodes = $xpath->query('tr', $table);

            $buildingData = [];
            foreach ($trNodes as $key => $trNode) {

                if ($key < 4) {
                    continue;
                }

                $tdNodes = $xpath->query("td", $trNode);

                if ($tdNodes->length == 1) {
                    foreach ($tdNodes as $tdNode) {
                        foreach ($xpath->query('div//a', $tdNode) as $aNode) {
                            $buildingData = self::generateBuildingData($aNode, $city['ID'], $region['ID']);
                        }
                    }
                } else {
                    if ($tdNodes->length == 13) {
                        if (!empty($offer)) {
                            array_push($offers, $offer);
                            $offer = [];
                        }

                        $offer = self::startGenerateOffer($xpath, $tdNodes, $buildingData, $city);
                    } else if ($tdNodes->length == 9) {
                        self::addToOffer($xpath, $tdNodes, $offer);
                    }
                }
            }
        }
        if (!empty($offer)) {
            array_push($offers, $offer);
        }

        self::modifyOffers($offers);

        return $offers;
    }

    private static function modifyOffers(array &$offers)
    {
        foreach ($offers as &$offer) {
            if (!empty($offer['DETAIL_TEXT'])) {
                $offer['NAME'] .= " ({$offer['DETAIL_TEXT']})";
            } else {
                $offer['NAME'] .= " ({$offer['PROPERTY_VALUES']['rooms_html'][0]['type']} {$offer['PROPERTY_VALUES']['common_area']} КВ.М)";
            }

            $rooms_html = '<table class="rooms_html">
                    <thead>
                        <tr>
                            <th>Номер помещения</th>
                            <th>Назначение помещения</th>
                            <th>Этаж</th>
                            <th>Площадь</th>
                            <th>Освещение</th>
                            <th>Отопление</th>
                            <th>Телефонизация</th>
                            <th>Водопровод</th>
                        </tr>
                        </thead>
                        <tbody>';
            foreach ($offer['PROPERTY_VALUES']['rooms_html'] as $room) {

                $rooms_html .= "<tr>
                        <td>{$room['number']}</td>    
                        <td>{$room['type']}</td>    
                        <td>{$room['floor']}</td>    
                        <td>{$room['rooms_area']}</td>    
                        <td>" . ($room['services']['lighting'] ? '+' : '') . "</td>    
                        <td>" . ($room['services']['heating'] ? '+' : '') . "</td>    
                        <td>" . ($room['services']['telephone_lines'] ? '+' : '') . "</td>    
                        <td>" . ($room['services']['water_supply'] ? '+' : '') . "</td>    
                        </tr>";
            }

            $rooms_html .= '</tbody></table>';

            $offer['PROPERTY_VALUES']['rooms_html'] = $rooms_html;

            $offer['XML_ID'] = md5($offer['NAME']);
        }
        unset($offer);
    }

    private static function generateBuildingData(DOMElement $aNode, $cityId, $regionId)
    {
        $domBuilding = new DOMDocument;
        $link = $aNode->getAttribute('href');

        parse_str(parse_url($link, PHP_URL_QUERY), $query);

        $htmlBuilding = Gateway::getBcHtmlBuilding($query['bld']);
        $domBuilding->loadHTML($htmlBuilding);
        $xpathBuilding = new DomXPath($domBuilding);
        $buildingContent = $xpathBuilding->query("//div[@class='content']")->item(0);

        $buildingData = $xpathBuilding->query("div[@class='hideair']//span[@class='menu_sel_03']", $buildingContent);

        $buildingValues = [
            'NAME' => $buildingData->item(2)->nodeValue,
            'DETAIL_PICTURE' => $xpathBuilding->query('a//img', $buildingContent)->item(0)->getAttribute('src'),
            'PROPERTY_VALUES' => [
                'COUNTRY' => Settings::$belarusCountryId,
                'CITY' => $cityId,
                'CITY_AREA' => $regionId,
                'ADDRESS' => $buildingData->item(2)->nodeValue,
                'CURATOR' => $buildingData->item(3)->nodeValue,
                'CONTRACT_DESIGNER' => $buildingData->item(4)->nodeValue,
                'ZONE_COEFFICIENT' => $buildingData->item(5)->nodeValue,
            ]
        ];

        $buildingId = Building::updateOrCreate(['XML_ID' => $query['bld']], $buildingValues);

        return [
            'id' => $buildingId,
            'name' => $buildingValues['NAME'],
            'COUNTRY' => $buildingValues['PROPERTY_VALUES']['COUNTRY'],
            'CITY' => $buildingValues['PROPERTY_VALUES']['CITY'],
            'CITY_AREA' => $buildingValues['PROPERTY_VALUES']['CITY_AREA'],
        ];
    }

    private static function addToOffer(DomXPath $xpath, DOMNodeList $tdNodes, array &$roomData)
    {
        $propertyData = [
            'number' => $xpath->query("span[@class='content_table']", $tdNodes->item(0))->item(0)->nodeValue,
            'floor' => $xpath->query("span[@class='content_table']", $tdNodes->item(3))->item(0)->nodeValue,
            'type' => $xpath->query("span[@class='content_table']", $tdNodes->item(2))->item(0)->nodeValue,
            'rooms_area' => (float)$xpath->query("span[@class='content_table']", $tdNodes->item(4))->item(0)->nodeValue,

            'services' => [
                'lighting' => $xpath->query('img', $tdNodes->item(5))->item(0) != null,
                'heating' => $xpath->query('img', $tdNodes->item(6))->item(0) != null,
                'telephone_lines' => $xpath->query('img', $tdNodes->item(7))->item(0) != null,
                'water_supply' => $xpath->query('img', $tdNodes->item(8))->item(0) != null,
            ]
        ];

        self::pushIfNotEmptyAndNotExist($roomData['PROPERTY_VALUES']['NEW_ROOMS_TYPE'], self::normalizeRoomType($xpath->query("span[@class='content_table']", $tdNodes->item(2))->item(0)->nodeValue));
        array_push($roomData['PROPERTY_VALUES']['NEW_ROOMS_AREA'], $propertyData['rooms_area']);
        $roomData['PROPERTY_VALUES']['common_area'] += $propertyData['rooms_area'];
        array_push($roomData['PROPERTY_VALUES']['rooms_html'], $propertyData);
        self::addPhotos($roomData['PROPERTY_VALUES']['photo'], $xpath->query("span[@class='content_table']//div//a", $tdNodes->item(1)));
        self::pushServices($roomData['PROPERTY_VALUES']['NEW_COMMERCIAL_FEATURES'], $propertyData['services']);
    }

    private static function startGenerateOffer(DomXPath $xpath, DOMNodeList $tdNodes, array $buildingData, $city)
    {
        $propertyData = [
            'number' => $xpath->query("span[@class='content_table']", $tdNodes->item(1))->item(0)->nodeValue,
            'floor' => $xpath->query("span[@class='content_table']", $tdNodes->item(4))->item(0)->nodeValue,
            'type' => $xpath->query("span[@class='content_table']", $tdNodes->item(3))->item(0)->nodeValue,
            'rooms_area' => (float)$xpath->query("span[@class='content_table']", $tdNodes->item(5))->item(0)->nodeValue,

            'services' => [
                'lighting' => $xpath->query('img', $tdNodes->item(6))->item(0) != null,
                'heating' => $xpath->query('img', $tdNodes->item(7))->item(0) != null,
                'telephone_lines' => $xpath->query('img', $tdNodes->item(8))->item(0) != null,
                'water_supply' => $xpath->query('img', $tdNodes->item(9))->item(0) != null,
            ],
        ];

        $roomData = [];
        $roomData['NAME'] = $buildingData['name'];
        $roomData['PROPERTY_VALUES']['NEW_BUILDING'] = $buildingData['id'];
        $roomData['PROPERTY_VALUES']['COUNTRY'] = $buildingData['COUNTRY'];
        $roomData['PROPERTY_VALUES']['CITY'] = $buildingData['CITY'];
        $roomData['PROPERTY_VALUES']['CITY_AREA'] = $buildingData['CITY_AREA'];
        $roomData['PROPERTY_VALUES']['ADDRESS'] = $buildingData['name'];

        //$roomData['XML_ID'] = current(explode(',', $xpath->query('input', $tdNodes->item(0))->item(0)->getAttribute('value')));
        $roomData['DETAIL_TEXT'] = $xpath->query("span[@class='content_table']//span[@class='blocketc']", $tdNodes->item(10))->item(0)->nodeValue;
        $roomData['PROPERTY_VALUES']['NEW_ROOMS_TYPE'] = [];
        self::pushIfNotEmptyAndNotExist($roomData['PROPERTY_VALUES']['NEW_ROOMS_TYPE'], self::normalizeRoomType($propertyData['type']));
        $roomData['PROPERTY_VALUES']['NEW_FLOOR'] = $propertyData['floor'];
        $roomData['PROPERTY_VALUES']['common_area'] = $propertyData['rooms_area'];
        $roomData['PROPERTY_VALUES']['NEW_ROOMS_AREA'] = [];
        $roomData['PROPERTY_VALUES']['geodata'] = "Беларусь {$city['NAME']} {$buildingData['name']}";
        array_push($roomData['PROPERTY_VALUES']['NEW_ROOMS_AREA'], $propertyData['rooms_area']);
        $roomData['PROPERTY_VALUES']['cost'] = (float)$xpath->query("span[@class='content_table']//span[@class='blocketc']", $tdNodes->item(12))->item(0)->nodeValue;
        $roomData['PROPERTY_VALUES']['cost_period'] = '54';
        $roomData['PROPERTY_VALUES']['cost_unit'] = '50';
        $roomData['PROPERTY_VALUES']['photo'] = [];
        $roomData['PROPERTY_VALUES']['NEW_COMMERCIAL_FEATURES'] = [];
        self::addPhotos($roomData['PROPERTY_VALUES']['photo'], $xpath->query("span[@class='content_table']//div//a", $tdNodes->item(2)));
        self::pushServices($roomData['PROPERTY_VALUES']['NEW_COMMERCIAL_FEATURES'], $propertyData['services']);

        $roomData['PROPERTY_VALUES']['rooms_html'] = [];
        array_push($roomData['PROPERTY_VALUES']['rooms_html'], $propertyData);
        $roomData['PROPERTY_VALUES']['price_for_meter'] = (float)$xpath->query("span[@class='content_table']//span[@class='blocketc']", $tdNodes->item(11))->item(0)->nodeValue;

        return $roomData;
    }

    private static function addPhotos(&$array, DOMNodeList $aNodes)
    {
        $photos = [];
        foreach ($aNodes as $aNode) {
            $photos[] = $aNode->getAttribute('href');
        }
        $array = array_unique(array_merge($array, $photos));
    }

    private static function pushIfNotEmptyAndNotExist(&$array, $value)
    {
        if ($value != '' && !in_array($value, $array)) {
            array_push($array, $value);
        }
    }

    private static function pushServices(&$array, $services)
    {
        foreach ($services as $serviceCode => $serviceExist) {
            $code = self::normalizeService($serviceCode);
            if ($serviceExist) {
                self::pushIfNotEmptyAndNotExist($array, $code);
            }
        }
    }

    private static function normalizeService($serviceCode)
    {
        $services = [
            'lighting' => '290', //Освещение
            'heating' => '291', //Отопление
            'telephone_lines' => '292', //Телефонные линии
            'water_supply' => '293', //Водопровод
        ];

        return $services[$serviceCode];
    }

    private static function normalizeRoomType($roomType)
    {
        $roomType = strtolower($roomType);
        $types = [
            'кабинет' => '315',
            'комната' => '316',
            'вспом.пом.' => '317',
            'кладовая' => '318',
            'склад' => '319',
            'столовая' => '320',
            'кухня' => '321',
            'моечная' => '322',
            'зал заседаний' => '323',
            'произв.уч-к' => '324',
            'констр.бюро' => '325',
            'уч.класс' => '326',
            'библиотека' => '327',
            'лаборатория' => '328',
            'служебное' => '329',
            'антресоль' => '330',
            'гарбероб' => '331',
            'архив' => '332',
            'коридор' => '333',
            'вахта' => '334',
            'уч.кабинет' => '335',
            'произв.пом.' => '336',
            'множ.уч-ок' => '337',
            'читал.зал' => '338',
            'гараж' => '339',
            'подсобное' => '340',
            'диспетч.' => '341',
            'производств.' => '342',
            'демонс.зал' => '343',
            'санузел' => '344',
            'гардероб' => '345',
            'душевая' => '346',
            'раздевалка' => '347',
            'магазин' => '348',
            'комн.приема пищи' => '349',
            'комн. отдыха' => '350',
            'тренаж.каб.' => '351',
            'массажная' => '352',
            'парилка' => '353',
            'бассейн' => '354',
            'мастерская' => '355',
            'комн.гигиены' => '356',
            'машино-место' => '357',
            'пост охраны' => '358',
            'тен.корт' => '359',
            'реконс.стол' => '360',
            'рецепция' => '361',
            'зал аэробики' => '362',
            'бар' => '363',
            'лабаратория' => '364',
            'обед.зал' => '365',
            'салон' => '366',
            'бытовая' => '367',
            'цех' => '368',
            'лест.клетка' => '369',
            'касса' => '370',
            'тамбур' => '371',
            'хол.камера' => '372',
            'компрессорная' => '373',
            'разгрузочная' => '374',
            'команата гигиены' => '375',
            'аудитория' => '376',
            'столярная' => '377',
            'множ. уч-к' => '378',
            'выст.зал' => '379',
            'светокопия' => '380',
            'компьют.зал' => '381',
            'зал' => '382',
            'комн.отдыха' => '383',
            'преддушевая' => '384',
            'зал.заседаний' => '385',
            'ком.гигиены' => '386',
        ];

        return $types[$roomType] ?? '';
    }
}
