<?php

namespace App\Parser\WebParser;

use App\Enum\Provider;
use App\Util\NumericUtil;
use Exception;
use Symfony\Component\DomCrawler\Crawler;

class OuestFranceImmoParser extends AbstractWebParser
{
    protected const SITE = Provider::OUESTFRANCE_IMMO;
    protected const SELECTOR_NEXT_PAGE_URL = '.currentPage + * > a[data-page]';
    protected const SELECTOR_AD_WRAPPER = '.annLink';
    protected const SELECTOR_EXTERNAL_ID = 'div[data-id]';
    protected const SELECTOR_TITLE = '';
    protected const SELECTOR_DESCRIPTION = '.annTexte';
    protected const SELECTOR_LOCATION = '.annAdresse';
    protected const SELECTOR_PUBLISHED_AT = '.annDebAff';
    protected const SELECTOR_URL = '.annLink';
    protected const SELECTOR_PRICE = '.annPrix';
    protected const SELECTOR_AREA = '.annCriteres';
    protected const SELECTOR_ROOMS_COUNT = '.annTitre';
    protected const SELECTOR_PHOTO = '.annPhoto';
    protected const SELECTOR_REAL_AGENT_ESTATE = '';
    protected const SELECTOR_NEW_BUILD = '';
    protected const PUBLISHED_AT_FORMAT = 'd/m/y';

    private const BASE_URL = 'https://www.ouestfrance-immo.com';
    private const REGEX_ROOMS_COUNT = '/([0-9]+)\spièces/u';

    /**
     * {@inheritDoc}
     */
    protected function getNextPageUrl(Crawler $crawler): ?string
    {
        $nextPage = parent::getNextPageUrl($crawler);

        if (null === $nextPage) {
            return null;
        }

        return self::BASE_URL . $nextPage;
    }

    /**
     * {@inheritDoc}
     */
    protected function getUrl(Crawler $crawler): string
    {
        return self::BASE_URL . parent::getUrl($crawler);
    }

    /**
     * {@inheritDoc}
     */
    protected function getDescription(Crawler $crawler): ?string
    {
        $description = parent::getDescription($crawler);

        try {
            $criterias = $crawler->filter('.annCriteres')->text();
        } catch (Exception $e) {
            $criterias = null;
        }

        return $description . '<br>' . $criterias;
    }

    /**
     * {@inheritDoc}
     */
    protected function getLocation(Crawler $crawler): ?string
    {
        $location = parent::getLocation($crawler);

       if (null === $location) {
           try {
               $location = trim($crawler->filter('.annVille')->text());
           } catch (Exception $e) {
               return null;
           }
       }

        return $location;
    }

    /**
     * {@inheritDoc}
     */
    protected function getArea(Crawler $crawler): float
    {
        $criterias = parent::getArea($crawler);
        $areaStr = explode('|', $criterias)[0];

        return NumericUtil::extractFloat($areaStr);
    }

    /**
     * {@inheritDoc}
     */
    protected function getRoomsCount(Crawler $crawler): int
    {
        $title = $crawler->filter(self::SELECTOR_ROOMS_COUNT)->text();
        preg_match(self::REGEX_ROOMS_COUNT, $title, $matches);

        return (int) $matches[1];
    }
}
