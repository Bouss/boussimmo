<?php

namespace App\Parser;

use App\Enum\Provider;
use Symfony\Component\DomCrawler\Crawler;

class BienIciParser extends AbstractParser
{
    protected const PROVIDER = Provider::BIENICI;

    protected const SELECTOR_AD_WRAPPER  = '.realEstateAd';
    protected const SELECTOR_LOCATION    = '.realEstateAdAddress a';
    protected const SELECTOR_NEW_BUILD   = 'span[style*="background: #ffb82f"]';

    /**
     * {@inheritDoc}
     */
    protected function parsePhoto(Crawler $crawler): ?string
    {
        return str_replace(
            ['200x160', 'width=200&height=160'],
            ['600x370', 'width=600&height=370'],
            parent::parsePhoto($crawler)
        );
    }

    /**
     * {@inheritDoc}
     */
    protected function parseNewBuild(Crawler $crawler, bool $nodeExistenceOnly = true): bool
    {
        return parent::parseNewBuild($crawler, false);
    }
}
