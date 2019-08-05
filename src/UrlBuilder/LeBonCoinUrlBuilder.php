<?php

namespace App\UrlBuilder;

use App\Definition\SiteEnum;
use App\Entity\PropertyType;

class LeBonCoinUrlBuilder extends AbstractUrlBuilder
{
    protected const SITE = SiteEnum::LEBONCOIN;

    private const URL_START = 'https://www.leboncoin.fr/recherche/';

    /**
     * {@inheritDoc}
     */
    protected function getUrlPath(
        string $city,
        int $propertyType,
        ?int $minPrice,
        int $maxPrice,
        int $minArea,
        ?int $maxArea,
        int $minRoomsCount,
        ?int $maxRoomsCount
    ): string
    {
        return self::URL_START;
    }

    /**
     * {@inheritDoc}
     */
    protected function getUrlParameters(
        string $city,
        int $propertyType,
        ?int $minPrice,
        int $maxPrice,
        int $minArea,
        ?int $maxArea,
        int $minRoomsCount,
        ?int $maxRoomsCount
    ): array
    {
        return [
            $this->buildPropertyTypeParam($propertyType),
            $this->buildLocationParam($this->getLocation($city)),
            ['immo_sell_type' => 'old,new'],
            ['real_estate_type' => 2],
            $this->buildPriceParam($minPrice, $maxPrice),
            $this->buildRoomsCountParam($minRoomsCount, $maxRoomsCount),
            $this->buildAreaParam($minArea, $maxArea),
        ];
    }

    /**
     * @param int $type
     *
     * @return array
     */
    private function buildPropertyTypeParam(int $type): array
    {
        switch ($type) {
            case PropertyType::HOUSE:
                $type = 2;
                break;
            case PropertyType::APARTMENT:
            default:
                $type = 9;
        }

        return ['category' => $type];
    }

    /**
     * @param string $city
     *
     * @return array
     */
    private function buildLocationParam(string $city): array
    {
        return ['locations' => $city];
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

        return ['price' => sprintf('%s-%d', $minPrice, $maxPrice)];
    }

    /**
     * @param int      $minRoomsCount
     * @param int|null $maxRoomsCount
     *
     * @return array
     */
    private function buildRoomsCountParam(int $minRoomsCount, int $maxRoomsCount = null): array
    {
        $maxRoomsCount = $maxRoomsCount ?: 'max';

        return ['rooms' => sprintf('%d-%s', $minRoomsCount, $maxRoomsCount)];
    }

    /**
     * @param int      $minArea
     * @param int|null $maxArea
     *
     * @return array
     */
    private function buildAreaParam(int $minArea, int $maxArea = null): array
    {
        $maxArea = $maxArea ?: 'max';

        return ['square' => sprintf('%d-%s', $minArea, $maxArea)];
    }
}
