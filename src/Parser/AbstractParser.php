<?php

namespace App\Parser;

use App\DTO\PropertyAd;
use App\Enum\PropertyAdFilter;
use App\Exception\ParseException;
use App\Formatter\DecimalFormatter;
use App\Repository\ProviderRepository;
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
    protected const SELECTOR_NEW_BUILD   = null;
    protected const SELECTOR_PHOTO       = 'img:first-child';
    protected const SELECTOR_URL         = 'a:first-child';

    private ProviderRepository $providerRepository;
    private LoggerInterface $logger;
    protected DecimalFormatter $formatter;

    /**
     * @param ProviderRepository $providerRepository
     * @param DecimalFormatter   $formatter
     * @param LoggerInterface    $logger
     */
    public function __construct(ProviderRepository $providerRepository, DecimalFormatter $formatter, LoggerInterface $logger)
    {
        $this->providerRepository = $providerRepository;
        $this->formatter = $formatter;
        $this->logger = $logger;
    }

    /**
     * {@inheritDoc}
     */
    public function parse(string $html, array $filters = [], array $params = []): array
    {
        $ads = [];

        // Iterate over all DOM elements wrapping a property ad
        ($this->createCrawler($html))->filter(static::SELECTOR_AD_WRAPPER)->each(function (Crawler $node) use (&$ads, $params) {
            try {
                $ads[] = $this->parseOne($node, $params['date']);
            } catch (Exception $e) {
                $this->logger->warning('Error while parsing a property ad: ' . $e->getMessage(), $params);
            }
        });

        if (empty($ads)) {
            throw new ParseException('No property ads parsed');
        }

        // Filter the property ads
        $ads = array_filter($ads, static fn(PropertyAd $ad) => isset($filters[PropertyAdFilter::NEW_BUILD]) ? $ad->isNewBuild() : true);

        return $ads;
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

    /**
     * @param Crawler $crawler
     *
     * @return float|null
     *
     * @throws ParseException
     */
    protected function parsePrice(Crawler $crawler): ?float
    {
        if (null === static::SELECTOR_PRICE) {
            return $this->formatter->parsePrice($crawler->html());
        }

        try {
            $priceStr = trim($crawler->filter(static::SELECTOR_PRICE)->text());
        } catch (Exception $e) {
            throw new ParseException('Error while parsing the price: ' . $e->getMessage());
        }

        return $this->formatter->parsePrice($priceStr);
    }

    /**
     * @param Crawler $crawler
     *
     * @return float|null
     *
     * @throws ParseException
     */
    protected function parseArea(Crawler $crawler): ?float
    {
        if (null === static::SELECTOR_AREA) {
            return $this->formatter->parseArea($crawler->html());
        }

        try {
            $areaStr = trim($crawler->filter(static::SELECTOR_AREA)->text());
        } catch (Exception $e) {
            throw new ParseException('Error while parsing the area: ' . $e->getMessage());
        }

        return $this->formatter->parseArea($areaStr);
    }

    /**
     * @param Crawler $crawler
     *
     * @return int|null
     *
     * @throws ParseException
     */
    protected function parseRoomsCount(Crawler $crawler): ?int
    {
        if (null === static::SELECTOR_ROOMS_COUNT) {
            return $this->formatter->parseRoomsCount($crawler->html());
        }

        try {
            $roomsCountStr = trim($crawler->filter(static::SELECTOR_ROOMS_COUNT)->text());
        } catch (Exception $e) {
            throw new ParseException('Error while parsing the number of rooms: ' . $e->getMessage());
        }

        return $this->formatter->parseRoomsCount($roomsCountStr);
    }

    /**
     * @param Crawler $crawler
     *
     * @return string|null
     */
    protected function parseLocation(Crawler $crawler): ?string
    {
        if (null === static::SELECTOR_LOCATION) {
            return null;
        }

        try {
            return trim($crawler->filter(static::SELECTOR_LOCATION)->text());
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * @param Crawler $crawler
     *
     * @return string|null
     */
    protected function parseName(Crawler $crawler): ?string
    {
        if (null === static::SELECTOR_NAME) {
            return null;
        }

        try {
            return trim($crawler->filter(static::SELECTOR_NAME)->text());
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * @param Crawler $crawler
     *
     * @return string|null
     */
    protected function parseTitle(Crawler $crawler): ?string
    {
        if (null === static::SELECTOR_TITLE) {
            return null;
        }

        try {
            return trim($crawler->filter(static::SELECTOR_TITLE)->text());
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * @param Crawler $crawler
     *
     * @return string|null
     */
    protected function parseDescription(Crawler $crawler): ?string
    {
        if (null === static::SELECTOR_DESCRIPTION) {
            return null;
        }

        try {
            return trim($crawler->filter(static::SELECTOR_DESCRIPTION)->text());
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * @param Crawler $crawler
     *
     * @return string
     *
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
     * @param Crawler $crawler
     *
     * @return string
     *
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
     * @param Crawler    $crawler
     * @param bool       $nodeExistenceOnly
     *
     * @return bool
     */
    protected function parseNewBuild(Crawler $crawler, bool $nodeExistenceOnly = true): bool
    {
        if (null === static::SELECTOR_NEW_BUILD) {
            return false;
        }

        try {
            if ($nodeExistenceOnly) {
                return 1 === $crawler->filter(static::SELECTOR_NEW_BUILD)->count();
            }

            return u($crawler->filter(static::SELECTOR_NEW_BUILD)->text())->containsAny(PropertyAd::NEW_BUILD_WORDS);
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * @param Crawler   $crawler
     * @param DateTime  $publishedAt
     *
     * @return PropertyAd
     *
     * @throws ParseException
     * @throws Exception
     */
    private function parseOne(Crawler $crawler, DateTime $publishedAt): PropertyAd
    {
        $propertyAd = (new PropertyAd)
            ->setProvider(static::PROVIDER)
            ->setPrice($this->parsePrice($crawler))
            ->setArea($this->parseArea($crawler))
            ->setRoomsCount($this->parseRoomsCount($crawler))
            ->setLocation($this->parseLocation($crawler))
            ->setName($this->parseName($crawler))
            ->setTitle($this->parseTitle($crawler))
            ->setDescription($this->parseDescription($crawler))
            ->setPhoto($this->parsePhoto($crawler))
            ->setUrl($this->parseUrl($crawler))
            ->setPublishedAt($publishedAt);

        if ((null !== $provider = $this->providerRepository->find(static::PROVIDER)) && $provider->isNewBuildOnly()) {
            $propertyAd->setNewBuild(true);
        } elseif (null !== static::SELECTOR_NEW_BUILD) {
            $propertyAd->setNewBuild($this->parseNewBuild($crawler));
        } else {
            $propertyAd->guessNewBuild();
        }

        return $propertyAd;
    }
}
