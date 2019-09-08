<?php

namespace App\Util;

class NumericUtil
{
    private const REGEX_INT_CAPTURE = '/([0-9]+)/';
    private const REGEX_FLOAT_CAPTURE = '/([0-9]+(?:\.[0-9]+)*)/';

    /**
     * @param string $val
     *
     * @return int
     */
    public static function extractInt(string $val): int
    {
        $val = StringUtil::removeNewLines($val);
        preg_match(self::REGEX_INT_CAPTURE, $val, $matches);

        return (int) $matches[0];
    }

    /**
     * @param string $val
     *
     * @return float
     */
    public static function extractFloat(string $val): float
    {
        $val = StringUtil::removeNewLines($val);
        $val = str_replace(',', '.', $val);
        preg_match(self::REGEX_FLOAT_CAPTURE, $val, $matches);

        return (float) $matches[0];
    }
}
