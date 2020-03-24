<?php

namespace App\Service;

use App\Enum\PropertyAdSort;

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
            case PropertyAdSort::PUBLISHED_AT_ASC:
                return ['publishedAt', 1];
            case PropertyAdSort::PRICE_ASC:
                return ['price', 1];
            case PropertyAdSort::PRICE_DESC:
                return ['price', -1];
            case PropertyAdSort::AREA_ASC:
                return ['area', 1];
            case PropertyAdSort::AREA_DESC:
                return ['area', -1];
            case PropertyAdSort::PUBLISHED_AT_DESC:
            default:
                return ['publishedAt', -1];
        }
    }
}
