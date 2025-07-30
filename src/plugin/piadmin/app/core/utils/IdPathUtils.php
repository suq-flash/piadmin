<?php

namespace plugin\piadmin\app\core\utils;

/**
 * ID路径工具类
 */
class IdPathUtils
{
    public static function addItem(array|string $idPath, int|string $newId): string
    {
        if (isBlank($idPath)) {
            return (string)$newId;
        }
        return $idPath . ',' . $newId;
    }
}