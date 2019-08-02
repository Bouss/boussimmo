<?php

namespace App\UrlBuilder;

use App\Service\LocationService;

abstract class AbstractUrlBuilder
{
    // Redefined in the child classes
    protected const SITE = '';

    /**
     * @var LocationService
     */
    protected $locationService;

    /**
     * @param LocationService $locationService
     */
    public function __construct(LocationService $locationService)
    {
        $this->locationService = $locationService;
    }

    /**
     * @param string   $city
     * @param int      $propertyType
     * @param int|null $minPrice
     * @param int      $maxPrice
     * @param int      $minArea
     * @param int|null $maxArea
     * @param int      $minRoomsCount
     * @param int|null $maxRoomsCount
     *
     * @return string
     */
    public function buildUrl(
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
        $criteria = [$city, $propertyType, $minPrice, $maxPrice, $minArea, $maxArea, $minRoomsCount, $maxRoomsCount];

        $url = $this->getUrlPath(...$criteria);

        if (null !== $params = $this->getUrlParameters(...$criteria)) {
            $url .= '?' . http_build_query(array_merge(...$params));
        }

        return $url;
    }

    /**
     * @param string   $city
     * @param int      $propertyType
     * @param int|null $minPrice
     * @param int      $maxPrice
     * @param int      $minArea
     * @param int|null $maxArea
     * @param int      $minRoomsCount
     * @param int|null $maxRoomsCount
     *
     * @return string
     */
    abstract protected function getUrlPath(
        string $city,
        int $propertyType,
        ?int $minPrice,
        int $maxPrice,
        int $minArea,
        ?int $maxArea,
        int $minRoomsCount,
        ?int $maxRoomsCount
    ): string;

    /**
     * @param string   $city
     * @param int      $propertyType
     * @param int|null $minPrice
     * @param int      $maxPrice
     * @param int      $minArea
     * @param int|null $maxArea
     * @param int      $minRoomsCount
     * @param int|null $maxRoomsCount
     *
     * @return array|null
     */
    abstract protected function getUrlParameters(
        string $city,
        int $propertyType,
        ?int $minPrice,
        int $maxPrice,
        int $minArea,
        ?int $maxArea,
        int $minRoomsCount,
        ?int $maxRoomsCount
    ): ?array;

    /**
     * @param $city
     *
     * @return string
     */
    protected function getLocation($city): string
    {
        return $this->locationService->getLocation(static::SITE, $city);
    }
}
