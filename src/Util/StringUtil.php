<?php

namespace App\Util;

use function base64_decode;
use function strtr;

class StringUtil
{
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
