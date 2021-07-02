<?php

namespace App\Tests\Util;

use App\Util\NumericUtil;
use Generator;
use PHPUnit\Framework\TestCase;

class NumericUtilTest extends TestCase
{
    /**
     * @dataProvider priceDataset
     */
    public function test_parse_price_parses_a_price_from_different_input_patterns(string $input, ?float $expected): void
    {
        self::assertEquals($expected, NumericUtil::parsePrice($input));
    }

    /**
     * @dataProvider areaDataset
     */
    public function test_parse_area_parses_an_area_from_different_input_patterns(string $input, ?float $expected): void
    {
        self::assertEquals($expected, NumericUtil::parseArea($input));
    }

    /**
     * @dataProvider roomsCountDataset
     */
    public function test_parse_rooms_count_parses_a_room_count_from_different_input_patterns(string $input, ?int $expected): void
    {
        self::assertEquals($expected, NumericUtil::parseRoomsCount($input));
    }

    public function priceDataset(): Generator
    {
        yield ['420000€', 420000.0];
        yield ['<foo>420000€</foo>', 420000.0];
        yield ['foo 420000€ bar', 420000.0];
        yield ['foo 420 000€ bar', 420000.0];
        yield ['foo 420 000€ bar', 420000.0];
        yield ['foo 420 000 € bar', 420000.0];
        yield ['foo 420 000,3€ bar', 420000.3];
        yield ['foo 420 000 euro bar', 420000.0];
        yield ['123m² 420 000€ 456', 420000.0];
        yield ['foo 420 000 bar', null];
    }

    public function areaDataset(): Generator
    {
        yield ['4200m²', 4200.0];
        yield ['<foo>4200m²</foo>', 4200.0];
        yield ['foo 4200m² bar', 4200.0];
        yield ['foo 4 200 m² bar', 4200.0];
        yield ['foo 4 200 m² bar', 4200.0];
        yield ['foo 4 200,3 m² bar', 4200.3];
        yield ['foo 4 200 m2 bar', 4200.0];
        yield ['123€ 4 200 m² 456', 4200.0];
        yield ['foo 4 200 bar', null];
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
