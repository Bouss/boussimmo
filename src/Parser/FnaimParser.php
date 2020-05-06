<?php

namespace App\Parser;

use Symfony\Component\DomCrawler\Crawler;

class FnaimParser extends AbstractParser
{
    protected const PROVIDER = 'fnaim';
    protected const SELECTOR_AD_WRAPPER = '[class*=item]';
    protected const SELECTOR_TITLE = '';
    protected const SELECTOR_DESCRIPTION = '[class$=desc]';
    protected const SELECTOR_LOCATION = 'h3 + p';
    protected const SELECTOR_PUBLISHED_AT = '';
    protected const SELECTOR_URL = 'a:first-child';
    protected const SELECTOR_PRICE = 'h3';
    protected const SELECTOR_AREA = 'h3 > a';
    protected const SELECTOR_ROOMS_COUNT = 'h3 > a';
    protected const SELECTOR_PHOTO = 'img';
    protected const SELECTOR_NEW_BUILD = '';

    /**
     * {@inheritDoc}
     */
    protected function parsePhoto(Crawler $crawler): ?string
    {
        return str_replace('images2', 'images1', parent::parsePhoto($crawler));
    }
}
