<?php

namespace App\Tests\Parser;

use App\DTO\Provider;
use App\Formatter\DecimalFormatter;
use App\Parser\BienIciParser;
use App\Parser\LeBonCoinParser;
use App\Parser\LogicImmoParser;
use App\Parser\LogicImmoPartnerParser;
use App\Repository\ProviderRepository;
use DateTime;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class LogicImmoPartnerParserTest extends KernelTestCase
{
    use ProphecyTrait;

    /** @var ObjectProphecy|ProviderRepository */
    private $providerRepository;

    /** @var ObjectProphecy|LoggerInterface */
    private $logger;

    private string $projectDir;
    private LogicImmoPartnerParser $parser;

    public function setUp(): void
    {
        self::bootKernel();
        $this->projectDir = self::$kernel->getProjectDir();

        $this->providerRepository = $this->prophesize(ProviderRepository::class);
        $this->logger = $this->prophesize(LoggerInterface::class);

        $this->parser = new LogicImmoPartnerParser(
            $this->providerRepository->reveal(),
            new DecimalFormatter(),
            $this->logger->reveal()
        );
    }

    public function testParseCreatesAPropertyAdFromAnEmail(): void
    {
        $provider = $this->prophesize(Provider::class);

        // Given
        $provider->isNewBuildOnly()->willReturn(false);
        $this->providerRepository->find(Argument::any())->willReturn($provider->reveal());
        $html = file_get_contents($this->projectDir . '/tests/data/logic_immo_partner.html','r');

        // When
        $propertyAds = $this->parser->parse($html, [], ['date' => new DateTime('2020-01-01 12:00:00')]);

        // Then
        self::assertCount(1, $propertyAds);
        $p = $propertyAds[0];
        self::assertEquals(215250, $p->getPrice());
        self::assertEquals('NANTES (44300)', $p->getLocation());
        self::assertEquals(43, $p->getArea());
        self::assertEquals(2, $p->getRoomsCount());
        self::assertEquals('logic_immo', $p->getProvider());
        self::assertFalse($p->isNewBuild());
        self::assertNotNull($p->getUrl());
        self::assertNotNull($p->getPhoto());
        self::assertNotNull($p->getDescription());
        self::assertNull($p->getTitle());
        self::assertNull($p->getName());
    }
}
