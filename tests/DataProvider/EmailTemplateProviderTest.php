<?php

namespace App\Tests\DataProvider;

use App\DataProvider\EmailTemplateProvider;
use App\DataProvider\ProviderProvider;
use App\DTO\Provider;
use Generator;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class EmailTemplateProviderTest extends KernelTestCase
{
    use ProphecyTrait;

    private ObjectProphecy|ProviderProvider $providerProvider;
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
            self::$container->get('serializer'),
            $this->providerProvider->reveal()
        );
    }

    /**
     * @dataProvider findDataset
     */
    public function test_find_returns_the_good_email_template(array $input, array $expected): void
    {
        $emailTemplate = $this->emailTemplateProvider->find($input['from'], $input['subject']);

        if (null !== $expectedEmailTemplateName = $expected['email_template_name']) {
            self::assertEquals($expectedEmailTemplateName, $emailTemplate->getName());
        } else {
            self::assertNull($emailTemplate);
        }
    }

    public function test_get_all_addresses_returns_all_unique_addresses(): void
    {
        self::assertEquals(['p1@mail.com', 'p2@mail.com'], array_values($this->emailTemplateProvider->getAllAddresses()));
    }

    public function test_get_addresses_by_main_provider_returns_one_unique_address(): void
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
        yield 'no particular keywords' => [
            'input' => [
                'from' => 'P1 <p1@mail.com>',
                'subject' => 'Une annonce',
            ],
            'expected' => [
                'email_template_name' => 'e1'
            ]
        ];
        yield 'particular keywords' => [
            'input' => [
                'from' => 'P1 <p1@mail.com>',
                'subject' => 'Attention exclusivité !',
            ],
            'expected' => [
                'email_template_name' => 'e1_neuf'
            ]
        ];
        yield 'unknown provider' => [
            'input' => [
                'from' => 'Unknown',
                'subject' => 'Une annonce',
            ],
            'expected' => [
                'email_template_name' => null
            ]
        ];
    }
}
