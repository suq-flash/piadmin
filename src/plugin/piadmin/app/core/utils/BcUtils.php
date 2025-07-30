<?php

namespace plugin\piadmin\app\core\utils;

/**
 * BcUtils类提供了精确的数学运算方法，使用PHP的BCMath扩展进行高精度计算
 */
class BcUtils
{
    /**
     * 精确加法运算
     *
     * @param string $number1 第一个加数
     * @param string $number2 第二个加数
     * @param int $scale 结果的小数点保留位数，默认为2
     * @return string 返回两个数相加后的结果
     */
    public static function add($number1, $number2, $scale = 2): string
    {
        return bcadd($number1, $number2, $scale);
    }

    /**
     * 精确减法运算
     *
     * @param string $number1 被减数
     * @param string $number2 减数
     * @param int $scale 结果的小数点保留位数，默认为2
     * @return string 返回两个数相减后的结果
     */
    public static function sub($number1, $number2, $scale = 2): string
    {
        return bcsub($number1, $number2, $scale);
    }

    /**
     * 精确乘法运算
     *
     * @param string $number1 第一个乘数
     * @param string $number2 第二个乘数
     * @param int $scale 结果的小数点保留位数，默认为2
     * @return string 返回两个数相乘后的结果
     */
    public static function mul($number1, $number2, $scale = 2): string
    {
        return bcmul($number1, $number2, $scale);
    }

    /**
     * 精确除法运算
     *
     * @param string $number1 被除数
     * @param string $number2 除数
     * @param int $scale 结果的小数点保留位数，默认为2
     * @return string 返回两个数相除后的结果
     */
    public static function div($number1, $number2, $scale = 2): string
    {
        return bcdiv($number1, $number2, $scale);
    }
}
