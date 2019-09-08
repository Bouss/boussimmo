<?php

namespace App\Parser\WebParser;

use App\Definition\SiteEnum;
use Symfony\Component\DomCrawler\Crawler;

class SeLogerParser extends AbstractWebParser
{
    protected const SITE = SiteEnum::SELOGER;
    protected const SELECTOR_NEXT_PAGE_URL = '.pagination-next';
    protected const SELECTOR_AD_WRAPPER = '.c-pa-list';
    protected const SELECTOR_EXTERNAL_ID = '.c-pa-list[id]';
    protected const SELECTOR_TITLE = '';
    protected const SELECTOR_DESCRIPTION = '';
    protected const SELECTOR_LOCATION = '.c-pa-city';
    protected const SELECTOR_PUBLISHED_AT = '';
    protected const SELECTOR_URL = '.link_AB';
    protected const SELECTOR_PRICE = '.c-pa-cprice';
    protected const SELECTOR_AREA = '.c-pa-criterion em:nth-child(3)';
    protected const SELECTOR_ROOMS_COUNT = '.c-pa-criterion em:nth-child(1)';
    protected const SELECTOR_PHOTO = '.link_AB img';
    protected const SELECTOR_REAL_AGENT_ESTATE = '';
    protected const SELECTOR_NEW_BUILD = '.c-pa-link.link_AB:contains(\'neuf\')';
    protected const PUBLISHED_AT_FORMAT = '';

    private const PROTOCOL = 'https:';

    /**
     * {@inheritDoc}
     */
    protected function getNextPageUrl(Crawler $crawler): ?string
    {
        $nextPage = parent::getNextPageUrl($crawler);

        if (null === $nextPage) {
            return null;
        }

        return self::PROTOCOL . $nextPage;
    }
}
