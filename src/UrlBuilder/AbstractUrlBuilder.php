<?php

namespace App\UrlBuilder;

use App\DataProvider\LocationProvider;
use App\DTO\City;

abstract class AbstractUrlBuilder implements UrlBuilderInterface
{
    public function __construct(
        protected LocationProvider $locationProvider
    ) {}

    /**
     * @param string[] $propertyTypes
     */
    public function build(
        string $cityName,
        array $propertyTypes,
        ?int $minPrice,
        int $maxPrice,
        ?int $minArea,
        ?int $maxArea,
        int $minRoomsCount
    ): string {
        $city = $this->locationProvider->find($cityName);

        $criteria = [$city, $propertyTypes, $minPrice, $maxPrice, $minArea, $maxArea, $minRoomsCount];

        $url = $this->buildPath(...$criteria);

        if (!empty($params = $this->buildQueryParameters(...$criteria))) {
            $url .= '?' . urldecode(http_build_query(array_merge(...$params)));
        }

        return $url;
    }

    /**
     * @param string[] $types
     */
    abstract protected function buildPath(
        City $city,
        array $types,
        ?int $minPrice,
        int $maxPrice,
        ?int $minArea,
        ?int $maxArea,
        int $minRoomsCount
    ): string;

    /**
     * @param string[] $types
     */
    abstract protected function buildQueryParameters(
        City $city,
        array $types,
        ?int $minPrice,
        int $maxPrice,
        ?int $minArea,
        ?int $maxArea,
        int $minRoomsCount
    ): array;
}
