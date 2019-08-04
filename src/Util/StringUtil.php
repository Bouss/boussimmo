<?php

namespace App\Util;

class StringUtil
{
    /**
     * @param string $str
     * @param array  $words
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
}
