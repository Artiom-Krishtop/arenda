<?php

namespace travelsoft\adapters;

use travelsoft\Fields;

\Bitrix\Main\Loader::includeModule("iblock");

/**
 * Класс адаптер для bitrix iblock
 */
abstract class Iblock extends Store
{
    /**
     * @var string
     */
    protected static $storeName = null;

    /**
     * Возвращает полученные данные из хранилища в виде массива
     * @param array $query
     * @param callable $callback
     * @return array|\CIBlockResult|int
     */
    public static function get(array $query = array(), bool $likeArray = true, callable $callback = null)
    {
        if ($query['filter']) {

            $arFilter = $query["filter"];
        }

        $arFilter["IBLOCK_ID"] = self::getStoreId();

        if ($query['order']) {

            $arOrder = $query['order'];
        }

        if ($query['select']) {

            $arSelect = $query['select'];
        }

        if ($query['nav']) {

            $arNav = $query['nav'];
        }

        $dbList = \CIBlockElement::GetList($arOrder, $arFilter, null, $arNav, $arSelect);

        if (!$likeArray) {
            return $dbList;
        }

        $result = array();
        if ($callback) {

            while ($dbElement = $dbList->GetNextElement()) {

                $arFields = $dbElement->GetFields();
                if ($arFields["ID"] > 0) {

                    $arProperties = $dbElement->GetProperties();
                    $callback($arFields, $arProperties);
                    $result[$arFields["ID"]] = $arFields;
                    $result[$arFields["ID"]]["PROPERTIES"] = $arProperties;
                }
            }
        } else {

            while ($dbElement = $dbList->GetNextElement()) {

                $arFields = $dbElement->GetFields();
                if ($arFields["ID"] > 0) {

                    $arProperties = $dbElement->GetProperties();
                    $result[$arFields["ID"]] = $arFields;
                    $result[$arFields["ID"]]["PROPERTIES"] = $arProperties;
                }
            }
        }

        return (array)$result;
    }

    /**
     * Обновление записи по id
     * @param int $id
     * @param array $arUpdate
     * @return int
     */
    public static function update(int $id, array $arUpdate): int
    {
        $ob = new \CIBlockElement;
        $ob->Update($id, $arUpdate);

        return $id;
    }

    /**
     * Добавляет запись в хранилище
     * @param array $arSave
     * @return int
     */
    public static function add(array $arSave): int
    {
        $ob = new \CIBlockElement;
        $arSave['IBLOCK_ID'] = self::getStoreId();

        return (int)$ob->Add($arSave);
    }

    /**
     * Create or update a record matching the attributes, and fill it with values.
     *
     * @param  array $attributes
     * @param  array $values
     * @param array $propertiesForCreate
     * @return int
     */
    public static function updateOrCreate(array $attributes, array $values = [], array $propertiesForCreate = []): int
    {
        $items = self::get(["filter" => $attributes]);

        if(!empty($items)){

            $item = current($items);

            return self::update($item['ID'], $values);
        }
        else{
            $values = array_merge($attributes, $values);

            $values = array_merge_recursive($values, $propertiesForCreate);

            return self::add($values);
        }
    }

    /**
     * Удаляет запись из хранилища
     * @param int $id
     * @return bool
     */
    public static function delete(int $id): bool
    {
        $ob = new \CIBlockElement;
        return boolval($ob->Delete($id));
    }

    /**
     * Название по id
     * @param int $id
     * @return string
     */
    public static function nameById(int $id): string
    {
        return (string)current(self::get(array("filter" => array("ID" => $id), "select" => array("ID", "NAME"))))["NAME"];
    }

    /**
     * Возвращает поля записи таблицы по id
     * @param int $id
     * @param array $select
     * @return array
     */
    public static function getById(int $id, array $select = array()): array
    {
        $class = get_called_class();
        $query = array("filter" => array("ID" => $id));
        if (!empty($select)) {
            $query["select"] = $select;
        }
        $result = current($class::get($query));
        if (is_array($result) && !empty($result)) {

            return $result;
        } else {

            return array();
        }
    }

    /**
     * @return int
     */
    protected static function getStoreId(): int
    {
        $class = get_called_class();
        $tableId = $class::$storeName . "StoreId";
        return (int)Fields::$tableId();
    }
}
