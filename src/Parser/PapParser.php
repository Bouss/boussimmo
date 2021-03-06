<?php

namespace App\Parser;

use App\Enum\Provider;
use App\Util\NumericUtil;
use Symfony\Component\DomCrawler\Crawler;

class PapParser extends AbstractParser
{
    protected const PROVIDER = Provider::PAP;

    protected const SELECTOR_AD_WRAPPER    = 'table tr:nth-child(n+3):not(:last-child)';
    protected const SELECTOR_LOCATION      = 'td:nth-child(2) b';
    protected const SELECTOR_BUILDING_NAME = 'td:nth-child(2)';
    protected const SELECTOR_DESCRIPTION   = 'td:nth-child(2)';

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
    protected function parsePrice(Crawler $crawler): ?float
    {
        return NumericUtil::parsePrice(str_replace('.', '', $crawler->html()));
    }

    /**
     * {@inheritDoc}
     */
    protected function parseBuildingName(Crawler $crawler): ?string
    {
        if (1 === preg_match('/\)(.+) -/', parent::parseBuildingName($crawler), $matches)) {
            return trim($matches[1]);
        }

        return null;
    }
}
