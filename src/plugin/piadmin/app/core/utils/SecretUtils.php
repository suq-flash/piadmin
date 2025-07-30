<?php

namespace plugin\piadmin\app\core\utils;

/**
 * Class SecretUtils
 * 加密相关工具类
 */
class SecretUtils
{

    /**
     * 生成用户密码
     * @param string $password
     * @param string $salt
     * @return string
     */
    public static function generateUserPassword(string $password, string $salt = ''): string
    {
        if (empty($salt)) {
            $salt = config('secret.user_password_salt', '__123456__');
        }
        return sha1(md5($password . $salt));
    }
}