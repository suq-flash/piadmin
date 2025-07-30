<?php

namespace app\core\utils;

use app\service\lang\LangPackService;
use think\facade\Lang;
use think\facade\Log;

class LangUtils
{

    /**
     * 获取多语言数据
     * @param string|int $code 语言code
     * @param array $data 模版字符串参数
     * @param string $lang 语言标识
     * @return int|string
     */
    public static function get(string|int $code, array $data = [], string $lang = '')
    {
        try {
            $langPackService = app()->make(LangPackService::class);
            if (empty($lang)) {
                $lang = Lang::getLangSet();
            }
            // 缓存key
            $cacheKey = 'lang_pack:' . $lang;
            $langPackageData = CacheUtils::remember($cacheKey, function () use ($langPackService,  $lang) {
                return $langPackService->getColumn([
                    'lang' => $lang,
                    'delete_time' => 0
                ], 'lang_value', 'lang_key');
            }, 3600);
            $message = $langPackageData[$code] ?? 'Parse Code Error';

            // 填充
            return StrUtils::format($message, $data);
        } catch (\Throwable $e) {
            Log::error('获取语言code：' . $code . '发成错误，错误原因是：' . json_encode([
                    'file' => $e->getFile(),
                    'message' => $e->getMessage(),
                    'line' => $e->getLine()
                ]));
            return $code;
        }
    }

    /**
     * 清除指定语言的包缓存
     *
     * 该方法用于删除缓存中指定语言的语言包信息如果未指定语言，将使用系统当前的语言设置
     * 主要用于在语言变更或更新语言包后，清除旧的缓存信息，以确保最新的语言信息被加载
     *
     * @param string $lang 可选参数指定要清除缓存的语言如果未提供，默认使用当前系统语言
     * @return void
     */
    public static function clearPackCache(string $lang = ''): void
    {
        // 如果未提供语言参数或提供的语言参数为空，则使用系统当前的语言设置
        if (empty($lang)) {
            $lang = Lang::getLangSet();
        }

        // 删除指定语言的语言包缓存
        CacheUtils::delete('lang_pack:' . $lang);
    }
}