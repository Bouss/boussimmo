<?php

namespace App\Util;

use function str_replace;

class NumericUtil
{
    private const REGEX_START         = '(?:^|\s|>)';
    private const REGEX_END           = '(?:$|\s|\.|<)';
    private const REGEX_INT           = '((?:(?:\d{1,3}\s)+\d{3})|\d+)';
    private const REGEX_FLOAT         = '((?:(?:(?:\d{1,3}\s)+\d{3})|\d+)(?:(?:\.|,)\d+)?)';
    private const REGEX_PRICE         = self::REGEX_START . self::REGEX_FLOAT . '\s?(?:€|euros?)' . self::REGEX_END;
    private const REGEX_AREA          = self::REGEX_START . self::REGEX_FLOAT . '\s?(?:m²|m2)' . self::REGEX_END;
    private const REGEX_ROOMS_COUNT   = self::REGEX_START . self::REGEX_INT . '\s?(?:pi[e\p{L}]ce(?:s|\(s\))?|p)' . self::REGEX_END;
    private const REGEX_ROOMS_COUNT_2 = self::REGEX_START . '(?:T|F|type\s?)' . self::REGEX_INT . self::REGEX_END;

    public static function parsePrice(string $value): ?float
    {
        if (1 === preg_match(sprintf('/%s/ui', self::REGEX_PRICE), $value, $matches)) {
            return (float) str_replace([' ', ' ', ','], ['', '', '.'], $matches[1]);
        }

        return null;
    }

    public static function parseArea(string $value): ?float
    {
        if (1 === preg_match(sprintf('/%s/ui', self::REGEX_AREA), $value, $matches)) {
            return (float) str_replace([' ', ' ', ','], ['', '', '.'], $matches[1]);
        }

        return null;
    }

    public static function parseRoomsCount(string $value): ?int
    {
        if (1 === preg_match(sprintf('/%s/ui', self::REGEX_ROOMS_COUNT), $value, $matches)) {
            return (int) $matches[1];
        }

        if (1 === preg_match(sprintf('/%s/ui', self::REGEX_ROOMS_COUNT_2), $value, $matches)) {
            return (int) $matches[1];
        }

        return null;
    }
}
