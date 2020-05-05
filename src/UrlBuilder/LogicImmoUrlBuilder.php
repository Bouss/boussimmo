<?php

namespace App\UrlBuilder;

use App\DTO\City;
use App\Enum\PropertyType;

class LogicImmoUrlBuilder extends AbstractUrlBuilder
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
        int $minArea,
        ?int $maxArea,
        int $minRoomsCount
    ): string
    {
        return sprintf('https://www.logic-immo.com/%s/options/%s/%s%s/%s/%s%s',
            $this->buildLocationParam($city),
            $this->buildPropertyTypeParam($propertyTypes),
            ((null !== $minPrice) ? $this->buildPriceParam('min', $minPrice) . '/' : ''),
            $this->buildPriceParam('max', $maxPrice),
            $this->buildAreaParam('min', $minArea),
            ((null !== $maxArea) ? $this->buildAreaParam('max', $maxArea) . '/' : ''),
            ($minRoomsCount > 1 ? $this->buildRoomsCountParam($minRoomsCount) : '')
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
        return sprintf('vente-immobilier-%s-tous-codes-postaux,%d_99', $city->getName(), $city->getLogicImmoCode());
    }

    /**
     * @param string[] $types
     *
     * @return string
     */
    private function buildPropertyTypeParam(array $types): string
    {
        $types = str_replace([PropertyType::APARTMENT, PropertyType::HOUSE], [1, 2], $types);

        return 'groupprptypesids=' . implode(',', $types);
    }

    /**
     * @param string $bound
     * @param int    $price
     *
     * @return string
     */
    private function buildPriceParam(string $bound, int $price): string
    {
        return "price$bound=$price";
    }

    /**
     * @param string $bound
     * @param int    $area
     *
     * @return string
     */
    private function buildAreaParam(string $bound, int $area): string
    {
        return "area$bound=$area";
    }

    /**
     * @param int $minRoomsCount
     *
     * @return string
     */
    private function buildRoomsCountParam(int $minRoomsCount): string
    {
        $values = range($minRoomsCount, self::MAX_ROOMS_COUNT);

        return 'nbrooms=' . implode(',', $values);
    }
}
