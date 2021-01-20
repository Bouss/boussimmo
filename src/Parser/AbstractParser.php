<?php

namespace App\Parser;

use App\DataProvider\ProviderProvider;
use App\DTO\Property;
use App\DTO\PropertyAd;
use App\Enum\PropertyFilter;
use App\Exception\ParseException;
use App\Formatter\DecimalFormatter;
use DateTime;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\DomCrawler\Crawler;
use function array_filter;
use function Symfony\Component\String\u;

abstract class AbstractParser implements ParserInterface
{
    // Redefined in the child classes
    protected const PROVIDER = null;

    protected const SELECTOR_AD_WRAPPER  = null;
    protected const SELECTOR_PRICE       = null;
    protected const SELECTOR_AREA        = null;
    protected const SELECTOR_ROOMS_COUNT = null;
    protected const SELECTOR_NAME        = null;
    protected const SELECTOR_TITLE       = null;
    protected const SELECTOR_DESCRIPTION = null;
    protected const SELECTOR_LOCATION    = null;
    protected const SELECTOR_PHOTO       = 'img:first-child';
    protected const SELECTOR_URL         = 'a:first-child';

    private const NEW_BUILD_WORDS = ['neuf', 'livraison', 'programme', 'neuve', 'nouveau', 'nouvelle', 'remise'];

    public function __construct(
        private ProviderProvider $providerProvider,
        protected DecimalFormatter $formatter,
        protected LoggerInterface $logger
    ) {}

    /**
     * {@inheritDoc}
     */
    public function parse(string $html, array $filters = [], array $params = []): array
    {
        $properties = [];

        // Iterate over all DOM elements wrapping a property ad
        ($this->createCrawler($html))->filter(static::SELECTOR_AD_WRAPPER)->each(function (Crawler $node) use (&$properties, $params) {
            try {
                $properties[] = $this->parseOne($node, $params['date']);
            } catch (Exception $e) {
                $this->logger->warning('Error while parsing a property: ' . $e->getMessage(), $params);
            }
        });

        if (empty($properties)) {
            throw new ParseException('No property parsed');
        }

        // Filter the properties
        $properties = array_filter($properties, static fn(Property $ad) => isset($filters[PropertyFilter::NEW_BUILD]) ? $ad->isNewBuild() : true);

        return $properties;
    }

    /**
     * Enable to modify the DOM before parsing
     *
     * @param string $html
     *
     * @return Crawler
     */
    protected function createCrawler(string $html): Crawler
    {
        return new Crawler($html);
    }

    protected function parsePrice(Crawler $crawler): ?float
    {
        if (null === static::SELECTOR_PRICE) {
            return $this->formatter->parsePrice($crawler->html());
        }

        try {
            $priceStr = trim($crawler->filter(static::SELECTOR_PRICE)->text());
        } catch (Exception) {
            return null;
        }

        return $this->formatter->parsePrice($priceStr);
    }

    protected function parseArea(Crawler $crawler): ?float
    {
        if (null === static::SELECTOR_AREA) {
            return $this->formatter->parseArea($crawler->html());
        }

        try {
            $areaStr = trim($crawler->filter(static::SELECTOR_AREA)->text());
        } catch (Exception) {
            return null;
        }

        return $this->formatter->parseArea($areaStr);
    }

    protected function parseRoomsCount(Crawler $crawler): ?int
    {
        if (null === static::SELECTOR_ROOMS_COUNT) {
            return $this->formatter->parseRoomsCount($crawler->html());
        }

        try {
            $roomsCountStr = trim($crawler->filter(static::SELECTOR_ROOMS_COUNT)->text());
        } catch (Exception) {
            return null;
        }

        return $this->formatter->parseRoomsCount($roomsCountStr);
    }

    protected function parseLocation(Crawler $crawler): ?string
    {
        if (null === static::SELECTOR_LOCATION) {
            return null;
        }

        try {
            return trim($crawler->filter(static::SELECTOR_LOCATION)->text());
        } catch (Exception) {
            return null;
        }
    }

    protected function parseBuildingName(Crawler $crawler): ?string
    {
        if (null === static::SELECTOR_NAME) {
            return null;
        }

        try {
            return trim($crawler->filter(static::SELECTOR_NAME)->text());
        } catch (Exception) {
            return null;
        }
    }

    protected function parseTitle(Crawler $crawler): ?string
    {
        if (null === static::SELECTOR_TITLE) {
            return null;
        }

        try {
            return trim($crawler->filter(static::SELECTOR_TITLE)->text());
        } catch (Exception) {
            return null;
        }
    }

    protected function parseDescription(Crawler $crawler): ?string
    {
        if (null === static::SELECTOR_DESCRIPTION) {
            return null;
        }

        try {
            return trim($crawler->filter(static::SELECTOR_DESCRIPTION)->text());
        } catch (Exception) {
            return null;
        }
    }

    /**
     * @throws ParseException
     */
    protected function parsePhoto(Crawler $crawler): ?string
    {
        try {
            return $crawler->filter(static::SELECTOR_PHOTO)->attr('src');
        } catch (Exception $e) {
            throw new ParseException('Error while parsing the photo: ' . $e->getMessage());
        }
    }

    /**
     * @throws ParseException
     */
    protected function parseUrl(Crawler $crawler): string
    {
        try {
            return $crawler->filter(static::SELECTOR_URL)->attr('href');
        } catch (Exception $e) {
            throw new ParseException('Error while parsing the URL: ' . $e->getMessage());
        }
    }

    /**
     * @throws ParseException
     */
    private function parseOne(Crawler $crawler, DateTime $publishedAt): Property
    {
        $propertyAd = (new PropertyAd)
            ->setProvider(static::PROVIDER)
            ->setTitle($this->parseTitle($crawler))
            ->setDescription($this->parseDescription($crawler))
            ->setPhoto($this->parsePhoto($crawler))
            ->setUrl($this->parseUrl($crawler))
            ->setPublishedAt($publishedAt);

        $property = (new Property)
            ->setPrice($this->parsePrice($crawler))
            ->setArea($this->parseArea($crawler))
            ->setRoomsCount($this->parseRoomsCount($crawler))
            ->setLocation($this->parseLocation($crawler))
            ->setBuildingName($this->parseBuildingName($crawler))
            ->setAd($propertyAd);

        if ((null !== $provider = $this->providerProvider->find(static::PROVIDER)) && $provider->isNewBuildOnly()) {
            $property->setNewBuild(true);
        } else {
            $property->setNewBuild(u($propertyAd->getTitle() . $propertyAd->getDescription())->containsAny(self::NEW_BUILD_WORDS));
        }

        return $property;
    }
}
