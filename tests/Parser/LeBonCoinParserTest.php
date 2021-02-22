<?php

namespace App\Tests\Parser;

use App\DataProvider\ProviderProvider;
use App\DTO\Provider;
use App\Formatter\DecimalFormatter;
use App\Parser\LeBonCoinParser;
use DateTime;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class LeBonCoinParserTest extends KernelTestCase
{
    use ProphecyTrait;

    /** @var ObjectProphecy|ProviderProvider */
    private $providerProvider;

    /** @var ObjectProphecy|LoggerInterface */
    private $logger;

    private string $projectDir;
    private LeBonCoinParser $parser;

    public function setUp(): void
    {
        self::bootKernel();
        $this->projectDir = self::$kernel->getProjectDir();

        $this->providerProvider = $this->prophesize(ProviderProvider::class);
        $this->logger = $this->prophesize(LoggerInterface::class);

        $this->parser = new LeBonCoinParser($this->providerProvider->reveal(), new DecimalFormatter(), $this->logger->reveal());
    }

    public function testParseCreatesAPropertyFromAnEmail(): void
    {
        $provider = $this->prophesize(Provider::class);

        // Given
        $provider->isNewBuildOnly()->willReturn(false);
        $this->providerProvider->find(Argument::any())->willReturn($provider->reveal());
        $html = file_get_contents($this->projectDir . '/tests/data/leboncoin.html','r');

        // When
        $properties = $this->parser->parse($html, [], ['date' => new DateTime('2020-01-01 12:00:00')]);

        // Then
        self::assertCount(1, $properties);
        $p = $properties[0];
        self::assertEquals('Appartement 3 pièces 58 m²', $p->getAd()->getTitle());
        self::assertEquals(3, $p->getRoomsCount());
        self::assertEquals(58, $p->getArea());
        self::assertEquals('Bouguenais 44340', $p->getLocation());
        self::assertEquals(221550, $p->getPrice());
        self::assertEquals('leboncoin', $p->getAd()->getProvider());
        self::assertFalse($p->isNewBuild());
        self::assertNotNull($p->getAd()->getUrl());
        self::assertNotNull($p->getAd()->getPhoto());
        self::assertNull($p->getBuildingName());
        self::assertNull($p->getAd()->getDescription());
    }
}
