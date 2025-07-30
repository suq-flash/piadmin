<?php

namespace plugin\piadmin\app\core\utils;

use think\facade\Cache;

/**
 * 缓存工具类
 */
class CacheUtils
{
    /**
     * 过期时间
     * @var int
     */
    protected static $expire;

    /**
     * 写入缓存
     * @param string $name 缓存名称
     * @param mixed $value 缓存值
     * @param int|null $expire 缓存时间，为0读取系统缓存时间
     */
    public static function set(string $name, $value, int $expire = 0, string $tag = ''): bool
    {
        try {
            if (empty($tag)) {
                return Cache::set($name, $value, $expire);
            }
            return Cache::tag($tag)->set($name, $value, $expire);
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * 如果不存在则写入缓存
     * @param string $name
     * @param mixed $default
     * @param int|null $expire
     * @param string $tag
     * @return mixed|string|null
     */
    public static function remember(string $name, $default = '', int $expire = 0, string $tag = ''): mixed
    {
        try {
            if (empty($tag)) {
                return Cache::remember($name, $default, $expire);
            }
            return Cache::tag($tag)->remember($name, $default, $expire);
        } catch (\Throwable $e) {
            try {
                if (is_callable($default)) {
                    return $default();
                } else {
                    return $default;
                }
            } catch (\Throwable $e) {
                return null;
            }
        }
    }

    /**
     * 读取缓存
     * @param string $name
     * @param mixed $default
     * @return mixed|string
     */
    public static function get(string $name, $default = null): mixed
    {
        return Cache::get($name) ?? $default;
    }

    /**
     * 删除缓存
     * @param string $name
     * @return bool
     */
    public static function delete(string $name): bool
    {
        return Cache::delete($name);
    }

    /**
     * 清空缓存池
     * @return bool
     */
    public static function clear(string $tag = '')
    {
        if (empty($tag)) {
            return Cache::clear();
        }
        return Cache::tag($tag)->clear();
    }

    /**
     * 清空全部缓存
     * @return bool
     */
    public static function clearAll(): bool
    {
        return Cache::clear();
    }

    /**
     * 检查缓存是否存在
     * @param string $key
     * @return bool
     */
    public static function has(string $key)
    {
        try {
            return Cache::has($key);
        } catch (\Throwable $e) {
            return false;
        }
    }
}