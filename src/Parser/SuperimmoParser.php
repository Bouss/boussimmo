<?php

namespace App\Parser;

use App\Enum\Provider;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Note: some newsletters start with an advertising, uncatchable for the moment (2020-04-12)
 */
class SuperimmoParser extends AbstractParser
{
    protected const PROVIDER = Provider::SUPERIMMO;

    protected const SELECTOR_AD_WRAPPER  = 'td[style="width: 540px;"]';
    protected const SELECTOR_TITLE       = 'table:nth-child(2) tr:nth-child(1) span';
    protected const SELECTOR_DESCRIPTION = 'span[style="font-size: 14px;color:#282828;"]'; // Not a structuring property because of a possible "à partir de" row before
    protected const SELECTOR_LOCATION    = 'table:nth-child(2) tr:nth-child(2) span';

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
