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
     */
    public function test_parse_parses_numeric_values_from_different_inputs_patterns(string $input, float|int $expected): void
    {
        self::assertEquals($expected, $this->formatter->parse($input));
    }

    /**
     * @dataProvider priceDataset
     */
    public function test_parse_price_parses_a_price_from_different_input_patterns(string $input, ?float $expected): void
    {
        self::assertEquals($expected, $this->formatter->parsePrice($input));
    }

    /**
     * @dataProvider areaDataset
     */
    public function test_parse_area_parses_an_area_from_different_input_patterns(string $input, ?float $expected): void
    {
        self::assertEquals($expected, $this->formatter->parseArea($input));
    }

    /**
     * @dataProvider roomsCountDataset
     */
    public function test_parse_rooms_count_parses_a_room_count_from_different_input_patterns(string $input, ?int $expected): void
    {
        self::assertEquals($expected, $this->formatter->parseRoomsCount($input));
    }

    public function numericDataset(): Generator
    {
        yield ['420000', 420000];
        yield ['420 000', 420000];
        yield ['420 000,3', 420000.3];
    }

    public function priceDataset(): Generator
    {
        yield ['420000€', 420000.0];
        yield ['<foo>420000€</foo>', 420000.0];
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
        yield ['42m²', 42.0];
        yield ['<foo>42m²</foo>', 42.0];
        yield ['foo 42m² bar', 42.0];
        yield ['foo 42 m² bar', 42.0];
        yield ['foo 42,3 m² bar', 42.3];
        yield ['foo 42 m2 bar', 42.0];
        yield ['123€ 42 m² 456', 42.0];
        yield ['foo 42 bar', null];
    }

    public function roomsCountDataset(): Generator
    {
        yield ['4p', 4];
        yield ['T4', 4];
        yield ['F4', 4];
        yield ['<foo>4p</foo>', 4];
        yield ['Type 4', 4];
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
