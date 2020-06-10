<?php

namespace App\Formatter;

use NumberFormatter;
use function preg_match;

class DecimalFormatter
{
    private const REGEX_INT = '([0-9])+';
    private const REGEX_FLOAT = '([0-9]+(?:\s?[0-9]{3})*(?:,[0-9]+)?)+';
    private const REGEX_PRICE = self::REGEX_FLOAT . '\s?(?:€|euro)';
    private const REGEX_AREA = self::REGEX_FLOAT . '\s?(?:m²|m2)';
    private const REGEX_ROOMS_COUNT = self::REGEX_INT . '(?:\spi[e\p{L}]ce|\s?p.)|(?:T(?:YPE\s)?|F)' . self::REGEX_INT;

    private NumberFormatter $formatter;

    public function __construct()
    {
        $this->formatter = new NumberFormatter('fr_FR', NumberFormatter::DECIMAL);
    }

    /**
     * @param string $value
     *
     * @return float
     */
    public function parse(string $value): float
    {
        return $this->formatter->parse($value);
    }

    /**
     * @param string $value
     *
     * @return float|null
     */
    public function parsePrice(string $value): ?float
    {
        if (preg_match(sprintf('/%s/ui', self::REGEX_PRICE), $value, $matches)) {
            return $this->formatter->parse($matches[1]);
        }

        return null;
    }

    /**
     * @param string $value
     *
     * @return float|null
     */
    public function parseArea(string $value): ?float
    {
        if (preg_match(sprintf('/%s/ui', self::REGEX_AREA), $value, $matches)) {
            return $this->formatter->parse($matches[1]);
        }

        return null;
    }

    /**
     * @param string $value
     *
     * @return int|null
     */
    public function parseRoomsCount(string $value): ?int
    {
        preg_match(sprintf('/%s/ui', self::REGEX_ROOMS_COUNT), $value, $matches);

        if (!empty($matches[1])) {
            return $this->formatter->parse($matches[1]);
        }
        if (isset($matches[2])) {
            return $this->formatter->parse($matches[2]);
        }

        return null;
    }
}
