<?php

namespace App\Tests\Parser;

use App\DataProvider\ProviderProvider;
use App\DTO\Provider;
use App\Parser\PapNeufParser;
use DateTime;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class PapNeufParserTest extends KernelTestCase
{
    use ProphecyTrait;

    private ObjectProphecy|ProviderProvider $providerProvider;
    private string $projectDir;
    private PapNeufParser $parser;

    public function setUp(): void
    {
        self::bootKernel();
        $this->projectDir = self::$kernel->getProjectDir();

        $this->providerProvider = $this->prophesize(ProviderProvider::class);
        $logger = $this->prophesize(LoggerInterface::class);

        $this->parser = new PapNeufParser($this->providerProvider->reveal(), $logger->reveal());
    }

    public function test_parse_creates_a_property_from_an_email(): void
    {
        $provider = $this->prophesize(Provider::class);

        // Given
        $provider->isNewBuildOnly()->willReturn(true);
        $this->providerProvider->find(Argument::any())->willReturn($provider->reveal());
        $html = file_get_contents($this->projectDir . '/tests/data/pap_neuf.html','r');

        // When
        $properties = $this->parser->parse($html, [], ['date' => new DateTime('2020-01-01 12:00:00')]);

        // Then
        self::assertCount(1, $properties);
        $p = $properties[0];
        self::assertEquals('89-95 Rue de la Contrie 44100 Nantes', $p->getLocation());
        self::assertEquals('COSY GARDEN', $p->getBuildingName());
        self::assertEquals(170000, $p->getPrice());
        self::assertEquals('pap_neuf', $p->getAd()->getProvider());
        self::assertTrue($p->isNewBuild());
        self::assertNotNull($p->getAd()->getUrl());
        self::assertNotNull($p->getAd()->getPhoto());
        self::assertNull($p->getRoomsCount());
        self::assertNull($p->getArea());
        self::assertNull($p->getAd()->getTitle());
        self::assertNull($p->getAd()->getDescription());
    }
}
