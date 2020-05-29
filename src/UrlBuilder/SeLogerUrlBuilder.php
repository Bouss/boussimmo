<?php

namespace App\UrlBuilder;

use App\DTO\City;
use App\Enum\PropertyType;

class SeLogerUrlBuilder extends AbstractUrlBuilder
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
        return 'https://www.seloger.com/list.html';
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
            ['projects' => '2,5'],
            $this->buildPropertyTypesParam($propertyTypes),
            ['natures' => '1,2,4'],
            $this->buildLocationParam($city),
            $this->buildPriceParam($minPrice, $maxPrice),
            $this->buildAreaParam($minArea, $maxArea),
            $this->buildRoomsCountParam($minRoomsCount),
            ['enterprise' => 0],
            ['qsVersion' => '1.0']
        ];
    }

    /**
     * @param City $city
     *
     * @return array
     */
    private function buildLocationParam(City $city): array
    {
        return ['places' => sprintf('[{ci:%s}]', substr_replace($city->getInseeCode(), '0', 2, 0))];
    }

    /**
     * @param string[] $types
     *
     * @return array
     */
    private function buildPropertyTypesParam(array $types): array
    {
        $types = str_replace([PropertyType::APARTMENT, PropertyType::HOUSE], [1, 2], $types);

        return ['types' => implode(',', $types)];
    }

    /**
     * @param int|null $minPrice
     * @param int      $maxPrice
     *
     * @return array
     */
    private function buildPriceParam(?int $minPrice, int $maxPrice): array
    {
        $minPrice = $minPrice ?: 'NaN';

        return ['price' => "$minPrice/$maxPrice"];
    }

    /**
     * @param int|null $minArea
     * @param int|null $maxArea
     *
     * @return array
     */
    private function buildAreaParam(?int $minArea, ?int $maxArea): array
    {
        if (null === $minArea && null === $maxArea) {
            return [];
        }

        $minArea = $minArea ?: 'NaN';
        $maxArea = $maxArea ?: 'NaN';

        return ['surface' => "$minArea/$maxArea"];
    }

    /**
     * @param int $minRoomsCount
     *
     * @return array
     */
    private function buildRoomsCountParam(int $minRoomsCount): array
    {
        return ['rooms' => min($minRoomsCount, self::MAX_ROOMS_COUNT)];
    }
}
