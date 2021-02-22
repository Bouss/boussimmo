<?php

namespace App\Tests\Parser;

use App\DataProvider\ProviderProvider;
use App\DTO\Provider;
use App\Formatter\DecimalFormatter;
use App\Parser\SuperimmoNeufParser;
use DateTime;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class SuperimmoNeufParserTest extends KernelTestCase
{
    use ProphecyTrait;

    /** @var ObjectProphecy|ProviderProvider */
    private $providerProvider;

    /** @var ObjectProphecy|LoggerInterface */
    private $logger;

    private string $projectDir;
    private SuperimmoNeufParser $parser;

    public function setUp(): void
    {
        self::bootKernel();
        $this->projectDir = self::$kernel->getProjectDir();

        $this->providerProvider = $this->prophesize(ProviderProvider::class);
        $this->logger = $this->prophesize(LoggerInterface::class);

        $this->parser = new SuperimmoNeufParser($this->providerProvider->reveal(), new DecimalFormatter(), $this->logger->reveal());
    }

    public function testParseCreatesAPropertyFromAnEmail(): void
    {
        $provider = $this->prophesize(Provider::class);

        // Given
        $provider->isNewBuildOnly()->willReturn(true);
        $this->providerProvider->find(Argument::any())->willReturn($provider->reveal());
        $html = file_get_contents($this->projectDir . '/tests/data/superimmo_neuf.html','r');

        // When
        $properties = $this->parser->parse($html, [], ['date' => new DateTime('2020-01-01 12:00:00')]);

        // Then
        self::assertCount(1, $properties);
        $p = $properties[0];
        self::assertEquals(3, $p->getRoomsCount());
        self::assertEquals('Nantes (44300)', $p->getLocation());
        self::assertEquals('superimmo_neuf', $p->getAd()->getProvider());
        self::assertTrue($p->isNewBuild());
        self::assertNotNull($p->getAd()->getTitle());
        self::assertNotNull($p->getAd()->getDescription());
        self::assertNotNull($p->getAd()->getUrl());
        self::assertNotNull($p->getAd()->getPhoto());
        self::assertNull($p->getArea());
        self::assertNull($p->getPrice());
        self::assertNull($p->getBuildingName());
    }
}
