<?php

namespace App\Service;

use App\Form\Type\SortPropertyAdsType;

class PropertyAdSortResolver
{
    /**
     * @param string $sort
     *
     * @return array
     */
    public function resolve(string $sort): array
    {
        switch ($sort) {
            case SortPropertyAdsType::PUBLISHED_AT_ASC:
                return ['publishedAt', 1];
            case SortPropertyAdsType::PRICE_ASC:
                return ['price', 1];
            case SortPropertyAdsType::PRICE_DESC:
                return ['price', -1];
            case SortPropertyAdsType::AREA_ASC:
                return ['area', 1];
            case SortPropertyAdsType::AREA_DESC:
                return ['area', -1];
            case SortPropertyAdsType::PUBLISHED_AT_DESC:
            default:
                return ['publishedAt', -1];
        }
    }
}
