<?php

namespace App\Tests\DTO;

use App\DTO\Property;
use App\DTO\PropertyAd;
use Generator;
use PHPUnit\Framework\TestCase;

class PropertyTest extends TestCase
{
    private Property $property;
    private Property $newBuildProperty;

    public function setUp(): void
    {
        $this->property = (new Property)
            ->setPrice(200456)
            ->setArea(55);

        $this->newBuildProperty = (new Property)
            ->setBuildingName('High Gardens')
            ->setNewBuild(true);
    }

    /**
     * @dataProvider propertyDataProvider
     */
    public function testPropertyAdEqualsOrNotAnOtherOne(?float $price, ?float $area, bool $expected): void
    {
        $p = (new Property)->setPrice($price)->setArea($area);

        self::assertEquals($expected, $this->property->equals($p));
    }

    /**
     * @dataProvider newBuildPropertyDataProvider
     */
    public function testNewBuildPropertyAdEqualsOrNotAnOtherOne(?String $name, bool $newBuild, ?float $price, bool $expected): void
    {
        $p = (new Property)->setBuildingName($name)->setNewBuild($newBuild)->setPrice($price);

        self::assertEquals($expected, $this->newBuildProperty->equals($p));
    }

    public function testEqualReturnsFalseWhenThePriceIsEqualButSoCommon(): void
    {
        $p1 = (new Property)->setPrice(200000)->setArea(null);
        $p2 = (new Property)->setPrice(200000)->setArea(55);

        self::assertFalse($p1->equals($p2));
    }

    public function propertyDataProvider(): Generator
    {
        yield [200456, 55, true];
        yield [200456, 54, true];
        yield [200000, 55, false];
        yield [200456, 85, false];
        yield [null, 55, false];
        yield [200456, null, true];
    }

    public function newBuildPropertyDataProvider(): Generator
    {
        yield ['High Gardens', true, null, true];
        yield ['High Gardens', true, 300000, true];
        yield ['High Gardens', false, null, false];
        yield ['Sea and Sun', true, null, false];
        yield [null, true, null, false];
    }
}
