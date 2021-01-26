<?php

namespace App\Tests\Parser;

use App\DTO\Provider;
use App\Formatter\DecimalFormatter;
use App\Parser\SuperimmoParser;
use App\DataProvider\ProviderProvider;
use DateTime;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use function Symfony\Component\String\u;

class SuperimmoParserTest extends KernelTestCase
{
    use ProphecyTrait;

    /** @var ObjectProphecy|ProviderProvider */
    private $providerProvider;

    /** @var ObjectProphecy|LoggerInterface */
    private $logger;

    private string $projectDir;
    private SuperimmoParser $parser;

    public function setUp(): void
    {
        self::bootKernel();
        $this->projectDir = self::$kernel->getProjectDir();

        $this->providerProvider = $this->prophesize(ProviderProvider::class);
        $this->logger = $this->prophesize(LoggerInterface::class);

        $this->parser = new SuperimmoParser($this->providerProvider->reveal(), new DecimalFormatter(), $this->logger->reveal());
    }

    public function testParseCreatesAPropertyFromAnEmail(): void
    {
        $provider = $this->prophesize(Provider::class);

        // Given
        $provider->isNewBuildOnly()->willReturn(false);
        $this->providerProvider->find(Argument::any())->willReturn($provider->reveal());
        $html = file_get_contents($this->projectDir . '/tests/data/superimmo.html','r');

        // When
        $properties = $this->parser->parse($html, [], ['date' => new DateTime('2020-01-01 12:00:00')]);

        // Then
        self::assertCount(1, $properties);
        $p = $properties[0];
        self::assertEquals(72.55, $p->getArea());
        self::assertEquals('Nantes (44100)', $p->getLocation());
        self::assertEquals(193000, $p->getPrice());
        self::assertEquals('superimmo', $p->getAd()->getProvider());
        self::assertFalse($p->isNewBuild());
        self::assertNotNull($p->getAd()->getTitle());
        self::assertNotNull($p->getAd()->getDescription());
        self::assertNotNull($p->getAd()->getUrl());
        self::assertNotNull($p->getAd()->getPhoto());
        self::assertNull($p->getRoomsCount());
        self::assertNull($p->getBuildingName());
    }
}
