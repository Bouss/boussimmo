<?php

namespace App\Parser;

use App\Enum\Provider;
use Exception;
use Symfony\Component\DomCrawler\Crawler;

class ParuVenduParser extends AbstractParser
{
    protected const PROVIDER = Provider::PARUVENDU;

    protected const SELECTOR_AD_WRAPPER = 'table[style*="border:1px solid #d9d8d4"]';
    protected const SELECTOR_LOCATION   = 'tr:nth-child(3) td:nth-child(2) span:first-of-type';

    /**
     * {@inheritDoc}
     */
    protected function createCrawler(string $html): Crawler
    {
        // Fixes the page encoding
        $html = str_replace('iso-8859-1', 'UTF-8', $html);

        return new Crawler($html);
    }

    /**
     * {@inheritDoc}
     */
    protected function parseLocation(Crawler $crawler): ?string
    {
        try {
            $description = trim($crawler->filter(static::SELECTOR_LOCATION)->text());
        } catch (Exception) {
            return null;
        }

        // E.g.: "- Saint-Herblain (44800)"
        if (1 === preg_match('/- ((?:\w+(?:(?:-|\s)\w+)?)+ \(\d+\))(?:$| -)/u', $description, $matches)) {
            return $matches[1];
        }

        return null;
    }
}
