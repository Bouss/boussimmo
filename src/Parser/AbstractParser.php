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

abstract class AbstractParser implements ParserInterface
{
    // Redefined in the child classes
    protected const SITE = '';
    protected const SELECTOR_AD_WRAPPER = null;
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

    private const NEW_BUILD_WORDS = ['neuf', 'livraison', 'programme', 'neuve', 'nouveau', 'nouvelle', 'remise'];

    /**
     * @var LoggerInterface
     */
    protected $logger;

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
                return $this->buildPropertyAd($crawler, $params);
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

        // Clean (remove null values) and filter the property ads
        $ads = array_filter($ads, static function (?PropertyAd $ad) use ($filters) {
            return
                null !== $ad &&
                (isset($filters[PropertyAdFilter::NEW_BUILD]) && $filters[PropertyAdFilter::NEW_BUILD] ? $ad->isNewBuild() : true);
        });

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
    protected function getUrl(Crawler $crawler): string
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
    protected function getPrice(Crawler $crawler): ?float
    {
        if (empty(static::SELECTOR_PRICE)) {
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
    protected function getArea(Crawler $crawler): ?float
    {
        if (empty(static::SELECTOR_AREA)) {
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
    protected function getRoomsCount(Crawler $crawler): ?int
    {
        if (empty(static::SELECTOR_ROOMS_COUNT)) {
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
    protected function getLocation(Crawler $crawler): ?string
    {
        if (empty(static::SELECTOR_LOCATION)) {
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
    protected function getTitle(Crawler $crawler): ?string
    {
        if (empty(static::SELECTOR_TITLE)) {
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
    protected function getDescription(Crawler $crawler): ?string
    {
        if (empty(static::SELECTOR_DESCRIPTION)) {
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
    protected function getPhoto(Crawler $crawler): ?string
    {
        if (empty(static::SELECTOR_PHOTO)) {
            return null;
        }

        try {
            return $crawler->filter(static::SELECTOR_PHOTO)->attr('src');
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * @param Crawler $crawler
     * @param bool    $nodeExistenceOnly
     *
     * @return bool
     */
    protected function isNewBuild(Crawler $crawler, bool $nodeExistenceOnly = true): bool
    {
        if (empty(static::SELECTOR_NEW_BUILD)) {
            return StringUtil::contains(
                $this->getTitle($crawler) . $this->getDescription($crawler),
                self::NEW_BUILD_WORDS
            );
        }

        try {
            if ($nodeExistenceOnly) {
                return 1 === $crawler->filter(static::SELECTOR_NEW_BUILD)->count();
            }

            return StringUtil::contains(
                $crawler->filter(static::SELECTOR_NEW_BUILD)->text(),
                self::NEW_BUILD_WORDS
            );
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * @param Crawler $crawler
     * @param array   $params
     *
     * @return PropertyAd
     *
     * @throws ParseException
     * @throws Exception
     */
    protected function buildPropertyAd(Crawler $crawler, array $params = []): PropertyAd
    {
        return (new PropertyAd)
            ->setSite(static::SITE)
            ->setUrl($this->getUrl($crawler))
            ->setPrice($this->getPrice($crawler))
            ->setArea($this->getArea($crawler))
            ->setRoomsCount($this->getRoomsCount($crawler))
            ->setLocation($this->getLocation($crawler))
            ->setPublishedAt($params['date'])
            ->setTitle($this->getTitle($crawler))
            ->setDescription($this->getDescription($crawler))
            ->setPhoto($this->getPhoto($crawler))
            ->setNewBuild($this->isNewBuild($crawler));
    }
}
