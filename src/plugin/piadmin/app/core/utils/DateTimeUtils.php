<?php

namespace plugin\piadmin\app\core\utils;

use plugin\piadmin\app\exception\FrameworkException;
use DateInterval;
use DateTime;
use DateTimeZone;
use support\Log;
use Webman\Http\Request;

/**
 * 时间日期工具类
 */
class DateTimeUtils
{

    /**
     * 获取当前时间的毫秒数
     *
     * 此函数用于获取当前时间的毫秒数，用于需要高精度时间测量的场景
     * 例如性能分析、日志记录等
     *
     * @return float 当前时间的毫秒数
     */
    public static function getMillisecond(): float
    {
        // 获取当前的微秒时间和秒时间
        list($msec, $sec) = explode(' ', microtime());

        // 将微秒时间和秒时间转换为浮点数，并计算总毫秒数
        // 使用 sprintf 函数进行格式化，确保结果为整数部分，然后转换为浮点数返回
        return (float)sprintf('%.0f', (floatval($msec) + floatval($sec)) * 1000);
    }

    /**
     * 获取指定时间戳对应的时区日期时间
     * @param int $timestamp 时间戳
     * @param string $timezone 时区
     * @throws \Exception
     */
    public static function getTimezoneDate(int $timestamp, string $timezone = ''): DateTime
    {
        try {
            $timezone = $timezone ?: date_default_timezone_get();
            return (new DateTime("@{$timestamp}"))->setTimezone(new DateTimeZone($timezone));
        } catch (\Exception $e) {
            Log::error("时间日期解析失败: {$e->getMessage()} 参数: timestamp: {$timestamp} timezone: {$timezone}");
            throw new FrameWorkException(2000007);
        }

    }

    /**
     * 获取当前时区
     * @return string
     */
    public static function getTimezone(): string
    {
        $date_default_timezone_get = date_default_timezone_get();
        return Request::param('timezone') ?? $date_default_timezone_get;
    }

    /**
     * 检查日期字符串是否为有效的日期时间字符串
     * @param $dateStr
     * @param $format
     * @return bool
     */
    public static function isValidDateTime(string $dateStr, string $format = 'Y-m-d'): bool
    {
        $date = DateTime::createFromFormat($format, $dateStr);
        return $date != false;
    }

    public static function beginTimestamp(string $dateStr): ?int
    {
        if (empty($dateStr)) {
            return null;
        }
        try {
            $dateTime = new \DateTime($dateStr);
            $dateTime->setTime(0, 0, 0);
            return $dateTime->getTimestamp();
        } catch (\Exception $e) {
            return null;
        }
    }

    public static function endTimestamp(string $dateStr): ?int
    {
        if (empty($dateStr)) {
            return null;
        }
        try {
            $dateTime = new \DateTime($dateStr);
            $dateTime->setTime(23, 59, 59);
            return $dateTime->getTimestamp();
        } catch (\Exception $e) {
            return null;
        }
    }

    public static function beginTime(string|DateTime $dateStr): ?string
    {
        if (empty($dateStr)) {
            return null;
        }
        try {
            if (!($dateStr instanceof DateTime)) {
                $dateTime = new \DateTime($dateStr);
            } else {
                $dateTime = $dateStr;
            }
            $dateTime->setTime(0, 0, 0);
            return $dateTime->format('Y-m-d H:i:s');
        } catch (\Exception $e) {
            return null;
        }
    }

    public static function endTime(string|DateTime $dateStr): ?string
    {
        if (empty($dateStr)) {
            return null;
        }
        try {
            if (!($dateStr instanceof DateTime)) {
                $dateTime = new \DateTime($dateStr);
            } else {
                $dateTime = $dateStr;
            }
            $dateTime->setTime(23, 59, 59);
            return $dateTime->format('Y-m-d H:i:s');
        } catch (\Exception $e) {
            return null;
        }
    }

    public static function begin(string|DateTime $dateStr): ?DateTime
    {
        if (empty($dateStr)) {
            return null;
        }
        try {
            if (!($dateStr instanceof DateTime)) {
                $dateTime = new \DateTime($dateStr);
            } else {
                $dateTime = $dateStr;
            }
            $dateTime->setTime(0, 0, 0);
            return $dateTime;
        } catch (\Exception $e) {
            return null;
        }
    }

    public static function end(string|DateTime $dateStr): ?DateTime
    {
        if (empty($dateStr)) {
            return null;
        }
        try {
            if (!($dateStr instanceof DateTime)) {
                $dateTime = new \DateTime($dateStr);
            } else {
                $dateTime = $dateStr;
            }
            $dateTime->setTime(23, 59, 59);
            return $dateTime;
        } catch (\Exception $e) {
            return null;
        }
    }

    public static function generateDateRange(string $startDate, string $endDate, string $format = 'Y-m-d'): array {
        $dates = [];
        $current = new \DateTime($startDate);
        $end = new \DateTime($endDate);

        // 确保结束日期 >= 开始日期
        if ($current > $end) {
            return [];
        }

        // 循环生成每一天
        while ($current <= $end) {
            $dates[] = $current->format($format);
            $current->add(new DateInterval('P1D'));  // 增加 1 天
        }

        return $dates;
    }

    /**
     * 是否为秒级时间戳
     * @param $timestamp
     * @return bool
     */
    public static function isSecondTimestamp($timestamp): bool
    {
        // 10位数字通常是秒级时间戳
        return is_numeric($timestamp) && strlen((string)$timestamp) === 10;
    }

    /**
     * 是否为毫秒级时间戳
     * @param $timestamp
     * @return bool
     */
    public static function isMilliTimestamp($timestamp): bool
    {
        // 10位数字通常是秒级时间戳
        return is_numeric($timestamp) && strlen((string)$timestamp) === 13;
    }

    public static function toDateStr(mixed $val, string $default='--', string $format = 'Y-m-d'): string
    {
        if (empty($val)) {
            return $default;
        }
        $dateTime = null;
        try {
            if (!($val instanceof DateTime)) {
                $dateTime = new \DateTime($val);
            } else {
                $dateTime = $val;
            }
            $year = $dateTime->format('Y');
            if ($year < 2010) {
                return $default;
            }
            return $dateTime->format($format);
        } catch (\Exception $e) {
            return $default;
        }
    }
}