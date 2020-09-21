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
     * @dataProvider propertyAdDataset
     *
     * @param PropertyAd $ad
     * @param bool       $strict
     * @param bool       $expected
     */
    public function testPropertyAdEqualsOrNotAnOtherOne(PropertyAd $ad, bool $strict, bool $expected): void
    {
        self::assertEquals($expected, $this->propertyAd->equals($ad, $strict));
    }

    /**
     * @dataProvider newBuildPropertyAdDataset
     *
     * @param PropertyAd $ad
     * @param bool       $strict
     * @param bool       $expected
     */
    public function testNewBuildPropertyAdEqualsOrNotAnOtherOne(PropertyAd $ad, bool $strict, bool $expected): void
    {
        self::assertEquals($expected, $this->newBuildPropertyAd->equals($ad, $strict));
    }

    public function testEqualReturnsFalseWhenThePriceIsEqualButSoCommon(): void
    {
        $p1 = (new PropertyAd)->setPrice(200000)->setArea(null)->setProvider('p1');
        $p2 = (new PropertyAd)->setPrice(200000)->setArea(55)->setProvider('p2');

        self::assertFalse($p1->equals($p2));
    }

    public function propertyAdDataset(): Generator
    {
        yield [(new PropertyAd)->setPrice(200456)->setArea(55)->setProvider('p1'), true, true];
        yield [(new PropertyAd)->setPrice(200456)->setArea(55)->setProvider('p2'), true, false];
        yield [(new PropertyAd)->setPrice(200456)->setArea(54)->setProvider('p2'), false, true];
        yield [(new PropertyAd)->setPrice(200000)->setArea(55)->setProvider('p2'), false, false];
        yield [(new PropertyAd)->setPrice(200456)->setArea(85)->setProvider('p2'), false, false];
        yield [(new PropertyAd)->setPrice(null)->setArea(55)->setProvider('p2'), false, false];
        yield [(new PropertyAd)->setPrice(200456)->setArea(null)->setProvider('p2'), false, true];
        yield [(new PropertyAd)->setPrice(200456)->setArea(55)->setProvider('p2')->setNewBuild(true), false, true];
    }

    public function newBuildPropertyAdDataset(): Generator
    {
        yield [(new PropertyAd)->setName('High Gardens')->setNewBuild(true)->setProvider('p1'), true, true];
        yield [(new PropertyAd)->setName('High Gardens')->setNewBuild(true)->setProvider('p2'), true, false];
        yield [(new PropertyAd)->setName('High Gardens')->setNewBuild(false)->setProvider('p2'), false, false];
        yield [(new PropertyAd)->setName('Sea and Sun')->setNewBuild(true)->setProvider('p2'), false, false];
        yield [(new PropertyAd)->setName(null)->setNewBuild(true)->setProvider('p2'), false, false];
    }
}
