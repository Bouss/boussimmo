<?php

namespace App\Parser\EmailParser;

use App\Enum\Provider;
use App\Parser\AbstractParser;
use App\Util\NumericUtil;
use Symfony\Component\DomCrawler\Crawler;

class LeBonCoinParser extends AbstractParser
{
    protected const SITE = Provider::LEBONCOIN;
    protected const SELECTOR_AD_WRAPPER = '[class*="bc-classified"]';
    protected const SELECTOR_EXTERNAL_ID = '';
    protected const SELECTOR_TITLE = '[class$="classified-title"]';
    protected const SELECTOR_DESCRIPTION = '';
    protected const SELECTOR_LOCATION = '[class$=classified-location]';
    protected const SELECTOR_PUBLISHED_AT = '';
    protected const SELECTOR_URL = '[class$="classified-link"]';
    protected const SELECTOR_PRICE = '[class$="classified-price"]';
    protected const SELECTOR_AREA = '[class$="classified-title"]';
    protected const SELECTOR_ROOMS_COUNT = '[class$="classified-title"]';
    protected const SELECTOR_PHOTO = 'img';
    protected const SELECTOR_REAL_AGENT_ESTATE = '';
    protected const SELECTOR_NEW_BUILD = '';
    protected const PUBLISHED_AT_FORMAT = '';

    private const REGEX_ROOMS_COUNT_1 = '/([0-9]+)\spièces/u';
    private const REGEX_ROOMS_COUNT_2 = '/T([0-9])+/';

    /**
     * {@inheritDoc}
     */
    protected function getArea(Crawler $crawler): ?float
    {
        $title = trim($crawler->filter(static::SELECTOR_AREA)->text());

        return NumericUtil::extractArea($title);
    }

    /**
     * {@inheritDoc}
     */
    protected function getRoomsCount(Crawler $crawler): ?int
    {
        $title = $crawler->filter(self::SELECTOR_ROOMS_COUNT)->text();

        foreach ([self::REGEX_ROOMS_COUNT_1, self::REGEX_ROOMS_COUNT_2] as $regex) {
            preg_match($regex, $title, $matches);

            if (isset($matches[1])) {
                return (int) $matches[1];
            }
        }

        return null;
    }

    /**
     * {@inheritDoc}
     */
    protected function getPhoto(Crawler $crawler): ?string
    {
        $photo = parent::getPhoto($crawler);

        return str_replace('thumb', 'image', $photo);
    }
}
