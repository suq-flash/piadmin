<?php

namespace plugin\piadmin\app\core\utils;

use Firebase\JWT\JWT;
use Webman\Http\Request;

/**
 * JWT工具类
 */
class JwtUtils
{
    /**
     * 创建JWT令牌
     *
     * @param string|int $id 用户唯一标识符，可以是字符串或整数
     * @param string $type 用户类型，默认为'user'，用于获取配置中的密钥和过期时间
     * @param array $payload 自定义负载数据，将合并到令牌的默认声明中
     *
     * @return array 返回包含生成的令牌和过期时间的数组
     */
    public static function createToken(string|int $id, string $type = 'user', array $payload = [])
    {
        // 根据用户类型获取JWT配置中的密钥
        $key = config("jwt.{$type}.key");
        // 根据用户类型获取JWT配置中的过期时间
        $expire = config("jwt.{$type}.expire");
        // 获取当前请求的主机名
        $host = Request::host();
        // 获取当前时间戳
        $now = time();
        // 构造过期时间的字符串表示
        $expireTime = strtolower("+ {$expire} seconds");

        // 向负载数据中添加JWT标准声明
        $payload += [
            'iss' => $host, // 发布者
            'iat' => $now, // 签发时间
            'exp' => strtotime($expireTime), // 过期时间
            'nbf' => $now, // 生效时间
            'aud' => $host, // 接收方
        ];
        // 构造包含用户ID和类型的参数数组
        $params['jti'] = compact('id', 'type');
        // 使用HS256算法编码JWT令牌
        $token = JWT::encode($params, $key, 'HS256');
        // 返回包含生成的令牌和过期时间的信息
        return [
            'token' => $token,
            'expire' => $expire
        ];
    }
}