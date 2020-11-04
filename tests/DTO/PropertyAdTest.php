<?php

namespace App\Tests\DTO;

use App\DTO\PropertyAd;
use Generator;
use PHPUnit\Framework\TestCase;

class PropertyAdTest extends TestCase
{
    private PropertyAd $propertyAd;
    private PropertyAd $newBuildPropertyAd;

    public function setUp(): void
    {
        $this->propertyAd = (new PropertyAd)
            ->setPrice(200456)
            ->setArea(55)
            ->setProvider('p1');

        $this->newBuildPropertyAd = (new PropertyAd)
            ->setName('High Gardens')
            ->setNewBuild(true)
            ->setProvider('p1');
    }

    /**
     * @dataProvider propertyAdDataProvider
     *
     * @param float|null $price
     * @param float|null $area
     * @param string $provider
     * @param bool $strict
     * @param bool $expected
     */
    public function testPropertyAdEqualsOrNotAnOtherOne(?float $price, ?float $area, string $provider, bool $strict, bool $expected): void
    {
        $p = (new PropertyAd)->setPrice($price)->setArea($area)->setProvider($provider);

        self::assertEquals($expected, $this->propertyAd->equals($p, $strict));
    }

    /**
     * @dataProvider newBuildPropertyAdDataProvider
     *
     * @param String|null $name
     * @param bool $newBuild
     * @param string $provider
     * @param float|null $price
     * @param bool $strict
     * @param bool $expected
     */
    public function testNewBuildPropertyAdEqualsOrNotAnOtherOne(?String $name, bool $newBuild, string $provider, ?float $price, bool $strict, bool $expected): void
    {
        $p = (new PropertyAd)->setName($name)->setNewBuild($newBuild)->setProvider($provider)->setPrice($price);

        self::assertEquals($expected, $this->newBuildPropertyAd->equals($p, $strict));
    }

    public function testEqualReturnsFalseWhenThePriceIsEqualButSoCommon(): void
    {
        $p1 = (new PropertyAd)->setPrice(200000)->setArea(null)->setProvider('p1');
        $p2 = (new PropertyAd)->setPrice(200000)->setArea(55)->setProvider('p2');

        self::assertFalse($p1->equals($p2));
    }

    public function propertyAdDataProvider(): Generator
    {
        yield [200456, 55, 'p1', true, true];
        yield [200456, 55, 'p2', true, false];
        yield [200456, 54, 'p2', false, true];
        yield [200000, 55, 'p2', false, false];
        yield [200456, 85, 'p2', false, false];
        yield [null, 55, 'p2', false, false];
        yield [200456, null, 'p2', false, true];
        yield [200456, 55, 'p2', true, false, true];
    }

    public function newBuildPropertyAdDataProvider(): Generator
    {
        yield ['High Gardens', true, 'p1', null, true, true];
        yield ['High Gardens', true, 'p2', null, true, false];
        yield ['High Gardens', true, 'p2', 300000, true, false];
        yield ['High Gardens', false, 'p2', null, false, false];
        yield ['Sea and Sun', true, 'p2', null, false, false];
        yield [null, true, 'p2', null, false, false];
    }
}
