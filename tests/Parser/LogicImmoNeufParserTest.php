<?php

namespace App\Tests\Parser;

use App\DTO\Provider;
use App\Formatter\DecimalFormatter;
use App\Parser\BienIciParser;
use App\Parser\LeBonCoinParser;
use App\Parser\LogicImmoNeufParser;
use App\Parser\LogicImmoParser;
use App\Repository\ProviderRepository;
use DateTime;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class LogicImmoNeufParserTest extends KernelTestCase
{
    use ProphecyTrait;

    /** @var ObjectProphecy|ProviderRepository */
    private $providerRepository;

    /** @var ObjectProphecy|LoggerInterface */
    private $logger;

    private string $projectDir;
    private LogicImmoNeufParser $parser;

    public function setUp(): void
    {
        self::bootKernel();
        $this->projectDir = self::$kernel->getProjectDir();

        $this->providerRepository = $this->prophesize(ProviderRepository::class);
        $this->logger = $this->prophesize(LoggerInterface::class);

        $this->parser = new LogicImmoNeufParser(
            $this->providerRepository->reveal(),
            new DecimalFormatter(),
            $this->logger->reveal()
        );
    }

    public function testParseCreatesAPropertyAdFromAnEmail(): void
    {
        $provider = $this->prophesize(Provider::class);

        // Given
        $provider->isNewBuildOnly()->willReturn(true);
        $this->providerRepository->find(Argument::any())->willReturn($provider->reveal());
        $html = file_get_contents($this->projectDir . '/tests/data/logic_immo_neuf.html','r');

        // When
        $propertyAds = $this->parser->parse($html, [], ['date' => new DateTime('2020-01-01 12:00:00')]);

        // Then
        self::assertCount(1, $propertyAds);
        $p = $propertyAds[0];
        self::assertEquals('Aulneo', $p->getName());
        self::assertEquals(198835, $p->getPrice());
        self::assertEquals('Nantes (44300)', $p->getLocation());
        self::assertEquals('logic_immo_neuf', $p->getProvider());
        self::assertTrue($p->isNewBuild());
        self::assertNotNull($p->getUrl());
        self::assertNotNull($p->getPhoto());
        self::assertNull($p->getTitle());
        self::assertNull($p->getDescription());
        self::assertNull($p->getRoomsCount());
        self::assertNull($p->getArea());
    }
}
