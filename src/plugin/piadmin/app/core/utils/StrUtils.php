<?php

namespace plugin\piadmin\app\core\utils;

/**
 * 字符串工具类
 */
class StrUtils
{

    /**
     * 格式化字符串
     * @param string $template
     * @param array $data
     * @return string
     */
    public static function format(string $template, array $data): string
    {
        if (empty($data)) {
            return $template;
        }
        if (empty($template)) {
            return '';
        }
        foreach ($data as $key => $value) {
            $template = str_replace('{' . $key . '}', $value, $template);
        }
        return $template;
    }

    /**
     * 安全截取字符串
     * @param string $string
     * @param int $start
     * @param int $length
     * @param string $encoding
     * @return string
     */
    public static function safeSub(string $string, int $start, int $length, string $encoding = 'UTF-8'): string {
        if ($encoding === 'UTF-8') {
            $maxLength = mb_strlen($string, $encoding) - $start;
            $safeLength = min($length, $maxLength);
            return mb_substr($string, $start, $safeLength, $encoding);
        } else {
            $maxLength = strlen($string) - $start;
            $safeLength = min($length, $maxLength);
            return substr($string, $start, $safeLength);
        }
    }
}