<?php

namespace App\UrlBuilder;

use App\Enum\Site;
use App\Entity\PropertyType;

class SeLogerUrlBuilder extends AbstractUrlBuilder
{
    protected const SITE = Site::SELOGER;

        private const URL_START = 'https://www.seloger.com/list.htm';

    /**
     * @param string $city
     * @param int $propertyType
     * @param int|null $minPrice
     * @param int $maxPrice
     * @param int $minArea
     * @param int|null $maxArea
     * @param int $minRoomsCount
     * @param int|null $maxRoomsCount
     *
     * @return string
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
            ['projects' => '2,5'],
            ['enterprise' => 0],
            ['sort' => 'd_dt_crea'],
            ['natures' => 1,2,4],
            $this->buildPriceParam($minPrice, $maxPrice),
            $this->buildAreaParam($minArea, $maxArea),
            $this->buildRoomsCountParam($minRoomsCount),
            $this->buildLocationParam($this->getLocation($city)),
            ['qsVersion' => '1.0']
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
                $type = 1;
        }

        return ['types' => $type];
    }

    /**
     * @param int|null $minPrice
     * @param int      $maxPrice
     *
     * @return array
     */
    private function buildPriceParam(?int $minPrice, int $maxPrice): array
    {
        $minPrice = $minPrice ?: 'NaN';

        return ['price' => sprintf('%s/%d', $minPrice, $maxPrice)];
    }

    /**
     * @param int      $minArea
     * @param int|null $maxArea
     *
     * @return array
     */
    private function buildAreaParam(int $minArea, int $maxArea = null): array
    {
        $maxArea = $maxArea ?: 'NaN';

        return ['surface' => sprintf('%d/%s', $minArea, $maxArea)];
    }

    /**
     * @param int $minRoomsCount
     *
     * @return array
     */
    private function buildRoomsCountParam(int $minRoomsCount): array
    {
        return ['rooms' => $minRoomsCount];
    }

    /**
     * @param string $inseeCode
     *
     * @return array
     */
    private function buildLocationParam(string $inseeCode): array
    {
        return ['places' => sprintf('[{ci:%s}]', $inseeCode)];
    }
}
