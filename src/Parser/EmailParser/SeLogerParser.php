<?php

namespace App\Parser\EmailParser;

use App\Enum\Site;
use App\Parser\AbstractParser;
use Symfony\Component\DomCrawler\Crawler;

class SeLogerParser extends AbstractParser
{
    protected const SITE = Site::SELOGER;
    protected const SELECTOR_AD_WRAPPER = 'td[class$="two-column"]';
    protected const SELECTOR_EXTERNAL_ID = '';
    protected const SELECTOR_TITLE = '';
    protected const SELECTOR_DESCRIPTION = '';
    protected const SELECTOR_LOCATION = 'td[style$="font-size: 16px; line-height: 25px; color: #262626;"] a';
    protected const SELECTOR_PUBLISHED_AT = '';
    protected const SELECTOR_URL = 'a:first-child';
    protected const SELECTOR_PRICE = 'b';
    protected const SELECTOR_AREA = 'td[style$="font-size: 16px; line-height: 25px; color: #262626;"] a';
    protected const SELECTOR_ROOMS_COUNT = 'td[style$="font-size: 16px; line-height: 25px; color: #262626;"] a';
    protected const SELECTOR_PHOTO = 'img';
    protected const SELECTOR_REAL_AGENT_ESTATE = '';
    protected const SELECTOR_NEW_BUILD = '';
    protected const PUBLISHED_AT_FORMAT = '';

    /**
     * {@inheritDoc}
     */
    protected function getLocation(Crawler $crawler): ?string
    {
        $description = parent::getLocation($crawler);

        $location = explode('m²', $description)[1];

        return trim($location);
    }
}
