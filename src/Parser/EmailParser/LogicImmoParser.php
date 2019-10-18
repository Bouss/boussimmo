<?php

namespace App\Parser\EmailParser;

use App\Definition\SiteEnum;
use App\Exception\ParseException;
use App\Parser\AbstractParser;
use Exception;
use Symfony\Component\DomCrawler\Crawler;

class LogicImmoParser extends AbstractParser
{
    protected const SITE = SiteEnum::LOGIC_IMMO;
    protected const SELECTOR_AD_WRAPPER = 'td[class$="full"][width="270"] > table[bgcolor="#fcfcfc"]:not(:last-child)';
    protected const SELECTOR_EXTERNAL_ID = '';
    protected const SELECTOR_TITLE = '';
    protected const SELECTOR_DESCRIPTION = '';
    protected const SELECTOR_LOCATION = 'a[href*="description_ville"]';
    protected const SELECTOR_PUBLISHED_AT = '';
    protected const SELECTOR_URL = 'a:first-child';
    protected const SELECTOR_PRICE = 'b > a';
    protected const SELECTOR_AREA = 'td[style$="font-size:13px; color:#000000; padding-top:10px; padding-bottom:10px;"] > a';
    protected const SELECTOR_ROOMS_COUNT = 'td[style$="font-size:13px; color:#000000; padding-top:10px; padding-bottom:10px;"] > a';
    protected const SELECTOR_PHOTO = '[class$=background]';
    protected const SELECTOR_REAL_AGENT_ESTATE = '';
    protected const SELECTOR_NEW_BUILD = 'a:first-child[href*="neuf"]';
    protected const PUBLISHED_AT_FORMAT = '';

    private const REGEX_ROOMS_COUNT = '/([0-9]+)\spièces/u';
    private const REGEX_AREA = '/([0-9]+)\sm²/u';

    /**
     * {@inheritDoc}
     */
    protected function getArea(Crawler $crawler): float
    {
        try {
            $criteria = $crawler->filter(self::SELECTOR_ROOMS_COUNT)->text();

        } catch (Exception $e) {
            throw new ParseException('Error while parsing the area: '.$e->getMessage());
        }

        $areaStr = explode('|', $criteria)[2];

        preg_match(self::REGEX_AREA, $areaStr, $matches);

        return (float) $matches[1];
    }

    /**
     * {@inheritDoc}
     */
    protected function getRoomsCount(Crawler $crawler): int
    {
        try {
            $criteria = $crawler->filter(self::SELECTOR_AREA)->text();
        } catch (Exception $e) {
            throw new ParseException('Error while parsing the rooms count: '.$e->getMessage());
        }

        $roomsCountStr = explode('|', $criteria)[1];

        preg_match(self::REGEX_ROOMS_COUNT, $roomsCountStr, $matches);

        return (int) $matches[1];
    }

    /**
     * {@inheritDoc}
     */
    protected function getPhoto(Crawler $crawler): ?string
    {
        try {
            return $crawler->filter(static::SELECTOR_PHOTO)->attr('background');
        } catch (Exception $e) {
            return null;
        }
    }
}
