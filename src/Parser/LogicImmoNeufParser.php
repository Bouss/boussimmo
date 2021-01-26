<?php

namespace App\Parser;

use App\Enum\Provider;
use Symfony\Component\DomCrawler\Crawler;

class LogicImmoNeufParser extends AbstractParser
{
    protected const PROVIDER = Provider::LOGIC_IMMO_NEUF;

    protected const SELECTOR_AD_WRAPPER    = '.contentads1';
    protected const SELECTOR_LOCATION      = '.adscustom3';
    protected const SELECTOR_BUILDING_NAME = '.adscustom3 span:first-child';

    /**
     * {@inheritDoc}
     */
    public function parseLocation(Crawler $crawler): ?string
    {
        $str = $crawler->filter(static::SELECTOR_LOCATION)->html();

        return trim(explode('<br>', $str)[1]);
    }
}
