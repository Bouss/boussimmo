<?php

namespace App\Parser;

use App\Enum\Provider;
use Symfony\Component\DomCrawler\Crawler;

class LogicImmoNeufPartnerParser extends AbstractParser
{
    protected const PROVIDER = Provider::LOGIC_IMMO_NEUF;

    protected const SELECTOR_AD_WRAPPER    = 'body > table:first-of-type tr:nth-child(6) > td > table[width="600"]';
    protected const SELECTOR_LOCATION      = 'td.mea_txt_ad3';
    protected const SELECTOR_BUILDING_NAME = 'td.mea_txt_ad1';

    /**
     * {@inheritDoc}
     */
    protected function createCrawler(string $html): Crawler
    {
        // Inject "border-collapse: collapse" on <table> to make <tr> selectable
        $crawler = new Crawler($html);
        $crawler->filter('body > table:first-of-type')->getNode(0)->setAttribute('style', 'border-collapse: collapse;');

        return $crawler;
    }
}
