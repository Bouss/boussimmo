<?php

namespace App\Parser;

use App\DTO\PropertyAd;
use App\Enum\PropertyAdFilter;
use App\Exception\ParseException;
use App\Util\NumericUtil;
use App\Util\StringUtil;
use DateTime;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\DomCrawler\Crawler;
use function array_filter;
use function array_merge;

abstract class AbstractParser implements ParserInterface
{
    // Redefined in the child classes
    protected const PROVIDER = null;
    protected const SELECTOR_AD_WRAPPER = null;
    protected const SELECTOR_NAME = null;
    protected const SELECTOR_TITLE = null;
    protected const SELECTOR_DESCRIPTION = null;
    protected const SELECTOR_LOCATION = null;
    protected const SELECTOR_PUBLISHED_AT = null;
    protected const SELECTOR_URL = null;
    protected const SELECTOR_PRICE = null;
    protected const SELECTOR_AREA = null;
    protected const SELECTOR_ROOMS_COUNT = null;
    protected const SELECTOR_PHOTO = null;
    protected const SELECTOR_NEW_BUILD = null;

    protected LoggerInterface $logger;

    /**
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * {@inheritDoc}
     */
    public function parse(string $html, array $filters = [], array $params = []): array
    {
        // Iterate over all DOM elements wrapping a property ad
        /** @var PropertyAd[] $ads */
        $ads[] = ($this->createCrawler($html))->filter(static::SELECTOR_AD_WRAPPER)->each(function (Crawler $crawler) use ($params) {
            try {
                return $this->createPropertyAd($crawler, $params['date']);
            } catch (Exception $e) {
                $this->logger->warning('Error while parsing a property ad: ' . $e->getMessage(), $params);

                return null;
            }
        });

        // Flatten the property ads
        $ads = array_merge(...$ads);

        if (empty($ads)) {
            throw new ParseException('No property ads parsed');
        }

        // Clean (exclude null values) and filter the property ads
        $ads = array_filter($ads, fn(?PropertyAd $ad) =>
            null !== $ad &&
            (isset($filters[PropertyAdFilter::NEW_BUILD]) && true === $filters[PropertyAdFilter::NEW_BUILD] ? $ad->isNewBuild() : true)
        );

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
     * @param Crawler $crawler
     *
     * @return float|null
     *
     * @throws ParseException
     */
    protected function parsePrice(Crawler $crawler): ?float
    {
        if (null === static::SELECTOR_PRICE) {
            return null;
        }

        try {
            $priceStr = trim($crawler->filter(static::SELECTOR_PRICE)->text());
        } catch (Exception $e) {
            throw new ParseException('Error while parsing the price: ' . $e->getMessage());
        }

        return NumericUtil::extractPrice($priceStr);
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
            return null;
        }

        try {
            $areaStr = trim($crawler->filter(static::SELECTOR_AREA)->text());
        } catch (Exception $e) {
            throw new ParseException('Error while parsing the area: ' . $e->getMessage());
        }

        return NumericUtil::extractArea($areaStr);
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
            return null;
        }

        try {
            $roomsCountStr = trim($crawler->filter(static::SELECTOR_ROOMS_COUNT)->text());
        } catch (Exception $e) {
            throw new ParseException('Error while parsing the number of rooms: ' . $e->getMessage());
        }

        return NumericUtil::extractRoomsCount($roomsCountStr);
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
     * @return string|null
     */
    protected function parsePhoto(Crawler $crawler): ?string
    {
        if (null === static::SELECTOR_PHOTO) {
            return null;
        }

        try {
            return $crawler->filter(static::SELECTOR_PHOTO)->attr('src');
        } catch (Exception $e) {
            return null;
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

            return StringUtil::contains(
                $crawler->filter(static::SELECTOR_NEW_BUILD)->text(),
                PropertyAd::NEW_BUILD_WORDS
            );
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
    private function createPropertyAd(Crawler $crawler, DateTime $publishedAt): PropertyAd
    {
        $propertyAd = (new PropertyAd)
            ->setProvider(static::PROVIDER)
            ->setPublishedAt($publishedAt)
            ->setUrl($this->parseUrl($crawler))
            ->setPrice($this->parsePrice($crawler))
            ->setArea($this->parseArea($crawler))
            ->setRoomsCount($this->parseRoomsCount($crawler))
            ->setLocation($this->parseLocation($crawler))
            ->setName($this->parseName($crawler))
            ->setTitle($this->parseTitle($crawler))
            ->setDescription($this->parseDescription($crawler))
            ->setPhoto($this->parsePhoto($crawler));

        if (null !== static::SELECTOR_NEW_BUILD) {
            $propertyAd->setNewBuild($this->parseNewBuild($crawler));
        } else {
            $propertyAd->guessNewBuild();
        }

        return $propertyAd;
    }
}
