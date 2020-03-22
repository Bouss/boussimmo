<?php

namespace App\Parser\EmailParser;

use App\Enum\Provider;
use App\Parser\AbstractParser;
use Exception;
use Symfony\Component\DomCrawler\Crawler;

class LogicImmoParser extends AbstractParser
{
    protected const SITE = Provider::LOGIC_IMMO;
    protected const SELECTOR_AD_WRAPPER = 'td[bgcolor="#ffffff"] td:nth-child(3)';
    protected const SELECTOR_EXTERNAL_ID = '';
    protected const SELECTOR_TITLE = '';
    protected const SELECTOR_DESCRIPTION = '';
    protected const SELECTOR_LOCATION = 'a[href*="description_ville"]';
    protected const SELECTOR_PUBLISHED_AT = '';
    protected const SELECTOR_URL = 'a:first-child';
    protected const SELECTOR_PRICE = 'b > a';
    protected const SELECTOR_AREA = 'td[style$="font-size:13px; color:#000000; padding-top:10px; padding-bottom:10px;"] > a';
    protected const SELECTOR_ROOMS_COUNT = 'td[style$="font-size:13px; color:#000000; padding-top:10px; padding-bottom:10px;"] > a';
    protected const SELECTOR_PHOTO = '[class$=background]';
    protected const SELECTOR_REAL_AGENT_ESTATE = '';
    protected const SELECTOR_NEW_BUILD = 'a:first-child[href*="neuf"]';
    protected const PUBLISHED_AT_FORMAT = '';

    /**
     * {@inheritDoc}
     */
    protected function getPhoto(Crawler $crawler): ?string
    {
        try {
            return $crawler->filter(static::SELECTOR_PHOTO)->attr('background');
        } catch (Exception $e) {
            return null;
        }
    }
}
