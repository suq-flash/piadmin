<?php

namespace plugin\piadmin\app\core\utils;

/**
 * 树形工具类
 */
class TreeUtils
{
    /**
     * 转换成树形结构
     * @param array $data
     * @param string $childName
     * @param string $keyName
     * @param string $pidName
     * @return array
     */
    public static function toTree(array $data, string $childName = 'children', string $keyName = 'id', string $pidName = 'pid')
    {
        $list = array();
        foreach ($data as $value) {
            $list[$value[$keyName]] = $value;
        }
        $tree = array(); //格式化好的树
        foreach ($list as $item) {
            if (isset($list[$item[$pidName]])) {
                $list[$item[$pidName]][$childName][] = &$list[$item[$keyName]];
            } else {
                $tree[] = &$list[$item[$keyName]];
            }
        }
        return $tree;
    }
}