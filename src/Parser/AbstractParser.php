<?php

namespace App\Parser;

use App\DTO\PropertyAd;
use App\Exception\ParseException;
use App\Util\NumericUtil;
use App\Util\StringUtil;
use DateTime;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\DomCrawler\Crawler;

abstract class AbstractParser
{
    // Redefined in the child classes
    protected const SITE = '';
    protected const SELECTOR_AD_WRAPPER = '';
    protected const SELECTOR_EXTERNAL_ID = '';
    protected const SELECTOR_TITLE = '';
    protected const SELECTOR_DESCRIPTION = '';
    protected const SELECTOR_LOCATION = '';
    protected const SELECTOR_PUBLISHED_AT = '';
    protected const SELECTOR_URL = '';
    protected const SELECTOR_PRICE = '';
    protected const SELECTOR_AREA = '';
    protected const SELECTOR_ROOMS_COUNT = '';
    protected const SELECTOR_PHOTO = '';
    protected const SELECTOR_REAL_AGENT_ESTATE = '';
    protected const SELECTOR_NEW_BUILD = '';
    protected const PUBLISHED_AT_FORMAT = '';

    private const NEW_BUILD_WORDS = ['neuf', 'livraison', 'programme'];

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
     * @param string $html
     * @param array  $filters
     * @param array  $params
     *
     * @return PropertyAd[]
     *
     * @throws ParseException
     */
    public function parse(string $html, array $filters = [], array $params = []): array
    {
        $crawler = new Crawler($html);

        try {
            $crawler->filter(static::SELECTOR_AD_WRAPPER);
        } catch (Exception $e) {
            throw new ParseException('No property ads found: ' . $e->getMessage());
        }

        // Iterate over all DOM elements wrapping a property ad
        /** @var PropertyAd[] $ads */
        $ads[] = $crawler->filter(static::SELECTOR_AD_WRAPPER)->each(function (Crawler $adCrawler) use ($params) {
            try {
                return $this->buildPropertyAd($adCrawler, $params);
            } catch (Exception $e) {
                $this->logger->error('Error while parsing a property ad: ' . $e->getMessage(), $params);

                return null;
            }
        });

        // Merge all the ad arrays in one, clean (remove null values) and filter the ads
        $ads = array_filter(array_merge(...$ads), static function (?PropertyAd $ad) use ($filters) {
            return
                null !== $ad &&
                (isset($filters['new_build']) && $filters['new_build']) ? $ad->isNewBuild() : true;
        });

        return $ads;
    }

    /**
     * @param Crawler $crawler
     *
     * @return string|null
     */
    protected function getExternalId(Crawler $crawler): ?string
    {
        if (empty(static::SELECTOR_EXTERNAL_ID)) {
            return null;
        }

        try {
            preg_match('/.*\[(.+)].*/', static::SELECTOR_EXTERNAL_ID, $matches);

            return $crawler->filter(static::SELECTOR_EXTERNAL_ID)->attr($matches[1]);
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
     * @return float
     *
     * @throws ParseException
     */
    protected function getPrice(Crawler $crawler): float
    {
        try {
            $priceStr = trim($crawler->filter(static::SELECTOR_PRICE)->text());
        } catch (Exception $e) {
            throw new ParseException('Error while parsing the price: ' . $e->getMessage());
        }

        return NumericUtil::extractPrice($priceStr) ?: NumericUtil::extractFloat($priceStr);
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

        return NumericUtil::extractArea($areaStr) ?: NumericUtil::extractFloat($areaStr);
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

        return NumericUtil::extractRoomsCount($roomsCountStr) ?: NumericUtil::extractInt($roomsCountStr);
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
     * @return DateTime|null
     *
     * @throws Exception
     */
    protected function getPublishedAt(Crawler $crawler): ?DateTime
    {
        if (empty(static::SELECTOR_PUBLISHED_AT)) {
            return null;
        }

        try {
            $publishedAtStr = trim($crawler->filter(static::SELECTOR_PUBLISHED_AT)->text());
            $publishedAt = DateTime::createFromFormat(static::PUBLISHED_AT_FORMAT, $publishedAtStr);

            if (false === strpos(static::PUBLISHED_AT_FORMAT, 'H')) {
                $publishedAt->setTime(12, 0);
            }

            return $publishedAt;
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
     *
     * @return string|null
     */
    protected function getRealEstateAgent(Crawler $crawler): ?string
    {
        if (empty(static::SELECTOR_REAL_AGENT_ESTATE)) {
            return null;
        }

        try {
            return trim($crawler->filter(static::SELECTOR_REAL_AGENT_ESTATE)->text());
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * @param Crawler $crawler
     *
     * @return bool
     */
    protected function isNewBuild(Crawler $crawler): bool
    {
        if (!empty(static::SELECTOR_NEW_BUILD)) {
            try {
                return 1 === $crawler->filter(static::SELECTOR_NEW_BUILD)->count();
            } catch (Exception $e) {
                return false;
            }
        }

        return StringUtil::contains($this->getTitle($crawler) . $this->getDescription($crawler), self::NEW_BUILD_WORDS);
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
            ->setExternalId($this->getExternalId($crawler))
            ->setUrl($this->getUrl($crawler))
            ->setPrice($this->getPrice($crawler))
            ->setArea($this->getArea($crawler))
            ->setRoomsCount($this->getRoomsCount($crawler))
            ->setLocation($this->getLocation($crawler))
            ->setPublishedAt($this->getPublishedAt($crawler) ?: $params['date'] ?? null)
            ->setTitle($this->getTitle($crawler))
            ->setDescription($this->getDescription($crawler))
            ->setPhoto($this->getPhoto($crawler))
            ->setRealEstateAgent($this->getRealEstateAgent($crawler))
            ->setNewBuild($this->isNewBuild($crawler));
    }
}
