<?php

namespace App\Parser;

use App\Enum\Provider;
use App\Exception\ParseException;
use Exception;
use Symfony\Component\DomCrawler\Crawler;

class LeBonCoinParser extends AbstractParser
{
    protected const PROVIDER = Provider::LEBONCOIN;

    protected const SELECTOR_AD_WRAPPER  = 'table[style*="border: solid 1px #e6ebef"]';
    protected const SELECTOR_TITLE       = 'td:nth-child(2) > a > span:first-of-type';
    protected const SELECTOR_LOCATION    = 'td:nth-child(2) > a > div > span';
    protected const SELECTOR_PHOTO       = 'td:nth-child(1) div';

    /**
     * {@inheritDoc}
     */
    protected function parsePhoto(Crawler $crawler): ?string
    {
        try {
            $backgroundImage = $crawler->filter(static::SELECTOR_PHOTO)->attr('style');

            if (1 === preg_match('/background-image: url\((.*)\)/', $backgroundImage, $matches)) {
                return $matches[1];
            }

            throw new ParseException('Error while parsing the photo: no property "background-image" found');
        } catch (Exception $e) {
            throw new ParseException('Error while parsing the photo: ' . $e->getMessage());
        }
    }
}
