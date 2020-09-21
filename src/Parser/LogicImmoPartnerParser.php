<?php

namespace App\Parser;

use App\Enum\Provider;
use Symfony\Component\DomCrawler\Crawler;

class LogicImmoPartnerParser extends AbstractParser
{
    protected const PROVIDER = Provider::LOGIC_IMMO;

    protected const SELECTOR_AD_WRAPPER  = 'table[bgcolor="#f1f1f1"] > tr:nth-child(n+6):not(:nth-last-child(-n+8)) table[width=600][bgcolor="#ffffff"]';
    protected const SELECTOR_DESCRIPTION = 'td[style*="font-size:12px"]';
    protected const SELECTOR_LOCATION    = 'td.mea_txt_ad3';
    protected const SELECTOR_PRICE       = 'td.mea_txt_ad2';
    protected const SELECTOR_AREA        = 'td.mea_txt_ad3';
    protected const SELECTOR_ROOMS_COUNT = 'td.mea_txt_ad3';

    /**
     * {@inheritDoc}
     */
    protected function parseLocation(Crawler $crawler): ?string
    {
        $data = parent::parseLocation($crawler);

        return trim(explode('|', $data)[0]);
    }
}
