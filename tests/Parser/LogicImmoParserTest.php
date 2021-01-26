<?php

namespace App\Tests\Parser;

use App\DTO\Provider;
use App\Formatter\DecimalFormatter;
use App\Parser\LogicImmoParser;
use App\DataProvider\ProviderProvider;
use DateTime;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use function Symfony\Component\String\u;

class LogicImmoParserTest extends KernelTestCase
{
    use ProphecyTrait;

    /** @var ObjectProphecy|ProviderProvider */
    private $providerProvider;

    /** @var ObjectProphecy|LoggerInterface */
    private $logger;

    private string $projectDir;
    private LogicImmoParser $parser;

    public function setUp(): void
    {
        self::bootKernel();
        $this->projectDir = self::$kernel->getProjectDir();

        $this->providerProvider = $this->prophesize(ProviderProvider::class);
        $this->logger = $this->prophesize(LoggerInterface::class);

        $this->parser = new LogicImmoParser($this->providerProvider->reveal(), new DecimalFormatter(), $this->logger->reveal());
    }

    public function testParseCreatesAPropertyFromAnEmail(): void
    {
        $provider = $this->prophesize(Provider::class);

        // Given
        $provider->isNewBuildOnly()->willReturn(false);
        $this->providerProvider->find(Argument::any())->willReturn($provider->reveal());
        $html = file_get_contents($this->projectDir . '/tests/data/logic_immo.html','r');

        // When
        $properties = $this->parser->parse($html, [], ['date' => new DateTime('2020-01-01 12:00:00')]);

        // Then
        self::assertCount(1, $properties);
        $p = $properties[0];
        self::assertEquals(3, $p->getRoomsCount());
        self::assertEquals(66, $p->getArea());
        self::assertEquals(164900, $p->getPrice());
        self::assertEquals('NANTES (44300)', $p->getLocation());
        self::assertEquals('logic_immo', $p->getAd()->getProvider());
        self::assertFalse($p->isNewBuild());
        self::assertNotNull($p->getAd()->getUrl());
        self::assertNotNull($p->getAd()->getPhoto());
        self::assertNotNull($p->getAd()->getDescription());
        self::assertNull($p->getAd()->getTitle());
        self::assertNull($p->getBuildingName());
    }
}
