<?php

namespace App\Parser;

use App\Enum\Provider;
use Symfony\Component\DomCrawler\Crawler;

class SuperimmoNeufParser extends AbstractParser
{
    protected const PROVIDER = Provider::SUPERIMMO_NEUF;

    protected const SELECTOR_AD_WRAPPER  = 'td[style="width: 540px;"]';
    protected const SELECTOR_LOCATION    = 'table:nth-child(2) tr:nth-child(2) span';
    protected const SELECTOR_TITLE       = 'table:nth-child(2) tr:nth-child(1) span';
    protected const SELECTOR_DESCRIPTION = 'table:nth-child(2) tr:nth-child(5) span';

    /**
     * {@inheritDoc}
     */
    protected function parsePhoto(Crawler $crawler): ?string
    {
        $photo = parent::parsePhoto($crawler);
        $photo = substr($photo, strpos($photo, '#'));

        return str_replace('wide', 'biggest', $photo);
    }
}
