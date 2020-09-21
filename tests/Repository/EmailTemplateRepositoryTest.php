<?php

namespace App\Tests\Repository;

use App\DTO\Provider;
use App\Repository\EmailTemplateRepository;
use App\Repository\ProviderRepository;
use Generator;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class EmailTemplateRepositoryTest extends KernelTestCase
{
    use ProphecyTrait;

    /** @var ObjectProphecy|ProviderRepository */
    private $providerRepository;

    private EmailTemplateRepository $emailTemplateRepository;

    public function setUp(): void
    {
        self::bootKernel();

        $emailTemplates = [
            [
                'id' => 'e1_neuf',
                'provider_id' => 'p1_neuf',
                'name' => 'P1',
                'address' => 'p1@mail.com',
                'subject_keyword' => 'exclusivité'
            ],
            [
                'id' => 'e1',
                'provider_id' => 'p1',
                'name' => 'P1',
                'address' => 'p1@mail.com'
            ],
            [
                'id' => 'e2',
                'provider_id' => 'p2',
                'name' => 'P2',
                'address' => 'p2@mail.com'
            ],
        ];

        $this->providerRepository = $this->prophesize(ProviderRepository::class);

        $this->emailTemplateRepository = new EmailTemplateRepository(
            $emailTemplates,
            self::$kernel->getContainer()->get('serializer'),
            $this->providerRepository->reveal()
        );
    }

    /**
     * @dataProvider findDataset
     *
     * @param string      $from
     * @param string      $subject
     * @param string|null $expectedEmailTemplateId
     */
    public function testFindReturnsTheGoodEmailTemplate(string $from, string $subject, ?string $expectedEmailTemplateId): void
    {
        $emailTemplate = $this->emailTemplateRepository->find($from, $subject);

        if (null !== $expectedEmailTemplateId) {
            self::assertEquals($expectedEmailTemplateId, $emailTemplate->getId());
        } else {
            self::assertNull($emailTemplate);
        }
    }

    public function testGetAllAddressesReturnsAllUniqueAddresses(): void
    {
        self::assertEquals(['p1@mail.com', 'p2@mail.com'], array_values($this->emailTemplateRepository->getAllAddresses()));
    }

    public function testGetAddressesByMainProviderReturnsOneUniqueAddress(): void
    {
        $p1 = $this->prophesize(Provider::class);
        $p2 = $this->prophesize(Provider::class);

        // Given
        $p1->getId()->willReturn('p1');
        $p2->getId()->willReturn('p1_neuf');
        $this->providerRepository->getProvidersByMainProvider('p1')->willReturn([$p1->reveal(), $p2->reveal()]);

        self::assertEquals(['p1@mail.com'], $this->emailTemplateRepository->getAddressesByMainProvider('p1'));
    }

    public function findDataset(): Generator
    {
        yield ['P1 <p1@mail.com>', 'Une annonce', 'e1'];
        yield ['P1 <p1@mail.com>', 'Attention exclusivité !', 'e1_neuf'];
        yield ['Unknown', 'Une annonce', null];
    }
}
