<?php

namespace App\Parser\EmailParser;

use App\Enum\Provider;
use App\Parser\AbstractParser;
use Symfony\Component\DomCrawler\Crawler;

class SeLogerNeufParser extends AbstractParser
{
    protected const SITE = Provider::SELOGER;
    protected const SELECTOR_AD_WRAPPER = 'td[style*="padding-left:20px"] > .outer';
    protected const SELECTOR_TITLE      = '.contents tr:nth-child(2) span:first-child';
    protected const SELECTOR_LOCATION   = '.contents tr:nth-child(2) span b';
    protected const SELECTOR_URL        = 'a:first-child';
    protected const SELECTOR_PHOTO      = '.column:nth-child(1) img:first-child';

    /**
     * {@inheritDoc}
     */
    protected function isNewBuild(Crawler $crawler, bool $nodeExistenceOnly = true): bool
    {
        return true;
    }
}
