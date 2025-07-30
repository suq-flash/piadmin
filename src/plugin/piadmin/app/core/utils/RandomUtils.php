<?php

namespace plugin\piadmin\app\core\utils;

/**
 * 随机工具类
 */
class RandomUtils
{
    /**
     * 随机打乱数组
     * @param \DateTime $dateTime
     * @param array $srcArray
     * @return array
     */
    public static function randomShuffleArray(\DateTime $dateTime, array $srcArray): array
    {
        // 将日期转换为字符串并计算哈希值
        $dateStr = $dateTime->format("Y-m-d");
        $hashValue = md5($dateStr);

        // 使用哈希值作为随机种子
        $seed = hexdec(substr($hashValue, 0, 8));
        srand($seed);

        // Fisher-Yates 洗牌算法打乱数组
        $n = count($srcArray);
        for ($i = $n - 1; $i > 0; $i--) {
            $j = rand(0, $i);
            $temp = $srcArray[$i];
            $srcArray[$i] = $srcArray[$j];
            $srcArray[$j] = $temp;
        }
        return $srcArray;
    }

    /**
     * 生成随机种子
     * @param string $seedStr
     * @return string
     */
    public static function generateRandomSeed(string $seedStr): string
    {
        // SHA256哈希计算
        $hash = hash('sha256', $seedStr);
        // 取前8位十六进制并转换为整数
        $hex_part = substr($hash, 0, 8);
        return hexdec($hex_part);
    }
}