<?php

namespace App\Util;

class NumericUtil
{
    private const REGEX_INT = '/([0-9]+)/';
    private const REGEX_FLOAT = '/([0-9]+(?:\.[0-9]+)*)/';
    private const REGEX_ROOMS_COUNT = '/([0-9]+)\spièces/u';
    private const REGEX_AREA = '/([0-9]+)\s?(?:m²|m2)/u';

    /**
     * @param string $val
     *
     * @return int
     */
    public static function extractInt(string $val): int
    {
        $val = StringUtil::removeWhitespaces($val);
        preg_match(self::REGEX_INT, $val, $matches);

        return (int) $matches[0];
    }

    /**
     * @param string $val
     *
     * @return float
     */
    public static function extractFloat(string $val): float
    {
        $val = StringUtil::removeWhitespaces($val);
        $val = str_replace(',', '.', $val);
        preg_match(self::REGEX_FLOAT, $val, $matches);

        return (float) $matches[0];
    }

    /**
     * @param string $val
     *
     * @return int|null
     */
    public static function extractRoomsCount(string $val): ?int
    {
        preg_match(self::REGEX_ROOMS_COUNT, $val, $matches);

        return isset($matches[1]) ? (int) $matches[1] : null;
    }

    /**
     * @param string $val
     *
     * @return float|null
     */
    public static function extractArea(string $val): ?float
    {
        preg_match(self::REGEX_AREA, $val, $matches);

        return isset($matches[1]) ? (float) $matches[1] : null;
    }
}
