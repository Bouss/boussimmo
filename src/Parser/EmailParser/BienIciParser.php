<?php

namespace App\Parser\EmailParser;

use App\Definition\SiteEnum;
use App\Parser\AbstractParser;
use Symfony\Component\DomCrawler\Crawler;

class BienIciParser extends AbstractParser
{
    protected const SITE = SiteEnum::BIENICI;
    protected const SELECTOR_AD_WRAPPER = '[class$="realEstateAd"]';
    protected const SELECTOR_EXTERNAL_ID = '';
    protected const SELECTOR_TITLE = '';
    protected const SELECTOR_DESCRIPTION = '';
    protected const SELECTOR_LOCATION = '[class$="realEstateAdAddress"] > a';
    protected const SELECTOR_PUBLISHED_AT = '';
    protected const SELECTOR_URL = 'a:first-child';
    protected const SELECTOR_PRICE = '[class$="realEstateAdPrice"] strong';
    protected const SELECTOR_AREA = '[class$="realEstateAdTitle"] strong';
    protected const SELECTOR_ROOMS_COUNT = '[class$="realEstateAdTitle"] strong';
    protected const SELECTOR_PHOTO = '[class$=realEstateAdPhoto] img';
    protected const SELECTOR_REAL_AGENT_ESTATE = '';
    protected const SELECTOR_NEW_BUILD = '';
    protected const PUBLISHED_AT_FORMAT = '';

    /**
     * {@inheritDoc}
     */
    protected function getPhoto(Crawler $crawler): ?string
    {
        $photo = parent::getPhoto($crawler);

        return str_replace(
               ['200x160', 'width=200&height=160'],
               ['600x370', 'width=600&height=370'],
               $photo
        );
    }
}
