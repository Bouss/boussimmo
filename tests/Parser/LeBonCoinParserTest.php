<?php

namespace App\Tests\Parser;

use App\DataProvider\ProviderProvider;
use App\DTO\Provider;
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

    private ObjectProphecy|ProviderProvider $providerProvider;
    private string $projectDir;
    private LeBonCoinParser $parser;

    public function setUp(): void
    {
        self::bootKernel();
        $this->projectDir = self::$kernel->getProjectDir();

        $this->providerProvider = $this->prophesize(ProviderProvider::class);
        $logger = $this->prophesize(LoggerInterface::class);

        $this->parser = new LeBonCoinParser($this->providerProvider->reveal(), $logger->reveal());
    }

    public function test_parse_creates_a_property_from_an_email(): void
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
        self::assertEquals('Maison - 4 pièces - 73.91m²', $p->getAd()->getTitle());
        self::assertEquals(4, $p->getRoomsCount());
        self::assertEquals(73.91, $p->getArea());
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
