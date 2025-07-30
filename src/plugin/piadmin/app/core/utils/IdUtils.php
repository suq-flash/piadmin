<?php

namespace plugin\piadmin\app\core\utils;

use Ramsey\Uuid\Uuid;

/**
 * ID工具类
 */
class IdUtils
{
    /**
     * 生成UUID字符串
     *
     * 该函数使用UUID版本7生成唯一的标识符，并移除其中的连字符
     *
     * @return string 返回不包含连字符的UUID字符串
     */
    public static function uuid(): string
    {
        $uuid = Uuid::uuid7();
        return str_replace('-', '', $uuid->toString());
    }
}
