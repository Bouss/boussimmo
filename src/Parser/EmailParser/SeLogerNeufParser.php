<?php

namespace App\Parser\EmailParser;

use App\Enum\Provider;
use App\Parser\AbstractParser;
use Symfony\Component\DomCrawler\Crawler;

class SeLogerNeufParser extends AbstractParser
{
    protected const SITE = Provider::SELOGER;
    protected const SELECTOR_AD_WRAPPER = 'td[style*="text-align:center;font-size:0"]';
    protected const SELECTOR_EXTERNAL_ID = '';
    protected const SELECTOR_TITLE = '';
    protected const SELECTOR_DESCRIPTION = '';
    protected const SELECTOR_LOCATION = 'span[style*="font-size: 14px;line-height:15px"] > b:first-child';
    protected const SELECTOR_PUBLISHED_AT = '';
    protected const SELECTOR_URL = 'a';
    protected const SELECTOR_PRICE = '';
    protected const SELECTOR_AREA = '';
    protected const SELECTOR_ROOMS_COUNT = '';
    protected const SELECTOR_PHOTO = 'img';
    protected const SELECTOR_REAL_AGENT_ESTATE = '';
    protected const SELECTOR_NEW_BUILD = '';
    protected const PUBLISHED_AT_FORMAT = '';

    /**
     * {@inheritDoc}
     */
    protected function isNewBuild(Crawler $crawler, bool $nodeExistenceOnly = true): bool
    {
        return true;
    }
}
