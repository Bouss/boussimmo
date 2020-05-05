<?php

namespace App\UrlBuilder;

use App\DTO\City;
use App\Enum\PropertyType;

class SuperimmoUrlBuilder extends AbstractUrlBuilder
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
        return sprintf('https://www.superimmo.com/achat/%s%s/%s',
            $this->buildPropertyTypesParam($propertyTypes),
            $this->buildLocationParam($city),
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
        int $minArea,
        ?int $maxArea,
        int $minRoomsCount
    ): array
    {
        return [
            $this->buildAreaParam('max', $maxArea),
            $this->buildAreaParam('min', $minArea),
            $this->buildPriceParam('max', $maxPrice),
            $this->buildPriceParam('min', $minPrice)
        ];
    }

    /**
     * @param City $city
     *
     * @return string
     */
    private function buildLocationParam(City $city): string
    {
        return sprintf('%s/%s/%s-%d', $city->getRegion(), $city->getDepartment(), $city->getName(), $city->getDepartmentCode());
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

        return str_replace([PropertyType::APARTMENT, PropertyType::HOUSE], ['appartement/', 'maison/'], $types[0]);
    }

    /**
     * @param string   $bound
     * @param int|null $price
     *
     * @return array
     */
    private function buildPriceParam(string $bound, ?int $price): array
    {
        $priceStr = null !== $price ? sprintf('%d.0', $price) : null;

        return ["price_$bound" => $priceStr];
    }

    /**
     * @param string   $bound
     * @param int|null $area
     *
     * @return array
     */
    private function buildAreaParam(string $bound, ?int $area): array
    {
        $areaStr = null !== $area ? sprintf('%d.0', $area) : null;

        return ["area_$bound" => $areaStr];
    }

    /**
     * @param int $minRoomsCount
     *
     * @return string
     */
    private function buildRoomsCountParam(int $minRoomsCount): string
    {
        return sprintf('pieces-%s', min($minRoomsCount, self::MAX_ROOMS_COUNT));
    }
}
