<?php

namespace App\Parser\EmailParser;

use App\Enum\Provider;
use App\Parser\AbstractParser;
use Symfony\Component\DomCrawler\Crawler;

class BienIciParser extends AbstractParser
{
    protected const SITE = Provider::BIENICI;
    protected const SELECTOR_AD_WRAPPER  = '.realEstateAd';
    protected const SELECTOR_TITLE       = '.realEstateAdTitle strong';
    protected const SELECTOR_LOCATION    = '.realEstateAdAddress a';
    protected const SELECTOR_URL         = 'a:first-child';
    protected const SELECTOR_PRICE       = '.realEstateAdPrice strong';
    protected const SELECTOR_AREA        = '.realEstateAdTitle strong';
    protected const SELECTOR_ROOMS_COUNT = '.realEstateAdTitle strong';
    protected const SELECTOR_PHOTO       = '.realEstateAdPhoto img';
    protected const SELECTOR_NEW_BUILD   = 'span[style*="background: #ffb82f"]';

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

    /**
     * {@inheritDoc}
     */
    protected function isNewBuild(Crawler $crawler, bool $nodeExistenceOnly = true): bool
    {
        return parent::isNewBuild($crawler, false);
    }
}
