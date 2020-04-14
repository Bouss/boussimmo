<?php

namespace App\Parser\EmailParser;

use App\Enum\Provider;
use App\Parser\AbstractParser;
use Symfony\Component\DomCrawler\Crawler;

class LeBonCoinParser extends AbstractParser
{
    protected const SITE = Provider::LEBONCOIN;
    protected const SELECTOR_AD_WRAPPER  = '.lbc-classified';
    protected const SELECTOR_TITLE       = '.classified-title';
    protected const SELECTOR_LOCATION    = '.classified-location';
    protected const SELECTOR_URL         = '.classified-link';
    protected const SELECTOR_PRICE       = '.classified-price';
    protected const SELECTOR_AREA        = '.classified-title';
    protected const SELECTOR_ROOMS_COUNT = '.classified-title';
    protected const SELECTOR_PHOTO       = 'img:first-child';

    private const REGEX_ROOMS_COUNT_1 = '/([0-9]+)\spièces/u';
    private const REGEX_ROOMS_COUNT_2 = '/T([0-9])+/';

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
        return str_replace('thumb', 'image', parent::getPhoto($crawler));
    }
}
