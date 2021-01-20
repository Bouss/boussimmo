<?php

namespace App\UrlBuilder;

use App\DTO\City;
use App\Enum\PropertyType;

class SeLogerNeufUrlBuilder extends AbstractUrlBuilder
{
    private const MAX_ROOMS_COUNT = 4;

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
        return 'https://www.selogerneuf.com/recherche';
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
            $this->buildPriceParam($maxPrice),
            $this->buildRoomsCountParam($minRoomsCount),
            $this->buildAreaParam('min', $minArea),
            $this->buildAreaParam('max', $maxArea),
            ['idtt' => '9'],
            ['tri' => 'selection'],
            $this->buildLocationParam($city),
        ];
    }

    /**
     * @param City $city
     *
     * @return array
     */
    private function buildLocationParam(City $city): array
    {
        return ['localities' => $city->getSeLogerNeufCode()];
    }

    /**
     * @param string[] $types
     *
     * @return array
     */
    private function buildPropertyTypesParam(array $types): array
    {
        $types = str_replace([PropertyType::APARTMENT, PropertyType::HOUSE], [1, 2], $types);

        return ['idtypebien' => implode(',', $types)];
    }

    /**
     * @param int $maxPrice
     *
     * @return array
     */
    private function buildPriceParam(int $maxPrice): array
    {
        return ['pxmax' => $maxPrice];
    }

    /**
     * @param string   $bound
     * @param int|null $area
     *
     * @return array
     */
    private function buildAreaParam(string $bound, ?int $area): array
    {
        return ["surface$bound" => $area];
    }

    /**
     * @param int $minRoomsCount
     *
     * @return array
     */
    private function buildRoomsCountParam(int $minRoomsCount): array
    {
        if ($minRoomsCount > self::MAX_ROOMS_COUNT) {
            return ['nb_pieces' => '+4'];
        }

        $roomsCount = min($minRoomsCount, self::MAX_ROOMS_COUNT);
        $roomsList = implode(',', range(1, $roomsCount));

        if ($minRoomsCount > self::MAX_ROOMS_COUNT) {
            $roomsList .= ',+4';
        }

        return ['nb_pieces' => $roomsList];
    }
}
