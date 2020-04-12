<?php

namespace App\Parser\EmailParser;

use App\Enum\Provider;
use App\Parser\AbstractParser;
use Symfony\Component\DomCrawler\Crawler;

class SuperimmoParser extends AbstractParser
{
    protected const SITE = Provider::SUPERIMMO;
    protected const SELECTOR_AD_WRAPPER  = 'td[style="width: 540px;"]';
    protected const SELECTOR_TITLE       = 'span[style*="color:#0063c6"]';
    protected const SELECTOR_DESCRIPTION = 'span[style="font-size: 14px;color:#282828;"]';
    protected const SELECTOR_LOCATION    = 'span[style*="font-size: 18px"][style*="color:#282828"]';
    protected const SELECTOR_URL         = 'a:first-child';
    protected const SELECTOR_PRICE       = 'span[style*="color:#f90362"]';
    protected const SELECTOR_AREA        = 'span[style*="color:#0063c6"]';
    protected const SELECTOR_PHOTO       = 'img:first-child';

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
