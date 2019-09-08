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
    public static function removeNewLines(string $str): string
    {
        return trim(preg_replace('/\s+/u', '', $str));
    }
}
