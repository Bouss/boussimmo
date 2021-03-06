<?php

namespace App\Parser;

use App\Enum\Provider;
use Exception;
use Symfony\Component\DomCrawler\Crawler;

class LogicImmoParser extends AbstractParser
{
    protected const PROVIDER = Provider::LOGIC_IMMO;

    protected const SELECTOR_AD_WRAPPER  = 'td[width="10"] + .full[width="270"]';
    protected const SELECTOR_LOCATION    = 'tr:nth-child(3) tr:nth-child(3) a';
    protected const SELECTOR_DESCRIPTION = 'tr:nth-child(3) tr:nth-child(4) a';
    protected const SELECTOR_PHOTO       = '.background';

    /**
     * {@inheritDoc}
     */
    protected function parsePhoto(Crawler $crawler): ?string
    {
        try {
            return $crawler->filter(static::SELECTOR_PHOTO)->attr('background');
        } catch (Exception) {
            return null;
        }
    }
}
