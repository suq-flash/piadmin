<?php

namespace plugin\piadmin\app\core\utils;

/**
 * url工具类
 */
class UrlUtils
{
    /**
     * 获取主域名
     * @param string $url
     * @return string|null
     */
    public static function getPrimaryDomain($url): ?string
    {
        $host = parse_url($url, PHP_URL_HOST);
        if (!$host) return null;

        $parts = explode('.', $host);
        $count = count($parts);

        // 处理双后缀域名（如.com.cn）
        if ($count >= 3 && in_array($parts[$count - 2], ['com', 'net', 'org', 'edu', 'gov'])) {
            return $parts[$count - 3] . '.' . $parts[$count - 2] . '.' . $parts[$count - 1];
        }
        return $parts[$count - 2] . '.' . $parts[$count - 1];
    }
}