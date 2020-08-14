<?php

namespace App\Parser;

use App\Enum\Provider;
use App\Exception\ParseException;
use Symfony\Component\DomCrawler\Crawler;

class SuperimmoNeufParser extends AbstractParser
{
    protected const PROVIDER = Provider::SUPERIMMO_NEUF;

    protected const SELECTOR_AD_WRAPPER  = 'td[style="width: 540px;"]';
    protected const SELECTOR_TITLE       = 'table:nth-child(2) tr:nth-child(1) span';
    protected const SELECTOR_DESCRIPTION = 'table:nth-child(2) tr:nth-child(5) span';
    protected const SELECTOR_LOCATION    = 'table:nth-child(2) tr:nth-child(2) span';
    protected const SELECTOR_PRICE       = 'span[style*="color:#f90362"]'; // Not a structuring property because of a possible "à partir de" row before
    protected const SELECTOR_AREA        = 'table:nth-child(2) tr:nth-child(1) span';
    protected const SELECTOR_ROOMS_COUNT = 'table:nth-child(2) tr:nth-child(1) span';

    /**
     * {@inheritDoc}
     */
    protected function parsePrice(Crawler $crawler): ?float
    {
        // Some property ads don't have a price
        try {
            return parent::parsePrice($crawler);
        } catch (ParseException $e) {
            return null;
        }
    }

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
