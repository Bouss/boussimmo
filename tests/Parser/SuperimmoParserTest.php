<?php

namespace App\Tests\Parser;

use App\DTO\Provider;
use App\Formatter\DecimalFormatter;
use App\Parser\BienIciParser;
use App\Parser\LeBonCoinParser;
use App\Parser\LogicImmoParser;
use App\Parser\LogicImmoPartnerParser;
use App\Parser\OuestFranceImmo2Parser;
use App\Parser\OuestFranceImmoNeufParser;
use App\Parser\OuestFranceImmoParser;
use App\Parser\PapParser;
use App\Parser\SeLogerParser;
use App\Parser\SuperimmoParser;
use App\Repository\ProviderRepository;
use DateTime;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class SuperimmoParserTest extends KernelTestCase
{
    use ProphecyTrait;

    /** @var ObjectProphecy|ProviderRepository */
    private $providerRepository;

    /** @var ObjectProphecy|LoggerInterface */
    private $logger;

    private string $projectDir;
    private SuperimmoParser $parser;

    public function setUp(): void
    {
        self::bootKernel();
        $this->projectDir = self::$kernel->getProjectDir();

        $this->providerRepository = $this->prophesize(ProviderRepository::class);
        $this->logger = $this->prophesize(LoggerInterface::class);

        $this->parser = new SuperimmoParser(
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
        $html = file_get_contents($this->projectDir . '/tests/data/superimmo.html','r');

        // When
        $propertyAds = $this->parser->parse($html, [], ['date' => new DateTime('2020-01-01 12:00:00')]);

        // Then
        self::assertCount(1, $propertyAds);
        $p = $propertyAds[0];
        self::assertEquals(72.55, $p->getArea());
        self::assertEquals('Nantes (44100)', $p->getLocation());
        self::assertEquals(193000, $p->getPrice());
        self::assertEquals('superimmo', $p->getProvider());
        self::assertFalse($p->isNewBuild());
        self::assertNotNull($p->getTitle());
        self::assertNotNull($p->getDescription());
        self::assertNotNull($p->getUrl());
        self::assertNotNull($p->getPhoto());
        self::assertNull($p->getRoomsCount());
        self::assertNull($p->getName());
    }
}
