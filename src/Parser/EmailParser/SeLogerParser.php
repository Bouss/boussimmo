<?php

namespace App\Parser\EmailParser;

use App\Definition\SiteEnum;
use App\Exception\ParseException;
use App\Parser\AbstractParser;
use App\Util\NumericUtil;
use Exception;
use Symfony\Component\DomCrawler\Crawler;

class SeLogerParser extends AbstractParser
{
    protected const SITE = SiteEnum::SELOGER;
    protected const SELECTOR_AD_WRAPPER = 'td[class$="two-column"]';
    protected const SELECTOR_EXTERNAL_ID = '';
    protected const SELECTOR_TITLE = '';
    protected const SELECTOR_DESCRIPTION = '';
    protected const SELECTOR_LOCATION = 'td[style$="font-size: 16px; line-height: 25px; color: #262626;"] a';
    protected const SELECTOR_PUBLISHED_AT = '';
    protected const SELECTOR_URL = 'a:first-child';
    protected const SELECTOR_PRICE = 'b';
    protected const SELECTOR_AREA = 'td[style$="font-size: 16px; line-height: 25px; color: #262626;"] a';
    protected const SELECTOR_ROOMS_COUNT = 'td[style$="font-size: 16px; line-height: 25px; color: #262626;"] a';
    protected const SELECTOR_PHOTO = 'img';
    protected const SELECTOR_REAL_AGENT_ESTATE = '';
    protected const SELECTOR_NEW_BUILD = '';
    protected const PUBLISHED_AT_FORMAT = '';

    /**
     * {@inheritDoc}
     */
    protected function getLocation(Crawler $crawler): ?string
    {
        $description = parent::getLocation($crawler);

        $location = explode('m²', $description)[1];

        return trim($location);
    }

    /**
     * {@inheritDoc}
     */
    protected function getArea(Crawler $crawler): float
    {
        try {
            $description = trim($crawler->filter(static::SELECTOR_AREA)->text());
        } catch (Exception $e) {
            throw new ParseException('Error while parsing the area: ' . $e->getMessage());
        }

        $areaStr = explode('•', $description)[2];

        return NumericUtil::extractFloat($areaStr);
    }

    /**
     * {@inheritDoc}
     */
    protected function getRoomsCount(Crawler $crawler): int
    {
        try {
            $description = trim($crawler->filter(static::SELECTOR_ROOMS_COUNT)->text());
        } catch (Exception $e) {
            throw new ParseException('Error while parsing the number of rooms: ' . $e->getMessage());
        }

        $roomsCountStr = explode('•', $description)[1];

        return NumericUtil::extractInt($roomsCountStr);
    }
}
