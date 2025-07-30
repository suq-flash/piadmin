<?php

namespace plugin\piadmin\app\core\utils;

use think\facade\Db;

/**
 * 数据库工具类
 */
class DbUtils
{
    public static function getCurrentDbConfig()
    {
        $dbConfig = Db::getConfig();
        return $dbConfig['connections'][$dbConfig['default']];
    }
}