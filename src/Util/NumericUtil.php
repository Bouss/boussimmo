<?php

namespace App\Util;

class NumericUtil
{
    private const REGEX_INT = '([0-9]+)';
    private const REGEX_FLOAT = '([0-9]+(?:\.[0-9]+)*)';
    private const REGEX_PRICE = self::REGEX_FLOAT . '\s?(?:€|euro)';
    private const REGEX_ROOMS_COUNT = self::REGEX_INT . '\s?pi[e\p{L}]ce';
    private const REGEX_AREA = self::REGEX_FLOAT . '\s?(?:m²|m2)';

    /**
     * @param string $val
     *
     * @return int|null
     */
    public static function extractInt(string $val): ?int
    {
        $val = StringUtil::removeWhitespaces($val);
        preg_match(sprintf('/%s/', self::REGEX_INT), $val, $matches);

        return isset($matches[0]) ? (int) $matches[0] : null;
    }

    /**
     * @param string $val
     *
     * @return float|null
     */
    public static function extractFloat(string $val): ?float
    {
        $val = StringUtil::removeWhitespaces($val);
        $val = str_replace(',', '.', $val);
        preg_match(sprintf('/%s/', self::REGEX_FLOAT), $val, $matches);

        return isset($matches[0]) ? (float) $matches[0] : null;
    }

    /**
     * @param string $val
     *
     * @return float|null
     */
    public static function extractPrice(string $val): ?float
    {
        $val = StringUtil::removeWhitespaces($val);
        $val = str_replace([',', '.'], ['.', ''], $val);
        preg_match(sprintf('/%s/ui', self::REGEX_PRICE), $val, $matches);

        return isset($matches[1]) ? (float) $matches[1] : null;
    }

    /**
     * @param string $val
     *
     * @return int|null
     */
    public static function extractRoomsCount(string $val): ?int
    {
        preg_match(sprintf('/%s/ui', self::REGEX_ROOMS_COUNT), $val, $matches);

        return isset($matches[1]) ? (int) $matches[1] : null;
    }

    /**
     * @param string $val
     *
     * @return float|null
     */
    public static function extractArea(string $val): ?float
    {
        $val = str_replace(',', '.', $val);
        preg_match(sprintf('/%s/ui', self::REGEX_AREA), $val, $matches);

        return isset($matches[1]) ? (float) $matches[1] : null;
    }
}
