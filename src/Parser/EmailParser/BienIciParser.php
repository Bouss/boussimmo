<?php

namespace App\Parser\EmailParser;

use App\Definition\SiteEnum;
use App\Exception\ParseException;
use App\Parser\AbstractParser;
use Exception;
use Symfony\Component\DomCrawler\Crawler;

class BienIciParser extends AbstractParser
{
    protected const SITE = SiteEnum::BIENICI;
    protected const SELECTOR_AD_WRAPPER = '[class$="realEstateAd"]';
    protected const SELECTOR_EXTERNAL_ID = '';
    protected const SELECTOR_TITLE = '';
    protected const SELECTOR_DESCRIPTION = '';
    protected const SELECTOR_LOCATION = '[class$="realEstateAdAddress"] > a';
    protected const SELECTOR_PUBLISHED_AT = '';
    protected const SELECTOR_URL = 'a:first-child';
    protected const SELECTOR_PRICE = '[class$="realEstateAdPrice"] strong';
    protected const SELECTOR_AREA = '[class$="realEstateAdTitle"] strong';
    protected const SELECTOR_ROOMS_COUNT = '[class$="realEstateAdTitle"] strong';
    protected const SELECTOR_PHOTO = '[class$=realEstateAdPhoto] img';
    protected const SELECTOR_REAL_AGENT_ESTATE = '';
    protected const SELECTOR_NEW_BUILD = '';
    protected const PUBLISHED_AT_FORMAT = '';

    private const REGEX_ROOMS_COUNT = '/([0-9]+)\spièces/u';
    private const REGEX_AREA = '/([0-9]+)\sm²/u';

    /**
     * {@inheritDoc}
     */
    protected function getArea(Crawler $crawler): float
    {
        try {
            $title = $crawler->filter(self::SELECTOR_ROOMS_COUNT)->text();
        } catch (Exception $e) {
            throw new ParseException('Error while parsing the area: '.$e->getMessage());
        }

        preg_match(self::REGEX_AREA, $title, $matches);

        return (float) $matches[1];
    }

    /**
     * {@inheritDoc}
     */
    protected function getRoomsCount(Crawler $crawler): int
    {
        try {
            $title = $crawler->filter(self::SELECTOR_AREA)->text();
        } catch (Exception $e) {
            throw new ParseException('Error while parsing the area: '.$e->getMessage());
        }

        preg_match(self::REGEX_ROOMS_COUNT, $title, $matches);

        return (int) $matches[1];
    }
}
