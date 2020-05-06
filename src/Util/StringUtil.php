<?php

namespace App\Util;

use function base64_decode;
use function preg_replace;
use function stripos;
use function strtr;

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
     * @param string $str
     * @param bool   $strict
     *
     * @return string
     */
    public static function base64UrlDecode(string $str, bool $strict = false): string
    {
        return base64_decode(str_pad(strtr($str, '-_', '+/'), strlen($str) % 4, '='), $strict);
    }
}
