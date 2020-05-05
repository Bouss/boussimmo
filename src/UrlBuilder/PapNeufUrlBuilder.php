<?php

namespace App\UrlBuilder;

use App\DTO\City;
use App\Enum\PropertyType;

class PapNeufUrlBuilder extends AbstractUrlBuilder
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
        int $minArea,
        ?int $maxArea,
        int $minRoomsCount
    ): string
    {
        return sprintf('https://www.immoneuf.com/programme/%s/%s/%s/%s',
            $this->buildPropertyTypeParam($propertyTypes),
            $this->buildLocationParam($city),
            $this->buildRoomsCountParam($minRoomsCount),
            $this->buildPriceParam($maxPrice)
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
        int $minArea,
        ?int $maxArea,
        int $minRoomsCount
    ): array
    {
        return [];
    }

    /**
     * @param City $city
     *
     * @return string
     */
    private function buildLocationParam(City $city): string
    {
        return sprintf('%s-%d-g%d', $city->getName(), $city->getDepartmentCode(), $city->getPapCode());
    }

    /**
     * @param string[] $types
     *
     * @return string
     */
    private function buildPropertyTypeParam(array $types): string
    {
        if (2 === count($types)) {
            return 'immobilier-neuf';
        }

        return str_replace([PropertyType::APARTMENT, PropertyType::HOUSE], ['appartement-neuf', 'maison-neuve'], $types[0]);
    }

    /**
     * @param int $maxPrice
     *
     * @return string
     */
    private function buildPriceParam(int $maxPrice): string
    {
        return "jusqu-a-$maxPrice-euros";
    }

    /**
     * @param int $minRoomsCount
     *
     * @return string
     */
    private function buildRoomsCountParam(int $minRoomsCount): string
    {
        return sprintf('%d-%d-pieces', min($minRoomsCount, self::MAX_ROOMS_COUNT), self::MAX_ROOMS_COUNT);
    }
}
