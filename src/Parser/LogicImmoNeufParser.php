<?php

namespace App\Parser;

use App\Enum\Provider;
use Symfony\Component\DomCrawler\Crawler;

class LogicImmoNeufParser extends AbstractParser
{
    protected const PROVIDER = Provider::LOGIC_IMMO;
    protected const SELECTOR_AD_WRAPPER = '[class$="contentads1"]';
    protected const SELECTOR_TITLE = '';
    protected const SELECTOR_DESCRIPTION = '';
    protected const SELECTOR_LOCATION = '[class$="adscustom3"]';
    protected const SELECTOR_PUBLISHED_AT = '';
    protected const SELECTOR_URL = '[class$="adscustom4"] > a';
    protected const SELECTOR_PRICE = '[class$="adscustom4"]';
    protected const SELECTOR_AREA = '';
    protected const SELECTOR_ROOMS_COUNT = '';
    protected const SELECTOR_PHOTO = '[class$="adscustom2"]';
    protected const SELECTOR_NEW_BUILD = '';

    /**
     * {@inheritDoc}
     */
    protected function isNewBuild(Crawler $crawler, $nodeExistenceOnly = true): bool
    {
        return true;
    }
}
