<?php

namespace travelsoft\adapters;

/**
 * Абстрактный класс для работы с хранилищем данных
 */
abstract class Store
{
    abstract public static function get(array $query = array(), bool $likeArray = true, callable $callback = null);

    abstract public static function add(array $arSave): int;

    abstract public static function update(int $id, array $arUpdate): int;

    abstract public static function delete(int $id): bool;

    abstract public static function getById(int $id): array;
}
