<?php

namespace App\Parser;

use App\Enum\Provider;
use App\Exception\ParseException;
use Exception;
use Symfony\Component\DomCrawler\Crawler;
use function preg_match;

class PapNeufParser extends AbstractParser
{
    protected const PROVIDER = Provider::PAP_NEUF;

    protected const SELECTOR_AD_WRAPPER  = 'table:nth-child(2) tr:nth-child(2n+4):not(:nth-last-child(-n+5))';
    protected const SELECTOR_LOCATION    = 'td:nth-child(4) b:first-child';
    protected const SELECTOR_NAME        = 'td:nth-child(4)';
    protected const SELECTOR_URL         = 'a:first-child';
    protected const SELECTOR_PRICE       = 'td:nth-child(4)';
    protected const SELECTOR_PHOTO       = 'img:first-child';

    /**
     * {@inheritDoc}
     */
    protected function parsePrice(Crawler $crawler): ?float
    {
        try {
            $priceStr = trim($crawler->filter(static::SELECTOR_PRICE)->text());
        } catch (Exception $e) {
            throw new ParseException('Error while parsing the price: ' . $e->getMessage());
        }

        return $this->formatter->parsePrice(str_replace('.', '', $priceStr));
    }

    /**
     * {@inheritDoc}
     */
    protected function parseName(Crawler $crawler): ?string
    {
        preg_match('/\) (.+) Adresse/', parent::parseName($crawler), $matches);

        return $matches[1] ?? null;
    }
}
