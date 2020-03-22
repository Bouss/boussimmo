<?php

namespace App\Service;

class PropertyAdSortResolver
{
    public const PUBLISHED_AT_ASC = 'published_at_asc';
    public const PUBLISHED_AT_DESC = 'published_at_desc';
    public const PRICE_ASC = 'price_asc';
    public const PRICE_DESC = 'price_desc';
    public const AREA_ASC = 'area_asc';
    public const AREA_DESC = 'area_desc';

    /**
     * @param string $sort
     *
     * @return array
     */
    public function resolve(string $sort): array
    {
        switch ($sort) {
            case self::PUBLISHED_AT_ASC:
                return ['publishedAt', 1];
            case self::PRICE_ASC:
                return ['price', 1];
            case self::PRICE_DESC:
                return ['price', -1];
            case self::AREA_ASC:
                return ['area', 1];
            case self::AREA_DESC:
                return ['area', -1];
            case self::PUBLISHED_AT_DESC:
            default:
                return ['publishedAt', -1];
        }
    }
}
