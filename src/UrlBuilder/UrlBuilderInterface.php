<?php

namespace App\UrlBuilder;

interface UrlBuilderInterface
{
    /**
     * @param string   $city
     * @param string[] $propertyTypes
     * @param int|null $minPrice
     * @param int      $maxPrice
     * @param int|null $minArea
     * @param int|null $maxArea
     * @param int      $minRoomsCount
     *
     * @return string
     */
    public function build(
        string $city,
        array $propertyTypes,
        ?int $minPrice,
        int $maxPrice,
        ?int $minArea,
        ?int $maxArea,
        int $minRoomsCount
    ): string;
}
