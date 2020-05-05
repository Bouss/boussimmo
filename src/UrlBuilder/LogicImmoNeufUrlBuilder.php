<?php

namespace App\UrlBuilder;

use App\DTO\City;
use App\Enum\PropertyType;

class LogicImmoNeufUrlBuilder extends AbstractUrlBuilder
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
        return 'https://neuf.logic-immo.com/habiter/programmes-neufs';
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
        $params = [
            ...$this->buildLocationParam($city),
            $this->buildPropertyTypeParam($propertyTypes),
            $this->buildPriceParam($maxPrice)
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
        return [
            ['locName' => strtoupper($city->getName())],
            ['locId' => $city->getLogicImmoCode()],
            ['locLevel' => 99]
        ];
    }

    /**
     * @param string[] $types
     *
     * @return array
     */
    private function buildPropertyTypeParam(array $types): array
    {
        if (2 === count($types)) {
            return ['tdb' => 'maison-appartement'];
        }

        return ['tdb' => str_replace([PropertyType::APARTMENT, PropertyType::HOUSE], ['appartement', 'maison'], $types[0])];
    }

    /**
     * @param int $maxPrice
     *
     * @return array
     */
    private function buildPriceParam(int $maxPrice): array
    {
        return ['budget' => $maxPrice];
    }

    /**
     * @param int $minRoomsCount
     *
     * @return array
     */
    private function buildRoomsCountParam(int $minRoomsCount): array
    {
        $minRoomsCount = min($minRoomsCount, self::MAX_ROOMS_COUNT);
        $roomsRange = range($minRoomsCount, self::MAX_ROOMS_COUNT);

        return ['nbRooms' => implode('-', $roomsRange)];
    }
}
