<?php

namespace App\UrlBuilder;

use App\Enum\Site;
use App\Entity\PropertyType;

class LogicImmoUrlBuilder extends AbstractUrlBuilder
{
    protected const SITE = Site::LOGIC_IMMO;

    private const URL_START = 'https://www.logic-immo.com/vente-immobilier-';

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
        [$city, $code] = $this->getLocationParts($city);

        return self::URL_START . $city . '-tous-codes-postaux,' . $code . '_99/options/'
            . $this->buildPropertyTypeParam($propertyType) . '/'
            . ((null !== $minPrice) ? $this->buildPriceParam('min', $minPrice) . '/' : '')
            . $this->buildPriceParam('max', $maxPrice) . '/'
            . $this->buildAreaParam('min', $minArea) . '/'
            . ((null !== $maxArea) ? $this->buildAreaParam('max', $maxArea) . '/' : '')
            . $this->buildRoomsCountParam($minRoomsCount, $maxRoomsCount) . '/'
            . 'order=update_date_desc';
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
    ): ?array
    {
        return null;
    }

    /**
     * @param int $type
     *
     * @return string
     */
    private function buildPropertyTypeParam(int $type): string
    {
        switch ($type) {
            case PropertyType::HOUSE:
                $value = 2;
                break;
            case PropertyType::APARTMENT:
            default:
                $value = 1;
        }

        return 'groupprptypesids=' . $value;
    }

    /**
     * @param string $type
     * @param int    $price
     *
     * @return string
     */
    private function buildPriceParam(string $type, int $price): string
    {
        return sprintf('price%s=%d', $type, $price);
    }

    /**
     * @param string $type
     * @param int    $area
     *
     * @return string
     */
    private function buildAreaParam(string $type, int $area): string
    {
        return sprintf('area%s=%d', $type, $area);
    }

    /**
     * @param int      $minRoomsCount
     * @param int|null $maxRoomsCount
     *
     * @return string
     */
    private function buildRoomsCountParam(int $minRoomsCount, ?int $maxRoomsCount): string
    {
        $maxRoomsCount = $maxRoomsCount ?: $minRoomsCount;
        $values = range($minRoomsCount, $maxRoomsCount);

        return 'nbrooms=' . implode(',', $values);
    }

    /**
     * @param string $city
     *
     * @return string[]
     */
    private function getLocationParts(string $city): array
    {
        return explode(',', $this->getLocation($city));
    }
}
