<?php

namespace App\UrlBuilder;

use App\Definition\SiteEnum;
use App\Entity\PropertyType;

class OuestFranceImmoUrlBuilder extends AbstractUrlBuilder
{
    protected const SITE = SiteEnum::OUESTFRANCE_IMMO;

    private const URL_START = 'https://www.ouestfrance-immo.com/acheter/';

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
        return self::URL_START . $this->getLocation($city) . '/';
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
            $this->buildRoomsCountParam($propertyType, $minRoomsCount, $maxRoomsCount),
            $this->buildPriceParam($minPrice, $maxPrice),
            $this->buildAreaParam($minArea, $maxArea),
            ['tri' => 'DATE_DECROISSANT']
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
                $value = 'maison';
                break;
            case PropertyType::APARTMENT:
            default:
                $value = 'appartement';
        }

        return ['types' => $value];
    }

    /**
     * @param int|null $minPrice
     * @param int|null $maxPrice
     *
     * @return array
     */
    private function buildPriceParam(?int $minPrice, int $maxPrice = null): array
    {
        $value = $minPrice ?: '0';
        $value .= '_' . $maxPrice;

        return ['prix' => $value];
    }

    /**
     * @param int      $minArea
     * @param int|null $maxArea
     *
     * @return array
     */
    private function buildAreaParam(int $minArea, int $maxArea = null): array
    {
        $value = $minArea . '_';
        $value .= $maxArea ?: '0';

        return ['surface' => $value];
    }

    /**
     * @param int      $type
     * @param int      $minRoomsCount
     * @param int|null $maxRoomsCount
     *
     * @return array
     */
    private function buildRoomsCountParam(int $type, int $minRoomsCount, int $maxRoomsCount = null): array
    {
        switch ($type) {
            case PropertyType::HOUSE:
                return $this->buildRoomsCountParamForHouse($minRoomsCount);
            case PropertyType::APARTMENT:
            default:
                return $this->buildRoomsCountParamForApartment($minRoomsCount, $maxRoomsCount);
        }
    }

    /**
     * @param int      $minRoomsCount
     * @param int|null $maxRoomsCount
     *
     * @return array
     */
    private function buildRoomsCountParamForApartment(int $minRoomsCount, ?int $maxRoomsCount): array
    {
        $maxRoomsCount = $maxRoomsCount ?: $minRoomsCount;
        $values = [];

        for ($i = $minRoomsCount; $i <= $maxRoomsCount; ++$i) {
            if (1 === $i) {
                $values[] = 'studio,t1';
            } elseif ($i < 6) {
                $values[] = $i . '-pieces';
            } else {
                $values[] = '6-pieces-et-plus';
            }
        }

        return ['classifs' => implode(',', $values)];
    }

    /**
     * @param int $minRoomsCount
     *
     * @return array
     */
    private function buildRoomsCountParamForHouse(int $minRoomsCount): array
    {
        return ['chambres' => min($minRoomsCount, 6) . '_0'];
    }
}
