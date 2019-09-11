<?php

namespace App\Parser\EmailParser;

use App\Definition\SiteEnum;
use App\Exception\ParseException;
use App\Parser\AbstractParser;
use App\Util\NumericUtil;
use Exception;
use Symfony\Component\DomCrawler\Crawler;

class FnaimParser extends AbstractParser
{
    protected const SITE = SiteEnum::FNAIM;
    protected const SELECTOR_AD_WRAPPER = '[class*=item]';
    protected const SELECTOR_EXTERNAL_ID = '';
    protected const SELECTOR_TITLE = '';
    protected const SELECTOR_DESCRIPTION = '[class$=desc]';
    protected const SELECTOR_LOCATION = 'h3 + p';
    protected const SELECTOR_PUBLISHED_AT = '';
    protected const SELECTOR_URL = 'a:first-child';
    protected const SELECTOR_PRICE = 'h3';
    protected const SELECTOR_AREA = 'h3 > a';
    protected const SELECTOR_ROOMS_COUNT = 'h3 > a';
    protected const SELECTOR_PHOTO = 'img';
    protected const SELECTOR_REAL_AGENT_ESTATE = '';
    protected const SELECTOR_NEW_BUILD = '';
    protected const PUBLISHED_AT_FORMAT = '';

    /**
     * {@inheritDoc}
     */
    protected function getPrice(Crawler $crawler): float
    {
        try {
            $title = trim($crawler->filter(static::SELECTOR_PRICE)->text());
        } catch (Exception $e) {
            throw new ParseException('Error while parsing the price: ' . $e->getMessage());
        }

        $priceStr = explode('-', $title)[2];

        return NumericUtil::extractFloat($priceStr);
    }

    /**
     * {@inheritDoc}
     */
    protected function getArea(Crawler $crawler): float
    {
        try {
            $title = trim($crawler->filter(static::SELECTOR_AREA)->text());
        } catch (Exception $e) {
            throw new ParseException('Error while parsing the area: ' . $e->getMessage());
        }

        $areaStr = explode('-', $title)[1];

        return NumericUtil::extractFloat($areaStr);
    }

    /**
     * {@inheritDoc}
     */
    protected function getRoomsCount(Crawler $crawler): int
    {
        try {
            $title = trim($crawler->filter(static::SELECTOR_ROOMS_COUNT)->text());
        } catch (Exception $e) {
            throw new ParseException('Error while parsing the number of rooms: ' . $e->getMessage());
        }

        $roomsCountStr = explode('-', $title)[0];

        return NumericUtil::extractInt($roomsCountStr);
    }
}
