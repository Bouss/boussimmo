<?php

namespace App\Parser;

use App\Enum\Provider;
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

    /**
     * {@inheritDoc}
     */
    protected function getPhoto(Crawler $crawler): ?string
    {
        return str_replace('thumb', 'image', parent::getPhoto($crawler));
    }
}
