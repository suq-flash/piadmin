<?php

namespace plugin\piadmin\app\core\utils;

use InvalidArgumentException;
use RuntimeException;
use support\Log;

class CurlUtils
{
    /**
     * 发起HTTP请求
     *
     * @param string $url 请求的URL
     * @param string $method HTTP方法（GET、POST、PUT、DELETE等）
     * @param array $headers 请求头数组
     * @param array $data 请求数据数组
     * @param int $timeout 请求超时时间（秒）
     * @param bool $isJson 是否以JSON格式发送数据
     *
     * @return string|array 响应内容
     * @throws \Exception
     */
    public static function request(string $url, string $method, array $headers = [], array $data = [],
                                   int $timeout = 10, bool $isJson = true): string|array
    {

        if (empty($url) || !filter_var($url, FILTER_VALIDATE_URL)) {
            throw new InvalidArgumentException('Invalid URL provided.');
        }
        // 初始化
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);

        if (!empty($headers)) {
            // 设置header
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        // 设置超时时间
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout); // 整个请求的最大执行时间
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5); // 连接超时时间

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        // 根据 method 设置不同的选项
        switch (strtoupper($method)) {
            case 'GET':
                if (!empty($data)) {
                    // 检查 URL 是否已经包含查询参数
                    $query = http_build_query($data);
                    if (!str_contains($url, '?')) {
                        $url .= '?' . $query;
                    } else {
                        $url .= '&' . $query;
                    }
                }
                break;
            case 'POST':
                curl_setopt($ch, CURLOPT_POST, true);
                if ($isJson) {
                    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                } else {
                    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
                }
                break;
            case 'PUT':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
                break;
            case 'DELETE':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
                break;
            default:
                throw new InvalidArgumentException('Unsupported HTTP method: ' . $method);
        }

        // 执行请求
        $data = curl_exec($ch);
        try {
            if ($data === false) {
                $errno = curl_errno($ch);
                $error = curl_error($ch);
                Log::error("Curl error: [$url] - $errno: $error");
                throw new RuntimeException("Curl error: $errno: $error");
            }
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if ($httpCode >= 400) {
                Log::warning("HTTP error: [$url] - $httpCode");
                throw new RuntimeException("HTTP error: $httpCode");
            }
            if (self::isValidJson($data)) {
                return json_decode($data, true);
            }
            return $data;
        } catch (\Exception $exception) {
            Log::error("Curl request failed: [$url] - " . $exception->getMessage());
            throw $exception;
        } finally {
            curl_close($ch);
        }
    }

    /**
     * 发起GET请求并获取响应结果
     *
     * 该方法主要用于发起GET类型的HTTP请求，并接收请求的响应结果
     * 它允许通过传递URL、请求头、请求数据和超时设置来定制请求
     *
     * @param string $url 请求的URL地址，不能为空
     * @param array $headers 请求头数组，用于发送额外的HTTP头信息，默认为空数组
     * @param array $data 请求数据数组，用于发送额外的数据，默认为空数组
     * @param int $timeout 请求超时时间，单位为秒，默认为10秒
     *
     * @return string|array 返回请求的响应结果，可能是字符串或数组
     */
    public static function get(string $url, array $headers = [], array $data = [], int $timeout = 10): string|array
    {
        return self::request($url, 'GET', $headers, $data, $timeout);
    }

    /**
     * 发起POST请求并返回响应结果
     *
     * 该方法主要用于向指定URL发送POST请求，并根据参数设置请求头、请求体和超时时间
     * 它是一个静态方法，可以在不实例化类的情况下直接调用
     *
     * @param string $url 请求的URL地址，不能为空
     * @param array $headers 请求头数组，用于设置HTTP请求的头部信息，默认为空数组
     * @param array $data 请求体数据数组，用于设置POST请求的主体内容，默认为空数组
     * @param int $timeout 请求超时时间，单位为秒，默认为10秒
     * @param bool $isJson 指示请求体数据是否应被格式化为JSON，默认为true
     *
     * @return string|array 返回响应结果，可能是字符串或数组，具体类型取决于请求的响应
     */
    public static function post(string $url, array $headers = [], array|string $data = [], int $timeout = 10, bool $isJson = true): string|array
    {
        return self::request($url, 'POST', $headers, $data, $timeout, $isJson);
    }

    /**
     * 判断字符串是否是有效的JSON格式
     *
     * @param string $string 要检查的字符串
     *
     * @return bool 如果字符串是有效的JSON格式，返回true；否则返回false
     */
    private static function isValidJson(string $string): bool
    {
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }
}