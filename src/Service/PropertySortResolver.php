<?php

namespace App\Service;

use App\Enum\PropertySort;

class PropertySortResolver
{
    public function resolve(string $sort): array
    {
        return match ($sort) {
            PropertySort::PUBLISHED_AT_ASC => ['publishedAt', 1],
            PropertySort::PRICE_ASC => ['price', 1],
            PropertySort::PRICE_DESC => ['price', -1],
            PropertySort::AREA_ASC => ['area', 1],
            PropertySort::AREA_DESC => ['area', -1],
            default => ['publishedAt', -1],
        };
    }
}
