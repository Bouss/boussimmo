<?php

namespace App\Tests\Formatter;

use App\Formatter\DecimalFormatter;
use Generator;
use PHPUnit\Framework\TestCase;

class DecimalFormatterTest extends TestCase
{
    private DecimalFormatter $formatter;

    public function setUp(): void
    {
        $this->formatter = new DecimalFormatter();
    }

    /**
     * @dataProvider numericDataset
     *
     * @param string    $input
     * @param int|float $numeric
     */
    public function testParseParsesNumericValuesFromDifferentInputsPatterns(string $input, $numeric): void
    {
        self::assertEquals($numeric, $this->formatter->parse($input));
    }

    /**
     * @dataProvider priceDataset
     *
     * @param string     $input
     * @param float|null $price
     */
    public function testParsePriceParsesAPriceFromDifferentInputPatterns(string $input, ?float $price): void
    {
        self::assertEquals($price, $this->formatter->parsePrice($input));
    }

    /**
     * @dataProvider areaDataset
     *
     * @param string     $input
     * @param float|null $area
     */
    public function testParseAreaParsesAnAreaFromDifferentInputPatterns(string $input, ?float $area): void
    {
        self::assertEquals($area, $this->formatter->parseArea($input));
    }

    /**
     * @dataProvider roomsCountDataset
     *
     * @param string   $input
     * @param int|null $roomsCount
     */
    public function testParseRoomsCountParsesAnRoomsCountFromDifferentInputPatterns(string $input, ?int $roomsCount): void
    {
        self::assertEquals($roomsCount, $this->formatter->parseRoomsCount($input));
    }

    public function numericDataset(): Generator
    {
        yield ['420000', 420000];
        yield ['420 000', 420000];
        yield ['420 000,3', 420000.3];
    }

    public function priceDataset(): Generator
    {
        yield ['foo 420000€ bar', 420000.0];
        yield ['foo 420 000€ bar', 420000.0];
        yield ['foo 420 000 € bar', 420000.0];
        yield ['foo 420 000,3€ bar', 420000.3];
        yield ['foo 420 000 euro bar', 420000.0];
        yield ['123m² 420 000€ 456', 420000.0];
        yield ['foo 420 000 bar', null];
    }

    public function areaDataset(): Generator
    {
        yield ['foo 42m² bar', 42.0];
        yield ['foo 42 m² bar', 42.0];
        yield ['foo 42,3 m² bar', 42.3];
        yield ['foo 42 m2 bar', 42.0];
        yield ['123€ 42 m² 456', 42.0];
        yield ['foo 42 bar', null];
    }

    public function roomsCountDataset(): Generator
    {
        yield ['foo 4p bar', 4];
        yield ['foo 4 p bar', 4];
        yield ['foo 4 p. bar', 4];
        yield ['foo 4 pièces bar', 4];
        yield ['foo 4 piece bar', 4];
        yield ['foo F4 bar', 4];
        yield ['foo T4 bar', 4];
        yield ['foo TYPE 4 bar', 4];
        yield ['foo 4 places', null];
    }
}
