<?php

namespace App\Parser;

use App\Enum\Provider;
use Symfony\Component\DomCrawler\Crawler;

class OuestFranceImmoParser extends AbstractParser
{
    protected const PROVIDER = Provider::OUESTFRANCE_IMMO;
    protected const SELECTOR_AD_WRAPPER  = '.blocAnn';
    protected const SELECTOR_LOCATION    = 'tr:nth-child(2) div:nth-child(2) span';
    protected const SELECTOR_URL         = 'a:first-child';
    protected const SELECTOR_PRICE       = 'tr:nth-child(1) b';
    protected const SELECTOR_AREA        = 'tr:nth-child(3) font:nth-child(2) b';
    protected const SELECTOR_ROOMS_COUNT = 'tr:nth-child(3) font:nth-child(1) b';
    protected const SELECTOR_PHOTO       = 'img:first-child';

    /**
     * {@inheritDoc}
     */
    protected function parsePhoto(Crawler $crawler): ?string
    {
        $photo = parent::parsePhoto($crawler);

        return str_replace('276-207', '686-515', $photo);
    }
}
