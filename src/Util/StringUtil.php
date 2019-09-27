<?php

namespace App\Util;

class StringUtil
{
    /**
     * @param string   $str
     * @param string[] $words
     *
     * @return bool
     */
    public static function contains(string $str, array $words): bool
    {
        foreach ($words as $word) {
            if (false !== stripos($str, $word)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $str
     *
     * @return string
     */
    public static function removeWhitespaces(string $str): string
    {
        return trim(preg_replace('/\s+/u', '', $str));
    }

    /**
     * @param string $data
     * @param bool   $strict
     *
     * @return string
     */
    public static function base64UrlDecode(string $data, bool $strict = false): string
    {
        return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '='), $strict);
    }
}
