<?php

namespace App\UrlBuilder;

use App\DTO\City;
use App\Enum\PropertyType;

class SuperimmoNeufUrlBuilder extends AbstractUrlBuilder
{
    private const MAX_ROOMS_COUNT = 5;

    /**
     * {@inheritDoc}
     */
    protected function buildPath(
        City $city,
        array $propertyTypes,
        ?int $minPrice,
        int $maxPrice,
        ?int $minArea,
        ?int $maxArea,
        int $minRoomsCount
    ): string
    {
        return sprintf('https://www.superimmoneuf.com/%s-%s',
            $this->buildPropertyTypesParam($propertyTypes),
            $this->buildLocationParam($city),
        );
    }

    /**
     * {@inheritDoc}
     */
    protected function buildQueryParameters(
        City $city,
        array $propertyTypes,
        ?int $minPrice,
        int $maxPrice,
        ?int $minArea,
        ?int $maxArea,
        int $minRoomsCount
    ): array
    {
        return [
            $this->buildPriceParam($maxPrice),
            $this->buildRoomsCountParam($minRoomsCount)
        ];
    }

    /**
     * @param City $city
     *
     * @return string
     */
    private function buildLocationParam(City $city): string
    {
        return sprintf('%s-%d', $city->getName(), $city->getDepartmentCode());
    }

    /**
     * @param string[] $types
     *
     * @return string
     */
    private function buildPropertyTypesParam(array $types): string
    {
        if (2 === count($types)) {
            return 'immobilier-neuf';
        }

        return str_replace([PropertyType::APARTMENT, PropertyType::HOUSE], ['appartement-neuf', 'maison-neuve'], $types[0]);
    }

    /**
     * @param int $maxPrice
     *
     * @return array
     */
    private function buildPriceParam(int $maxPrice): array
    {
        return ['_search[price_max]' => $maxPrice];
    }

    /**
     * @param int $minRoomsCount
     *
     * @return array
     */
    private function buildRoomsCountParam(int $minRoomsCount): array
    {
        return ['_search[rooms][]' => min($minRoomsCount, self::MAX_ROOMS_COUNT)];
    }
}
