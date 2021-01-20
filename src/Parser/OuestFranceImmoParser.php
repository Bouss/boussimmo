<?php

namespace App\Parser;

use App\Enum\Provider;
use Symfony\Component\DomCrawler\Crawler;

class OuestFranceImmoParser extends AbstractParser
{
    protected const PROVIDER = Provider::OUESTFRANCE_IMMO;

    protected const SELECTOR_AD_WRAPPER  = 'td[style*="padding:5px 0;"]';
    protected const SELECTOR_LOCATION    = '.mj-column-per-50:nth-child(2) tr:nth-child(3) div';

    /**
     * {@inheritDoc}
     */
    protected function parsePhoto(Crawler $crawler): ?string
    {
        return str_replace('375-180', '1200-900', parent::parsePhoto($crawler));
    }
}
