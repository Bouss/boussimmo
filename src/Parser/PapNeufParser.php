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

    /**
     * {@inheritDoc}
     */
    protected function parsePrice(Crawler $crawler): ?float
    {
        return $this->formatter->parsePrice(str_replace('.', '', $crawler->html()));
    }

    /**
     * {@inheritDoc}
     */
    protected function parseName(Crawler $crawler): ?string
    {
        if (1 === preg_match('/\)(.+)Adresse/', parent::parseName($crawler), $matches)) {
            return trim($matches[1]);
        }

        return null;
    }
}
