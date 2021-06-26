<?php

namespace App\Tests\DTO;

use App\DTO\Property;
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
    public function test_property_ad_equals_or_not_another_one(array $input, array $expected): void
    {
        $p = (new Property)
            ->setPrice($input['price'])
            ->setArea($input['area']);

        self::assertEquals($expected['equals'], $this->property->equals($p));
    }

    /**
     * @dataProvider newBuildPropertyDataProvider
     */
    public function test_new_build_property_ad_equals_or_not_another_one(array $input, array $expected): void
    {
        $p = (new Property)
            ->setBuildingName($input['name'])
            ->setNewBuild($input['new_build'])
            ->setPrice($input['price']);

        self::assertEquals($expected['equals'], $this->newBuildProperty->equals($p));
    }

    public function test_equals_returns_false_when_the_price_is_equal_but_also_so_common(): void
    {
        $p1 = (new Property)->setPrice(200000)->setArea(null);
        $p2 = (new Property)->setPrice(200000)->setArea(55);

        self::assertFalse($p1->equals($p2));
    }

    public function propertyDataProvider(): Generator
    {
        yield 'same price, same area' => [
            'input' => [
                'price' => 200456,
                'area' => 55
            ],
            'expected' => [
                'equals' => true
            ]
        ];
        yield 'same price, almost same area' => [
            'input' => [
                'price' => 200456,
                'area' => 54
            ],
            'expected' => [
                'equals' => true
            ]
        ];
        yield 'different price, same area' => [
            'input' => [
                'price' => 200000,
                'area' => 55
            ],
            'expected' => [
                'equals' => false
            ]
        ];
        yield 'same price, different area' => [
            'input' => [
                'price' => 200456,
                'area' => 85
            ],
            'expected' => [
                'equals' => false
            ]
        ];
        yield 'no price, same area' => [
            'input' => [
                'price' => null,
                'area' => 55
            ],
            'expected' => [
                'equals' => false
            ]
        ];
        yield 'same price, no area' => [
            'input' => [
                'price' => 200456,
                'area' => null
            ],
            'expected' => [
                'equals' => true
            ]
        ];
    }

    public function newBuildPropertyDataProvider(): Generator
    {
        yield 'same name, new-build, no price' => [
            'input' => [
                'name' => 'High Gardens',
                'new_build' => true,
                'price' => null
            ],
            'expected' => [
                'equals' => true
            ]
        ];
        yield 'same name, new-build, different price' => [
            'input' => [
                'name' => 'High Gardens',
                'new_build' => true,
                'price' => 300000
            ],
            'expected' => [
                'equals' => true
            ]
        ];
        yield 'same name, not new-build, no price' => [
            'input' => [
                'name' => 'High Gardens',
                'new_build' => false,
                'price' => null
            ],
            'expected' => [
                'equals' => false
            ]
        ];
        yield 'different name, new-build, no price' => [
            'input' => [
                'name' => 'Sea and Sun',
                'new_build' => true,
                'price' => null
            ],
            'expected' => [
                'equals' => false
            ]
        ];
        yield 'no name, new-build, no price' => [
            'input' => [
                'name' => null,
                'new_build' => true,
                'price' => null
            ],
            'expected' => [
                'equals' => false
            ]
        ];
    }
}
