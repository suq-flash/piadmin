<?php

namespace plugin\piadmin\app\core\utils;

use app\service\dict\SysDictItemService;
use think\facade\Lang;

class DictUtils
{
    /**
     * 根据字典code获取字典值
     * @param string $dictCode
     * @param string $itemCode
     * @return mixed|null
     */
    public static function getValByCode(string $dictCode, string $itemCode): mixed
    {
        if (empty($dictCode) || empty($itemCode)) {
            return null;
        }
        $dictItems = self::getDictItems($dictCode);
        $itemMap = ArrayUtils::toMap($dictItems, 'code', 'value');
        return $itemMap[$itemCode] ?? null;
    }

    /**
     * 根据字典标签获取字典code
     * @param string $dictCode
     * @param string $itemCode
     * @return mixed|null
     */
    public static function getLabelByCode(string $dictCode, string $itemCode): mixed
    {
        if (empty($dictCode) || empty($itemCode)) {
            return null;
        }
        $dictItems = self::getDictItems($dictCode);
        $itemMap = ArrayUtils::toMap($dictItems, 'code', 'label');
        return $itemMap[$itemCode] ?? null;
    }

    /**
     * 根据字典标签获取字典值
     * @param string $dictCode
     * @param string $itemVal
     * @return mixed|null
     */
    public static function getLabelByVal(string $dictCode, string $itemVal): mixed
    {
        if (empty($dictCode) || empty($itemVal)) {
            return null;
        }
        $dictItems = self::getDictItems($dictCode);
        $itemMap = ArrayUtils::toMap($dictItems, 'value', 'label');
        return $itemMap[$itemVal] ?? null;
    }

    /**
     * 获取指定字典的字典数据
     * @param string $dictCode
     * @return mixed|string|null
     */
    public static function getDictItems(string $dictCode): mixed
    {
        $lang = Lang::getLangSet();
        $cacheKey = "dict:{$dictCode}_{$lang}";
        return CacheUtils::remember($cacheKey, function () use ($dictCode) {
            $dictItemService = app()->make(SysDictItemService::class);
            return $dictItemService->selectList([
                'dict_code' => $dictCode,
                'delete_time' => 0,
                'status' => 1
            ], 'dict_code, code, value, label')->toArray();
        }, 7200);
    }

    /**
     * 清除指定字典的缓存
     * @param string|array $dictCode
     * @return void
     */
    public static function clearDictCache(string|array $dictCode): void
    {
        $lang = Lang::getLangSet();
        if (is_string($dictCode)) {
            $dictCode = [$dictCode];
        }
        foreach ($dictCode as $item) {
            $cacheKey = "dict:{$item}_{$lang}";
            CacheUtils::delete($cacheKey);
        }
    }

    /**
     * 校验字典值是否存在
     * @param string $dictCode
     * @param string $itemValue
     * @return bool
     */
    public static function validateDictValue(string $dictCode, string $itemValue): bool
    {
        $dictItems = self::getDictItems($dictCode);
        $dictValues = ArrayUtils::map($dictItems, 'value');
        return in_array($itemValue, $dictValues);
    }
}