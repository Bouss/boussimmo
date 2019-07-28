<?php

namespace App\Parser;

use App\Entity\PropertyAd;
use App\Exception\ParseException;
use App\Util\NumberUtil;
use DateTime;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\DomCrawler\Crawler;

abstract class AbstractParser
{
    // Redefined in the child classes
    protected const SELECTOR_EXTERNAL_ID = '';
    protected const SELECTOR_AD_WRAPPER = '';
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
    protected const PUBLISHED_AT_FORMAT = '';

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param string $html
     *
     * @return PropertyAd[]
     *
     * @throws ParseException
     */
    public function parse(string $html): array
    {
        $crawler = new Crawler($html);

        try {
            $crawler->filter(static::SELECTOR_AD_WRAPPER);
        } catch (Exception $e) {
            throw new ParseException('No property ads found: ' . $e->getMessage());
        }

        /** @var PropertyAd[] $ads */
        $ads = $crawler->filter(static::SELECTOR_AD_WRAPPER)->each(function (Crawler $adCrawler) {
            try {
                $ad = (new PropertyAd())
                    ->setSite($this->getSite())
                    ->setExternalId($this->getExternalId($adCrawler))
                    ->setUrl($this->getUrl($adCrawler))
                    ->setPrice($this->getPrice($adCrawler))
                    ->setArea($this->getArea($adCrawler))
                    ->setRoomsCount($this->getRoomsCount($adCrawler))
                    ->setLocation($this->getLocation($adCrawler))
                    ->setPublishedAt($this->getPublishedAt($adCrawler))
                    ->setTitle($this->getTitle($adCrawler))
                    ->setDescription($this->getDescription($adCrawler))
                    ->setPhoto($this->getPhoto($adCrawler))
                    ->setRealEstateAgent($this->getRealEstateAgent($adCrawler));

                return $ad;
            } catch (Exception $e) {
                $this->logger->error('Error while parsing a property ad: ' . $e->getMessage(), ['site' => $this->getSite()]);
            }

            return null;
        });

        return $ads;
    }

    /**
     * @return string
     */
    abstract protected function getSite(): string;

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

        return NumberUtil::extractFloat($priceStr);
    }

    /**
     * @param Crawler $crawler
     *
     * @return float
     *
     * @throws ParseException
     */
    protected function getArea(Crawler $crawler): float
    {
        try {
            $areaStr = trim($crawler->filter(static::SELECTOR_AREA)->text());
        } catch (Exception $e) {
            throw new ParseException('Error while parsing the area: ' . $e->getMessage());
        }

        return NumberUtil::extractFloat($areaStr);
    }

    /**
     * @param Crawler $crawler
     *
     * @return int
     *
     * @throws ParseException
     */
    protected function getRoomsCount(Crawler $crawler): int
    {
        try {
            $roomsCountStr = trim($crawler->filter(static::SELECTOR_ROOMS_COUNT)->text());
        } catch (Exception $e) {
            throw new ParseException('Error while parsing the number of rooms: ' . $e->getMessage());
        }

        return NumberUtil::extractInt($roomsCountStr);
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
            return trim($crawler->filter(static::SELECTOR_EXTERNAL_ID)->text());
        } catch (Exception $e) {
            return null;
        }
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
}
