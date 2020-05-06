<?php

namespace App\Util;

use function preg_match;
use function str_replace;

class NumericUtil
{
    private const REGEX_INT = '([0-9]+)';
    private const REGEX_FLOAT = '([0-9]+(?:,[0-9]+)*)';
    private const REGEX_PRICE = self::REGEX_FLOAT . '\s?(?:€|euro)';
    private const REGEX_ROOMS_COUNT = self::REGEX_INT . '(?:\spi[e\p{L}]ce|\s?p.)|(?:T|F)' . self::REGEX_INT;
    private const REGEX_AREA = self::REGEX_FLOAT . '\s?(?:m²|m2)';

    /**
     * @param string $val
     *
     * @return float|null
     */
    public static function extractPrice(string $val): ?float
    {
        $val = StringUtil::removeWhitespaces($val);
        preg_match(sprintf('/%s/ui', self::REGEX_PRICE), $val, $matches);

        if (!isset($matches[1])) {
            return null;
        }

        return (float) str_replace(',', '.', $matches[1]);
    }

    /**
     * @param string $val
     *
     * @return int|null
     */
    public static function extractRoomsCount(string $val): ?int
    {
        preg_match(sprintf('/%s/ui', self::REGEX_ROOMS_COUNT), $val, $matches);

        if (!empty($matches[1])) {
            return (int) $matches[1];
        }
        if (!empty($matches[2])) {
            return (int) $matches[2];
        }

        return null;
    }

    /**
     * @param string $val
     *
     * @return float|null
     */
    public static function extractArea(string $val): ?float
    {
        $val = StringUtil::removeWhitespaces($val);
        preg_match(sprintf('/%s/ui', self::REGEX_AREA), $val, $matches);

        if (!isset($matches[1])) {
            return null;
        }

        return (float) str_replace(',', '.', $matches[1]);
    }
}
