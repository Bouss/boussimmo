<?php

namespace App\Parser\EmailParser;

use App\Enum\Provider;
use App\Parser\AbstractParser;
use Symfony\Component\DomCrawler\Crawler;

class LogicImmo2Parser extends AbstractParser
{
    protected const SITE = Provider::LOGIC_IMMO;
    protected const SELECTOR_AD_WRAPPER = 'table[bgcolor="#f1f1f1"] > tr:nth-child(n+6):not(:nth-last-child(-n+8)) table[width=600][bgcolor="#ffffff"]';
    protected const SELECTOR_EXTERNAL_ID = '';
    protected const SELECTOR_TITLE = '';
    protected const SELECTOR_DESCRIPTION = 'td[style*="font-size:12px"]';
    protected const SELECTOR_LOCATION = 'td[style*="font-size:14px"]';
    protected const SELECTOR_PUBLISHED_AT = '';
    protected const SELECTOR_URL = 'a:first-child';
    protected const SELECTOR_PRICE = 'td[style*="font-size:18px"]:nth-child(2)';
    protected const SELECTOR_AREA = 'td[style*="font-size:14px"]';
    protected const SELECTOR_ROOMS_COUNT = '';
    protected const SELECTOR_PHOTO = 'img';
    protected const SELECTOR_REAL_AGENT_ESTATE = '';
    protected const SELECTOR_NEW_BUILD = '';
    protected const PUBLISHED_AT_FORMAT = '';

    /**
     * {@inheritDoc}
     */
    protected function getLocation(Crawler $crawler): ?string
    {
        $data = parent::getLocation($crawler);

        return explode('|', $data)[0];
    }
}
