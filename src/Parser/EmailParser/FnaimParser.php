<?php

namespace App\Parser\EmailParser;

use App\Definition\SiteEnum;
use App\Parser\AbstractParser;
use Symfony\Component\DomCrawler\Crawler;

class FnaimParser extends AbstractParser
{
    protected const SITE = SiteEnum::FNAIM;
    protected const SELECTOR_AD_WRAPPER = '[class*=item]';
    protected const SELECTOR_EXTERNAL_ID = '';
    protected const SELECTOR_TITLE = '';
    protected const SELECTOR_DESCRIPTION = '[class$=desc]';
    protected const SELECTOR_LOCATION = 'h3 + p';
    protected const SELECTOR_PUBLISHED_AT = '';
    protected const SELECTOR_URL = 'a:first-child';
    protected const SELECTOR_PRICE = 'h3';
    protected const SELECTOR_AREA = 'h3 > a';
    protected const SELECTOR_ROOMS_COUNT = 'h3 > a';
    protected const SELECTOR_PHOTO = 'img';
    protected const SELECTOR_REAL_AGENT_ESTATE = '';
    protected const SELECTOR_NEW_BUILD = '';
    protected const PUBLISHED_AT_FORMAT = '';

    /**
     * {@inheritDoc}
     */
    protected function getPhoto(Crawler $crawler): ?string
    {
        $photo = parent::getPhoto($crawler);

        return str_replace('images2', 'images1', $photo);
    }
}
