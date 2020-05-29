<?php

namespace App\UrlBuilder;

use App\DTO\City;
use App\Enum\PropertyType;

class BienIciUrlBuilder extends AbstractUrlBuilder
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
        return sprintf('https://www.bienici.com/recherche/achat/%s/%s%s',
            $this->buildLocationParam($city),
            $this->buildPropertyTypesParam($propertyTypes),
            $this->buildRoomsCountParam($minRoomsCount)
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
            $this->buildPriceParam('min', $minPrice),
            $this->buildPriceParam('max', $maxPrice),
            $this->buildAreaParam('min', $minArea),
            $this->buildAreaParam('max', $maxArea)
        ];
    }

    /**
     * @param City $city
     *
     * @return string
     */
    private function buildLocationParam(City $city): string
    {
        return sprintf('%s-%s', $city->getName(), $city->getZipCode());
    }

    /**
     * @param string[] $types
     *
     * @return string
     */
    private function buildPropertyTypesParam(array $types): string
    {
        if (2 === count($types)) {
            return '';
        }

        return str_replace([PropertyType::APARTMENT, PropertyType::HOUSE], ['appartement/', 'maisonvilla/'], $types[0]);
    }

    /**
     * @param string   $bound
     * @param int|null $price
     *
     * @return array
     */
    private function buildPriceParam(string $bound, ?int $price): array
    {
        return ["prix-$bound" => $price];
    }

    /**
     * @param string   $bound
     * @param int|null $area
     *
     * @return array
     */
    private function buildAreaParam(string $bound, ?int $area): array
    {
        return ["surface-$bound" => $area];
    }

    /**
     * @param int $minRoomsCount
     *
     * @return string
     */
    private function buildRoomsCountParam(int $minRoomsCount): string
    {
        $minRoomsCount = min($minRoomsCount, self::MAX_ROOMS_COUNT);

        return "$minRoomsCount-pieces-et-plus";
    }
}
