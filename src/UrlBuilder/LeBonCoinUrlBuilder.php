<?php

namespace App\UrlBuilder;

use App\DTO\City;
use App\Enum\PropertyType;

class LeBonCoinUrlBuilder extends AbstractUrlBuilder
{
    private const MAX_ROOMS_COUNT = 8;

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
        return 'https://www.leboncoin.fr/recherche/';
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
        $params = [
            ['category' => 9],
            $this->buildLocationParam($city),
            $this->buildPropertyTypesParam($propertyTypes),
            $this->buildPriceParam($minPrice, $maxPrice),
            $this->buildAreaParam($minArea, $maxArea),
            ['immo_sell_type' => 'old,new'],
        ];

        if ($minRoomsCount > 1) {
            $params[] = $this->buildRoomsCountParam($minRoomsCount);
        }

        return $params;
    }

    /**
     * @param City $city
     *
     * @return array
     */
    private function buildLocationParam(City $city): array
    {
        return ['locations' => ucfirst($city->getName())];
    }

    /**
     * @param string[] $types
     *
     * @return array
     */
    private function buildPropertyTypesParam(array $types): array
    {
        $types = str_replace([PropertyType::HOUSE, PropertyType::APARTMENT], [1, 2], $types);

        return ['real_estate_type' => implode(',', $types)];
    }

    /**
     * @param int|null $minPrice
     * @param int      $maxPrice
     *
     * @return array
     */
    private function buildPriceParam(?int $minPrice, int $maxPrice): array
    {
        $minPrice = $minPrice ?: 'min';

        return ['price' => "$minPrice-$maxPrice"];
    }

    /**
     * @param int|null $minArea
     * @param int|null $maxArea
     *
     * @return array
     */
    private function buildAreaParam(?int $minArea, ?int $maxArea = null): array
    {
        if (null === $minArea && null === $maxArea) {
            return [];
        }

        $minArea = $minArea ?: 'min';
        $maxArea = $maxArea ?: 'max';

        return ['square' => "$minArea-$maxArea"];
    }

    /**
     * @param int $minRoomsCount
     *
     * @return array
     */
    private function buildRoomsCountParam(int $minRoomsCount): array
    {
        $minRoomsCount = min($minRoomsCount, self::MAX_ROOMS_COUNT);

        return ['rooms' => "$minRoomsCount-max"];
    }
}
