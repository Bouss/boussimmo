<?php

namespace App\Parser;

use App\Enum\Provider;
use Symfony\Component\DomCrawler\Crawler;

class SeLogerParser extends AbstractParser
{
    protected const PROVIDER = Provider::SELOGER;

    protected const SELECTOR_AD_WRAPPER  = '.two-column > a[_category="Bloc annonce"]'; // Ignore "Recommendation" ads
    protected const SELECTOR_LOCATION    = '.contents tr:nth-child(2) a';
    protected const SELECTOR_URL         = 'a';
    protected const SELECTOR_PRICE       = '.contents tr:nth-child(1) a';
    protected const SELECTOR_AREA        = '.contents tr:nth-child(2) a';
    protected const SELECTOR_ROOMS_COUNT = '.contents tr:nth-child(2) a';
    protected const SELECTOR_PHOTO       = '.column:nth-child(1) img:first-child';

    /**
     * {@inheritDoc}
     */
    protected function parseLocation(Crawler $crawler): ?string
    {
        $description = parent::parseLocation($crawler);

        $location = explode('m²', $description)[1];

        return trim($location);
    }
}
