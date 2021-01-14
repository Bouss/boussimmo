<?php

namespace App\Parser;

use App\Enum\Provider;
use Symfony\Component\DomCrawler\Crawler;

class LeBonCoinParser extends AbstractParser
{
    protected const PROVIDER = Provider::LEBONCOIN;

    protected const SELECTOR_AD_WRAPPER  = '.lbc-classified';
    protected const SELECTOR_TITLE       = '.classified-title';
    protected const SELECTOR_LOCATION    = '.classified-location';

    /**
     * {@inheritDoc}
     */
    protected function parsePhoto(Crawler $crawler): ?string
    {
        return str_replace('thumb', 'image', parent::parsePhoto($crawler));
    }
}
