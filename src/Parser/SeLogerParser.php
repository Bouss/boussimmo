<?php

namespace App\Parser;

use App\Enum\Provider;
use Symfony\Component\DomCrawler\Crawler;

class SeLogerParser extends AbstractParser
{
    protected const PROVIDER = Provider::SELOGER;

    protected const SELECTOR_AD_WRAPPER  = '.two-column > a[_category="Bloc annonce"]'; // Ignore "Recommendation" ads
    protected const SELECTOR_LOCATION    = '.contents tr:nth-child(2) a';

    /**
     * {@inheritDoc}
     */
    protected function parseLocation(Crawler $crawler): ?string
    {
        $description = parent::parseLocation($crawler);

        if (1 === preg_match('/m²\s([-\w\s]+\s\(\d+\))/u', $description,$matches)) {
            return trim($matches[1]);
        }

        return null;
    }
}
