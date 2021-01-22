<?php

namespace App\Tests\DataProvider;

use App\DataProvider\ProviderProvider;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ProviderProviderTest extends KernelTestCase
{
    use ProphecyTrait;

    private ProviderProvider $providerProvider;

    public function setUp(): void
    {
        self::bootKernel();

        $providers = [
            [
                'name' => 'p1',
                'logo' => 'p1.png'
            ],
            [
                'name' => 'p2',
                'logo' => 'p2.png'
            ],
            [
                'name' => 'p1_neuf',
                'logo' => 'p1_neuf.png',
                'parent' => 'p1',
            ]
        ];

        $this->providerProvider = new ProviderProvider($providers, self::$container->get('serializer'));
    }

    public function testGetProvidersByMainProviderReturnMasterAndChildProviders(): void
    {
        $providers = array_values($this->providerProvider->getProvidersByMainProvider('p1'));

        self::assertCount(2, $providers);
        self::assertEquals('p1', $providers[0]->getName());
        self::assertEquals('p1_neuf', $providers[1]->getName());
    }
}
