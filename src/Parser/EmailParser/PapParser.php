<?php

namespace App\Parser\EmailParser;

use App\Enum\Provider;
use App\Parser\AbstractParser;
use App\Util\NumericUtil;
use Symfony\Component\DomCrawler\Crawler;

class PapParser extends AbstractParser
{
    protected const SITE = Provider::PAP;
    protected const SELECTOR_AD_WRAPPER = 'table[width="550"] tr:nth-child(n+3):not(:last-child)';
    protected const SELECTOR_EXTERNAL_ID = '';
    protected const SELECTOR_TITLE = '';
    protected const SELECTOR_DESCRIPTION = 'td:nth-child(2)';
    protected const SELECTOR_LOCATION = 'td:nth-child(2) b';
    protected const SELECTOR_PUBLISHED_AT = '';
    protected const SELECTOR_URL = 'a:first-child';
    protected const SELECTOR_PRICE = 'td:nth-child(2)';
    protected const SELECTOR_AREA = 'td:nth-child(2)';
    protected const SELECTOR_ROOMS_COUNT = 'td:nth-child(2)';
    protected const SELECTOR_PHOTO = 'img';
    protected const SELECTOR_REAL_AGENT_ESTATE = '';
    protected const SELECTOR_NEW_BUILD = '';
    protected const PUBLISHED_AT_FORMAT = '';

    /**
     * {@inheritDoc}
     */
    protected function getPrice(Crawler $crawler): ?float
    {
        $price = trim($crawler->filter(static::SELECTOR_AREA)->text());

        return NumericUtil::extractPrice($price);
    }

    /**
     * {@inheritDoc}
     */
    protected function getArea(Crawler $crawler): ?float
    {
        $area = trim($crawler->filter(static::SELECTOR_AREA)->text());

        return NumericUtil::extractArea($area);
    }

    /**
     * {@inheritDoc}
     */
    protected function getRoomsCount(Crawler $crawler): ?int
    {
        $roomsCount = trim($crawler->filter(static::SELECTOR_AREA)->text());

        return NumericUtil::extractRoomsCount($roomsCount);
    }

}
