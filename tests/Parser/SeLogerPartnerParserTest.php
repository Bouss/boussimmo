<?php

namespace App\Tests\Parser;

use App\DataProvider\ProviderProvider;
use App\DTO\Provider;
use App\Parser\SeLogerPartnerParser;
use DateTime;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class SeLogerPartnerParserTest extends KernelTestCase
{
    use ProphecyTrait;

    private ObjectProphecy|ProviderProvider $providerProvider;
    private string $projectDir;
    private SeLogerPartnerParser $parser;

    public function setUp(): void
    {
        self::bootKernel();
        $this->projectDir = self::$kernel->getProjectDir();

        $this->providerProvider = $this->prophesize(ProviderProvider::class);
        $logger = $this->prophesize(LoggerInterface::class);

        $this->parser = new SeLogerPartnerParser($this->providerProvider->reveal(), $logger->reveal());
    }

    public function test_parse_creates_a_property_from_an_email(): void
    {
        $provider = $this->prophesize(Provider::class);

        // Given
        $provider->isNewBuildOnly()->willReturn(false);
        $this->providerProvider->find(Argument::any())->willReturn($provider->reveal());
        $html = file_get_contents($this->projectDir . '/tests/data/seloger_partner.html','r');

        // When
        $properties = $this->parser->parse($html, [], ['date' => new DateTime('2020-01-01 12:00:00')]);

        // Then
        self::assertCount(1, $properties);
        $p = $properties[0];
        self::assertEquals(224100, $p->getPrice());
        self::assertEquals(3, $p->getRoomsCount());
        self::assertEquals(72, $p->getArea());
        self::assertEquals('seloger', $p->getAd()->getProvider());
        self::assertFalse($p->isNewBuild());
        self::assertNotNull($p->getAd()->getDescription());
        self::assertNotNull($p->getAd()->getUrl());
        self::assertNotNull($p->getAd()->getPhoto());
        self::assertNull($p->getLocation());
        self::assertNull($p->getBuildingName());
        self::assertNull($p->getAd()->getTitle());
    }
}
