<?php

namespace App\UrlBuilder;

use App\DTO\City;
use App\Enum\PropertyType;

class OuestFranceImmoUrlBuilder extends AbstractUrlBuilder
{
    private const MAX_ROOMS_COUNT = 6;

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
        return sprintf('https://www.ouestfrance-immo.com/acheter/%s/', $this->buildLocationParam($city));
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
            $this->buildPropertyTypesParam($propertyTypes),
            $this->buildPriceParam($minPrice, $maxPrice),
            $this->buildAreaParam($minArea, $maxArea),
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
        return sprintf('%s-%s-%s', $city->getName(), $city->getDepartmentCode(), $city->getZipCode());
    }

    /**
     * @param string[] $types
     *
     * @return array
     */
    private function buildPropertyTypesParam(array $types): array
    {
        $types = str_replace([PropertyType::APARTMENT, PropertyType::HOUSE], ['appartement', 'maison'], $types);

        return ['types' => implode(',', $types)];
    }

    /**
     * @param int|null $minPrice
     * @param int|null $maxPrice
     *
     * @return array
     */
    private function buildPriceParam(?int $minPrice, int $maxPrice): array
    {
        $minPrice = $minPrice ?: 0;

        return ['prix' => sprintf('%d_%d', $minPrice, $maxPrice)];
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

        $minArea = $minArea ?: 0;
        $maxArea = $maxArea ?: 0;

        return ['surface' => sprintf('%d_%d', $minArea, $maxArea)];
    }

    /**
     * @param int $minRoomsCount
     *
     * @return array
     */
    private function buildRoomsCountParam(int $minRoomsCount): array
    {
        return ['pieces' => sprintf('%d_0', min($minRoomsCount, self::MAX_ROOMS_COUNT))];
    }
}
