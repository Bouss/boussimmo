<?php

namespace App\Tests\DataProvider;

use App\DTO\Provider;
use App\DataProvider\EmailTemplateProvider;
use App\DataProvider\ProviderProvider;
use Generator;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class EmailTemplateProviderTest extends KernelTestCase
{
    use ProphecyTrait;

    /** @var ObjectProphecy|ProviderProvider */
    private $providerProvider;

    private EmailTemplateProvider $emailTemplateProvider;

    public function setUp(): void
    {
        self::bootKernel();

        $emailTemplates = [
            [
                'name' => 'e1_neuf',
                'provider_name' => 'p1_neuf',
                'username' => 'P1',
                'address' => 'p1@mail.com',
                'subject_keyword' => 'exclusivité'
            ],
            [
                'name' => 'e1',
                'provider_name' => 'p1',
                'username' => 'P1',
                'address' => 'p1@mail.com'
            ],
            [
                'name' => 'e2',
                'provider_name' => 'p2',
                'username' => 'P2',
                'address' => 'p2@mail.com'
            ],
        ];

        $this->providerProvider = $this->prophesize(ProviderProvider::class);

        $this->emailTemplateProvider = new EmailTemplateProvider(
            $emailTemplates,
            self::$kernel->getContainer()->get('serializer'),
            $this->providerProvider->reveal()
        );
    }

    /**
     * @dataProvider findDataset
     */
    public function testFindReturnsTheGoodEmailTemplate(string $from, string $subject, ?string $expectedEmailTemplateId): void
    {
        $emailTemplate = $this->emailTemplateProvider->find($from, $subject);

        if (null !== $expectedEmailTemplateId) {
            self::assertEquals($expectedEmailTemplateId, $emailTemplate->getName());
        } else {
            self::assertNull($emailTemplate);
        }
    }

    public function testGetAllAddressesReturnsAllUniqueAddresses(): void
    {
        self::assertEquals(['p1@mail.com', 'p2@mail.com'], array_values($this->emailTemplateProvider->getAllAddresses()));
    }

    public function testGetAddressesByMainProviderReturnsOneUniqueAddress(): void
    {
        $p1 = $this->prophesize(Provider::class);
        $p2 = $this->prophesize(Provider::class);

        // Given
        $p1->getName()->willReturn('p1');
        $p2->getName()->willReturn('p1_neuf');
        $this->providerProvider->getProvidersByMainProvider('p1')->willReturn([$p1->reveal(), $p2->reveal()]);

        self::assertEquals(['p1@mail.com'], $this->emailTemplateProvider->getAddressesByMainProvider('p1'));
    }

    public function findDataset(): Generator
    {
        yield ['P1 <p1@mail.com>', 'Une annonce', 'e1'];
        yield ['P1 <p1@mail.com>', 'Attention exclusivité !', 'e1_neuf'];
        yield ['Unknown', 'Une annonce', null];
    }
}
