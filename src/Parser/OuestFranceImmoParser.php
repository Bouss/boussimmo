<?php

namespace App\Parser;

use App\Definition\SiteEnum;
use App\Util\NumberUtil;
use Exception;
use Symfony\Component\DomCrawler\Crawler;

class OuestFranceImmoParser extends AbstractParser
{
    protected const SELECTOR_AD_WRAPPER = '.annLink';
    protected const SELECTOR_TITLE = '.annTitre';
    protected const SELECTOR_DESCRIPTION = '.annTexte';
    protected const SELECTOR_LOCATION = '.annAdresse';
    protected const SELECTOR_PUBLISHED_AT = '.annDebAff';
    protected const SELECTOR_URL = '.annLink';
    protected const SELECTOR_PRICE = '.annPrix';
    protected const SELECTOR_AREA = '.annCriteres';
    protected const SELECTOR_ROOMS_COUNT = '.annTitre';
    protected const SELECTOR_PHOTO = '.annPhoto';
    protected const PUBLISHED_AT_FORMAT = 'd/m/y';

    private const UNIT_ROOM_COUNT = 'pièces';

    /**
     * {@inheritDoc}
     */
    protected function getSite(): string
    {
        return SiteEnum::OUESTFRANCE_IMMO;
    }

    /**
     * {@inheritDoc}
     */
    protected function getExternalId(Crawler $crawler): ?string
    {
        try {
            return $crawler->filter('div[data-id]')->attr('data-id');
        } catch (Exception $e) {
            return null;
        }
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

        return NumberUtil::extractFloat($areaStr);
    }

    /**
     * {@inheritDoc}
     */
    protected function getRoomsCount(Crawler $crawler): int
    {
        $title = $crawler->filter(self::SELECTOR_ROOMS_COUNT)->text();
        preg_match(sprintf('/([0-9]+)\s%s/',  self::UNIT_ROOM_COUNT), $title, $matches);

        return (int) $matches[1];
    }
}
