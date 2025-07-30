<?php

namespace plugin\piadmin\app\core\utils;

/**
 * 请求相关工具类
 */
class RequestUtils
{

    const SORT_RULES = ['asc', 'desc'];
    const SORT_FIELDS = ['create_time'];

    /**
     * 获取本次请求的分页参数
     * @return array [page, limit]
     */
    public static function getPageParameter(): array
    {
        $params = request()->only([
            config('settings.paging.page.name') => config('settings.paging.page.default'),
            config('settings.paging.limit.name') => config('settings.paging.limit.default'),
        ]);
        return array_values($params);
    }

    /**
     * 获取本次请求的排序参数
     * @return array
     */
    public static function getSortParameter(): array
    {
        $params = request()->only([
            config('settings.paging.sort_rule.name') => config('settings.paging.sort_rule.default'),
            config('settings.paging.sort_field.name') => config('settings.paging.sort_field.default'),
        ]);
        if (!in_array($params[config('settings.paging.sort_rule.name')], config('settings.paging.sort_rule.allow'))) {
            $params[config('settings.paging.sort_rule.name')] = config('settings.paging.sort_rule.default');
        }
        if (!in_array($params[config('settings.paging.sort_field.name')], config('settings.paging.sort_field.allow'))) {
            $params[config('settings.paging.sort_field.name')] = config('zailiu.paging.sort_field.default');
        }
        return array_values($params);
    }
}