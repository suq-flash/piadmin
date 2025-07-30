<?php

namespace app\core\utils;

use plugin\piadmin\app\exception\ApiException;
use plugin\piadmin\app\exception\FrameworkException;
use app\validate\system\CaptchaValidate;
use think\facade\Lang;

/**
 * 验证码工具类
 */
class CaptchaUtils
{

    public static function sendPhoneCaptcha(string $mobile, string $prefix, string $type): string
    {
        // 检查是否可以发送
        $cacheKey = "captcha:sms:{$type}:{$prefix}{$mobile}";
        $banCacheKey = "{$cacheKey}_ban";
        if (CacheUtils::has($banCacheKey)) {
            throw new ApiException('验证码发送太频繁啦 稍后再试吧');
        }
        $captchaConfig = config("settings.captcha.sms");
        $captchaStr = self::getCaptchaStr('sms', $type, $mobile);
        CacheUtils::set($cacheKey, $captchaStr, $captchaConfig['expire'] ?? 300);
        CacheUtils::set($banCacheKey, $captchaStr, $captchaConfig['ban_expire '] ?? 60);
        if (!env('APP_DEBUG')) {
            $sendSms = self::sendSms($captchaStr, $prefix , $mobile);
        }
        return $captchaStr;
    }

    /**
     * 发送邮箱验证码
     * @throws FrameWorkException|ApiException
     */
    public static function sendEmailCaptcha(string $email, string $type): string
    {
        // 检查是否可以发送
        $cacheKey = "captcha:email:{$type}:{$email}";
        $banCacheKey = "{$cacheKey}_ban";
        if (CacheUtils::has($banCacheKey)) {
            throw new ApiException('验证码发送太频繁啦 稍后再试吧');
        }
        $captchaStr = self::getCaptchaStr('email', $type, $email);
        $captchaConfig = config("settings.captcha.email");
        $sendEmail = self::sendEmail($captchaStr, $email);
        CacheUtils::set($cacheKey, $captchaStr, $captchaConfig['expire'] ?? 300);
        CacheUtils::set($banCacheKey, $captchaStr, $captchaConfig['ban_expire'] ?? 60);
        return $captchaStr;
    }

    /**
     * 验证邮箱验证码
     * @param string $email 邮箱
     * @param string $type 验证码类型
     * @param string $captcha 验证码
     * @return bool
     * @throws ApiException
     */
    public static function verifyEmailCaptcha(string $email, string $type, string $captcha): bool
    {
        $cacheKey = "captcha:email:{$type}:{$email}";
        $captchaStr = CacheUtils::get($cacheKey);
        if (empty($captchaStr) || $captchaStr !== $captcha) {
            throw new ApiException('验证码有误');
        }
        CacheUtils::delete($cacheKey);
        return true;
    }

    /**
     * 验证短信验证码
     * @param string $prefix
     * @param string $account
     * @param string $type
     * @param string $code
     * @return bool
     * @throws ApiException
     */
    public static function verifySmsCaptcha(string $prefix, string $account, string $type, string $code): bool
    {
        $cacheKey = "captcha:sms:{$type}:{$prefix}{$account}";
        $captchaStr = CacheUtils::get($cacheKey);
        if (empty($captchaStr) || $captchaStr !== $code) {
            throw new ApiException('验证码有误');
        }
        CacheUtils::delete($cacheKey);
        return true;
    }


    // ============================================================ 私有方法 ===============================================

    /**
     * 获取验证码
     * @return string
     */
    private static function getCaptchaStr(string $from, string $type, string $code): string
    {
        $str = null;
        $strPol = "1234567890";
        $max = strlen($strPol) - 1;

        $captchaConfig = config("settings.captcha.{$from}");
        if (empty($captchaConfig)) {
            throw new FrameWorkException('未找到验证码配置: {type}', 400, ['type' => $from]);
        }
        $length = $captchaConfig['length'] ?? 6;
        for ($i = 0; $i < $length; $i++) {
            $str .= $strPol[rand(0, $max)];
        }

        // 存入缓存
        $cacheKey = "captcha:{$from}:{$type}:{$code}";
        CacheUtils::set($cacheKey, $str, $captchaConfig['expire'] ?? 300);
        // 重新发送的时间
        $banCacheKey = "captcha:{$from}:{$type}:{$code}_ban";
        CacheUtils::set($banCacheKey, $str, $captchaConfig['ban_expire'] ?? 60);
        return $str;
    }

    /**
     * 发送邮件
     * @param string $captcha
     * @param string $email
     * @return array|string
     */
    private static function sendEmail(string $captcha, string $email): array|string
    {
        $lang = Lang::getLangSet();
        validate(CaptchaValidate::class)->scene('sendEmail')->check(['email' => $email]);
        $url = config("settings.captcha.base_url") . config("settings.captcha.email_message_url");
        $url = $url . '?' . http_build_query(config("settings.captcha.get_params"));
        $param = [
            'email' => $email,
            'template_id' => config("settings.captcha.email.template_code.{$lang}"),
            'template_param' => json_encode([
                'code' => $captcha,
                'expire' => config("settings.captcha.email.expire") / 60
            ]),
        ];
        return CurlUtils::post($url, [], $param);
    }

    /**
     * 发送短信
     * @param string $captcha
     * @param string $mobile
     * @return array|string
     */
    private static function sendSms(string $captcha, string $prefix, string $mobile): array|string
    {
        $lang = Lang::getLangSet();
        validate(CaptchaValidate::class)->scene('sendSms')->check(['mobile' => $mobile]);
        $url = config("settings.captcha.base_url") . config("settings.captcha.phone_message_url");
        $url = $url . '?' . http_build_query(config("settings.captcha.get_params"));
        $param = [
            'phone' => $mobile,
            'phone_prefix' => $prefix,
            'template_id' => config("settings.captcha.sms.template_code.{$lang}"),
            'template_param' => json_encode([
                'code' => $captcha,
                'expire' => config("settings.captcha.sms.expire") / 60
            ]),
        ];
        return CurlUtils::post($url, [], $param);
    }


}