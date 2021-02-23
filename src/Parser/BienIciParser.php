<?php

namespace App\Parser;

use App\Enum\Provider;
use Symfony\Component\DomCrawler\Crawler;

class BienIciParser extends AbstractParser
{
    protected const PROVIDER = Provider::BIENICI;

    protected const SELECTOR_AD_WRAPPER    = '.realEstateAd';
    protected const SELECTOR_LOCATION      = '.realEstateAdAddress a';
    protected const SELECTOR_BUILDING_NAME = '.realEstateAdTitle strong';
    protected const SELECTOR_DESCRIPTION   = '.newProperty';

    /**
     * {@inheritDoc}
     */
    protected function parsePhoto(Crawler $crawler): ?string
    {
        return str_replace(
            ['200x160', 'width=200&height=160'],
            ['600x370', 'width=600&height=370'],
            parent::parsePhoto($crawler)
        );
    }

    /**
     * {@inheritDoc}
     */
    protected function parseBuildingName(Crawler $crawler): ?string
    {
        $title = parent::parseBuildingName($crawler);

        // E.g.: "Les Jardins d'Antoine (1 à 4 pièces, 32 à 78 m²)" (vs "Appartement 3 pièces 71 m²")
        if (1 === preg_match('/(.+) \(.+\)/', $title, $matches)) {
            return $matches[1];
        }

        return null;
    }
}
