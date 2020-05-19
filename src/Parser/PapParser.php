<?php

namespace App\Parser;

use App\Enum\Provider;
use Symfony\Component\DomCrawler\Crawler;

class PapParser extends AbstractParser
{
    protected const PROVIDER = Provider::PAP;

    protected const SELECTOR_AD_WRAPPER  = 'table tr:nth-child(n+3):not(:last-child)';
    protected const SELECTOR_DESCRIPTION = 'td:nth-child(2)';
    protected const SELECTOR_NAME        = 'td:nth-child(2)';
    protected const SELECTOR_LOCATION    = 'td:nth-child(2) b';
    protected const SELECTOR_URL         = 'a:first-child';
    protected const SELECTOR_PRICE       = 'td:nth-child(2)';
    protected const SELECTOR_AREA        = 'td:nth-child(2)';
    protected const SELECTOR_ROOMS_COUNT = 'td:nth-child(2)';
    protected const SELECTOR_PHOTO       = 'img:first-child';

    /**
     * {@inheritDoc}
     */
    protected function createCrawler(string $html): Crawler
    {
        // Inject "border-collapse: collapse" on <table> to make <tr> selectable
        $crawler = new Crawler($html);
        $crawler->filter('table')->getNode(0)->setAttribute('style', 'border-collapse: collapse');

        return $crawler;
    }

    /**
     * {@inheritDoc}
     */
    protected function parseName(Crawler $crawler): ?string
    {
        if (preg_match('/\)(.+) -/', parent::parseName($crawler), $matches)) {
            return $matches[1];
        }

        return null;
    }
}
