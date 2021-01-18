<?php

namespace App\Service;

use App\Enum\PropertySort;

class PropertySortResolver
{
    /**
     * @param string $sort
     *
     * @return array
     */
    public function resolve(string $sort): array
    {
        switch ($sort) {
            case PropertySort::PUBLISHED_AT_ASC:
                return ['publishedAt', 1];
            case PropertySort::PRICE_ASC:
                return ['price', 1];
            case PropertySort::PRICE_DESC:
                return ['price', -1];
            case PropertySort::AREA_ASC:
                return ['area', 1];
            case PropertySort::AREA_DESC:
                return ['area', -1];
            case PropertySort::PUBLISHED_AT_DESC:
            default:
                return ['publishedAt', -1];
        }
    }
}
