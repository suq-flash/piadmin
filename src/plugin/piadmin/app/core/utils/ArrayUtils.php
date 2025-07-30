<?php

namespace plugin\piadmin\app\core\utils;

use think\Collection;
use think\helper\Str;

/**
 * 数组相关工具类
 */
class ArrayUtils
{
    /**
     * 将逗号分隔的参数转换为数组
     * @param $params
     * @return array
     */
    public static function paramsToArray($params, $ignoreZero = false): array
    {
        if ($ignoreZero && $params == 0) {
            return [];
        }
        if (is_null($params)) {
            return [];
        }
        if (is_int($params)) {
            return [$params];
        }
        if (is_array($params)) {
            return $params;
        }
        if (strlen($params) == 0) {
            return [];
        }

        if (is_string($params)) {
            return explode(',', $params);
        }
        return [];
    }

    /**
     * 提取二维关联数组中的某一列作为新数组
     * @param mixed $data 二维数组
     * @param string $key 列名
     * @param bool $unique 结果去重 默认去重
     * @return array 新数组
     */
    public static function map(mixed $data, string $key, bool $unique = true): array
    {
        if ($data instanceof Collection) {
            $data = $data->toArray();
        }
        if (empty($data)) {
            return [];
        }
        $cols = array_column($data, $key);
        return $unique ? array_unique($cols) : $cols;
    }

    /**
     * 将二维数组按某一列的值转为map，
     * @param mixed $data 二维数组
     * @param string $key map键的列名
     * @param string|null $hKey 可选，map值中的键名，为空则整个数组元素作为值
     * @return array
     */
    public static function toMap(mixed $data, string $key, string $hKey = null): array
    {
        if ($data instanceof Collection) {
            $data = $data->all();
        }
        if (empty($data)) {
            return [];
        }
        $result = [];
        foreach ($data as $item) {
            if ($hKey !== null && isset($item[$hKey])) {
                // 如果提供了 $hKey 并且数组中存在该键，则使用该键的值作为map的值
                $result[$item[$key]] = $item[$hKey];
            } else {
                // 否则，使用整个数组元素作为map的值
                $result[$item[$key]] = $item;
            }
        }
        return $result;
    }

    /**
     * 将二维数组按某一列分组并提取结果中的某一列
     * @param mixed $data 二维数组
     * @param string $groupKey 分组列
     * @param string $mapKey 提取列
     * @param bool $unique 结果去重 默认去重
     * @return array
     */
    public static function groupAndMap($data, string $groupKey, string $mapKey, bool $unique = true)
    {
        if ($data instanceof Collection) {
            $data = $data->toArray();
        }
        if (empty($data)) {
            return [];
        }
        $result = [];
        foreach ($data as $item) {
            if (!isset($result[$item[$groupKey]])) {
                $result[$item[$groupKey]] = [];
            }
            $result[$item[$groupKey]][] = $item[$mapKey];
        }
        // 去重
        if ($unique) {
            foreach ($result as &$values) {
                $values = array_unique($values);
            }
        }
        return $result;
    }

    /**
     * 将二维数组按某一列分组并提取结果中的某一列
     * @param mixed $data 二维数组
     * @param string $groupKey 分组列
     * @return array
     */
    public static function group($data, string $groupKey)
    {
        if ($data instanceof Collection) {
            if ($data->isEmpty()) {
                return [];
            }
        }
        if (empty($data)) {
            return [];
        }
        $result = [];
        foreach ($data as $item) {
            if (!isset($result[$item[$groupKey]])) {
                $result[$item[$groupKey]] = [];
            }
            $result[$item[$groupKey]][] = $item;
        }
        return $result;
    }


    /**
     * 根据指定的键和值列表过滤数据数组
     *
     * @param array $data 要过滤的数据数组
     * @param string $filterKey 用于过滤的键名
     * @param array $values 允许的值列表
     *
     * @return array 过滤后的数据数组
     */
    public static function filter($data, string $filterKey, array $values): array
    {
        // 初始化结果数组
        $result = [];

        // 遍历数据数组
        foreach ($data as $d) {
            // 如果当前项的指定键的值在允许的值列表中，则将该项添加到结果数组中
            if (in_array($d[$filterKey], $values)) {
                $result[] = $d;
            }
        }

        // 返回过滤后的结果数组
        return $result;
    }

    /**
     * 二维关联数组根据某个int类型的字段排序 默认倒序
     * @param array $data
     * @param string $sortKey
     * @param bool $isDesc
     * @return array
     */
    public static function sortByInt(array $data, string $sortKey, bool $isDesc = true): array
    {
        if (empty($data)) {
            return $data;
        }
        if ($isDesc) {
            usort($data, function ($a, $b) use ($sortKey) {
                if ($a[$sortKey] == $b[$sortKey]) {
                    return 0;
                }
                return ($a[$sortKey] > $b[$sortKey]) ? -1 : 1;
            });
        } else {
            usort($data, function ($a, $b) use ($sortKey) {
                return $a['score'] <=> $b['score'];
            });
        }
        return $data;
    }

    /**
     * 拼装多对多关联数据
     * @param $mainData
     * @param $subData
     * @param $associationData
     * @return array
     */
    public static function combineManyToManyAssociationsData($mainData, $subData, $associationData): array
    {
        $main = $mainData['data'];
        $sub = $subData['data'];
        $association = $associationData['data'];
        $result = [];
        foreach ($main as $data) {
            $data[$mainData['join_data_key']] = [];
            $result[$data[$mainData['main_key']]] = $data;
        }

        $sub = array_column($sub, null, $subData['sub_key']);

        foreach ($association as $item) {
            if (isset($sub[$item[$associationData['sub_key']]]) && isset($result[$item[$associationData['main_key']]])) {
                $result[$item[$associationData['main_key']]][$mainData['join_data_key']][] = $sub[$item[$associationData['sub_key']]];
            }
        }
        return $result;
    }

    /**
     * 是否为普通数组 true普通数组 false关联数组
     * @param $array
     * @return bool
     */
    public static function isArrayIndexed($array): bool
    {
        return array_diff(array_keys($array), range(0, count($array) - 1)) === array();
    }

    /**
     * 从关联数组中获取多个value
     * @param $data
     * @param $keys
     * @return array
     */
    public static function getMany($data, $keys): array
    {
        if (empty($data) || empty($keys)) {
            return [];
        }

        // 获取多个键对应的值
        return array_values(array_intersect_key($data, array_flip($keys)));
    }

    /**
     * 使用第一个数组中的值替换第二个数组中相同键的值
     *
     * @param array $array1 用于替换的数组
     * @param array $array2 被替换的数组
     *
     * @return array 替换后的数组
     */
    public static function replaceValues(array $array1, array $array2): array
    {
        foreach ($array1 as $key => $value) {
            if (array_key_exists($key, $array2)) {
                $array2[$key] = $value;
            }
        }

        // 返回替换后的数组
        return $array2;
    }

    /**
     * 过滤数组中的非空元素(不包括0)
     *
     * 该方法用于接收一个数组，并返回其中不含空值的元素子集空值的定义遵循 PHP 的空值规则，
     * 包括 NULL、空字符串、0、false 等
     *
     * @param array $data 需要进行过滤的数组
     * @return array 过滤后的数组，仅包含非空元素
     */
    public static function filterNotEmpty(array $data): array
    {
        // 如果传入的数组为空，则直接返回原数组，不做处理
        if (empty($data)) {
            return $data;
        }
        // 使用 array_filter 函数配合自定义回调函数来过滤数组中的非空元素
        // 回调函数判断每个元素是否不为空，并返回布尔值，array_filter 根据回调函数的返回值决定是否保留该元素
        return array_filter($data, function ($var) {
            return $var !== '' && $var !== null && $var !== false && $var !== [];
        });
    }

    /**
     * 从数组中随机选取指定数量的元素
     * @param array $sourceArray 源数组
     * @param int $selectCount 需要选取的数量
     * @return array 随机选取的元素数组
     */
    public static function getRandomElements(array $sourceArray, int $selectCount): array
    {
        // 边界条件处理
        if ($selectCount <= 0 || empty($sourceArray)) {
            return [];
        }
        $arrayLength = count($sourceArray);
        $selectCount = min($selectCount, $arrayLength); // 确保不超过数组长度

        // 随机打乱数组顺序
        shuffle($sourceArray);
        // 截取前N个元素
        return array_slice($sourceArray, 0, $selectCount);
    }

    public static function keysToSnake($param)
    {
        if (empty($param)) {
            return $param;
        }
        $delKey = [];
        foreach ($param as $key => &$value) {
            $newKey = Str::snake($key);
            if ($newKey != $key) {
                $delKey[] = $key;
            }
            if (is_array($value)) {
                $value = self::keysToSnake($value);
            }
            $param[$newKey] = $value;
        }
        foreach ($delKey as $key) {
            unset($param[$key]);
        }
        return $param;
    }
}
