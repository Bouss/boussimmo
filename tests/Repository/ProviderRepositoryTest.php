<?php

namespace App\Tests\Repository;

use App\Repository\ProviderRepository;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ProviderRepositoryTest extends KernelTestCase
{
    use ProphecyTrait;

    private ProviderRepository $providerRepository;

    public function setUp(): void
    {
        self::bootKernel();

        $providers = [
            [
                'id' => 'p1',
                'logo' => 'p1.png'
            ],
            [
                'id' => 'p2',
                'logo' => 'p2.png'
            ],
            [
                'id' => 'p1_neuf',
                'logo' => 'p1_neuf.png',
                'parent_provider' => 'p1',
            ]
        ];

        $this->providerRepository = new ProviderRepository($providers, self::$kernel->getContainer()->get('serializer'));
    }

    public function testGetProvidersByMainProviderReturnMasterAndChildProviders(): void
    {
        $providers = array_values($this->providerRepository->getProvidersByMainProvider('p1'));

        self::assertCount(2, $providers);
        self::assertEquals('p1', $providers[0]->getId());
        self::assertEquals('p1_neuf', $providers[1]->getId());
    }
}
