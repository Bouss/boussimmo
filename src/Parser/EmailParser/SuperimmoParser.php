<?php

namespace App\Parser\EmailParser;

use App\Enum\Provider;
use App\Exception\ParseException;
use App\Parser\AbstractParser;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Note: some newsletters start with an advertising, uncatchable for the moment (2020-04-12)
 */
class SuperimmoParser extends AbstractParser
{
    protected const SITE = Provider::SUPERIMMO;
    protected const SELECTOR_AD_WRAPPER  = 'td[style="width: 540px;"]';
    protected const SELECTOR_TITLE       = 'table:nth-child(2) tr:nth-child(1) span';
    protected const SELECTOR_DESCRIPTION = 'span[style="font-size: 14px;color:#282828;"]'; // Not a structuring property because of a possible "à partir de" row before
    protected const SELECTOR_LOCATION    = 'table:nth-child(2) tr:nth-child(2) span';
    protected const SELECTOR_URL         = 'a:first-child';
    protected const SELECTOR_PRICE       = 'span[style*="color:#f90362"]'; // Not a structuring property because of a possible "à partir de" row before
    protected const SELECTOR_AREA        = 'table:nth-child(2) tr:nth-child(1) span';
    protected const SELECTOR_PHOTO       = 'table:nth-child(1) img:first-child';

    /**
     * {@inheritDoc}
     */
    protected function getPrice(Crawler $crawler): ?float
    {
        // Some property ads don't have a price
        try {
            return parent::getPrice($crawler);
        } catch (ParseException $e) {
            return null;
        }

    }

    /**
     * {@inheritDoc}
     */
    protected function getPhoto(Crawler $crawler): ?string
    {
        $photo = parent::getPhoto($crawler);

        $photo = substr($photo, strpos($photo, '#'));

        return str_replace('wide', 'biggest', $photo);
    }
}
