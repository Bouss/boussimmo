<?php

namespace App\Parser;

use App\Enum\Provider;
use Symfony\Component\DomCrawler\Crawler;

class SuperimmoNeufParser extends AbstractParser
{
    protected const PROVIDER = Provider::SUPERIMMO;
    protected const SELECTOR_AD_WRAPPER  = 'td[style="width: 540px;"]';
    protected const SELECTOR_TITLE       = 'table:nth-child(2) tr:nth-child(1) span';
    protected const SELECTOR_DESCRIPTION = 'table:nth-child(2) tr:nth-child(5) span';
    protected const SELECTOR_LOCATION    = 'table:nth-child(2) tr:nth-child(2) span';
    protected const SELECTOR_URL         = 'a:first-child';
    protected const SELECTOR_ROOMS_COUNT = 'table:nth-child(2) tr:nth-child(1) span';
    protected const SELECTOR_PHOTO       = 'table:nth-child(1) img:first-child';

    /**
     * {@inheritDoc}
     */
    protected function getPhoto(Crawler $crawler): ?string
    {
        $photo = parent::getPhoto($crawler);

        $photo = substr($photo, strpos($photo, '#'));

        return str_replace('wide', 'biggest', $photo);
    }

    /**
     * {@inheritDoc}
     */
    protected function isNewBuild(Crawler $crawler, bool $nodeExistenceOnly = true): bool
    {
        return true;
    }
}
