<?php

namespace App\UrlBuilder;

use App\DTO\City;
use App\Enum\PropertyType;

class PapUrlBuilder extends AbstractUrlBuilder
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
        return sprintf('https://www.pap.fr/annonce/vente-%s-%s-%s-%s-%s',
            $this->buildPropertyTypeParam($propertyTypes),
            $this->buildLocationParam($city),
            $this->buildRoomsCountParam($minRoomsCount),
            $this->buildPriceParam($minPrice, $maxPrice),
            $this->buildAreaParam($minArea, $maxArea),
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
            $types = str_replace([PropertyType::APARTMENT, PropertyType::HOUSE], ['appartement', 'maison'], $types);

            return implode('-', $types);
        }

        return str_replace([PropertyType::APARTMENT, PropertyType::HOUSE], ['appartements', 'maisons'], $types[0]);
    }

    /**
     * @param int|null $minPrice
     * @param int      $maxPrice
     *
     * @return string
     */
    private function buildPriceParam(?int $minPrice, int $maxPrice): string
    {
        if (null === $minPrice) {
            return "jusqu-a-$maxPrice-euros";
        }

        return "entre-$minPrice-et-$maxPrice-euros";
    }

    /**
     * @param int|null $minArea
     * @param int|null $maxArea
     *
     * @return string
     */
    private function buildAreaParam(?int $minArea, ?int $maxArea): string
    {
        if (null !== $minArea && null !== $maxArea) {
            return "entre-$minArea-et-$maxArea-m2";
        }

        if (null !== $minArea) {
            return "a-partir-de-$minArea-m2";
        }

        if (null !== $maxArea) {
            return "jusqu-a-$maxArea-m2";
        }

        return '';
    }

    /**
     * @param int $minRoomsCount
     *
     * @return string
     */
    private function buildRoomsCountParam(int $minRoomsCount): string
    {
        if (1 === $minRoomsCount) {
            return 'studio';
        }

        $minRoomsCount = min($minRoomsCount, self::MAX_ROOMS_COUNT);
        $minRoomsCountStr = "$minRoomsCount-pieces";

        if (self::MAX_ROOMS_COUNT === $minRoomsCount) {
            $minRoomsCountStr = "a-partir-du-$minRoomsCountStr";
        }

        return $minRoomsCountStr;
    }
}
