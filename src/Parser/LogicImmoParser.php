<?php

namespace App\Parser;

use App\Definition\SiteEnum;
use Exception;
use Symfony\Component\DomCrawler\Crawler;

class LogicImmoParser extends AbstractParser
{
    protected const SITE = SiteEnum::LOGIC_IMMO;
    protected const SELECTOR_NEXT_PAGE_URL = '.btn-maincolor > .btn-lightgrey';
    protected const SELECTOR_AD_WRAPPER = '.offer-list-item';
    protected const SELECTOR_EXTERNAL_ID = 'div[id]';
    protected const SELECTOR_TITLE = '';
    protected const SELECTOR_DESCRIPTION = '';
    protected const SELECTOR_LOCATION = '.offer-details-location--locality';
    protected const SELECTOR_PUBLISHED_AT = '';
    protected const SELECTOR_URL = 'a.offer-link';
    protected const SELECTOR_PRICE = '.offer-price';
    protected const SELECTOR_AREA = '.offer-area-number';
    protected const SELECTOR_ROOMS_COUNT = '.offer-rooms-number';
    protected const SELECTOR_PHOTO = '.thumb-link img';
    protected const SELECTOR_REAL_AGENT_ESTATE = '';
    protected const SELECTOR_NEW_BUILD = '.availability.bold:contains(\'neuf\')';
    protected const PUBLISHED_AT_FORMAT = '';

    /**
     * {@inheritDoc}
     */
    protected function getLocation(Crawler $crawler): ?string
    {
        $city = parent::getLocation($crawler);

        try {
            $district = $crawler->filter('.offer-details-location--sector')->text();

            return $city . ' ' . $district;
        } catch (Exception $e) {
            try {
                $district = $crawler->filter('.offer-details-location--city')->text();

                return $city . ' ' . $district;
            } catch (Exception $e) {
                return null;
            }
        }
    }
}
