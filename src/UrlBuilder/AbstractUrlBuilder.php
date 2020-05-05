<?php

namespace App\UrlBuilder;

use App\DTO\City;
use App\Repository\LocationRepository;

abstract class AbstractUrlBuilder implements UrlBuilderInterface
{
    protected LocationRepository $locationRepository;

    /**
     * @param LocationRepository $locationRepository
     */
    public function __construct(LocationRepository $locationRepository)
    {
        $this->locationRepository = $locationRepository;
    }

    /**
     * @param string   $cityId
     * @param string[] $propertyTypes
     * @param int|null $minPrice
     * @param int      $maxPrice
     * @param int      $minArea
     * @param int|null $maxArea
     * @param int      $minRoomsCount
     *
     * @return string
     */
    public function build(
        string $cityId,
        array $propertyTypes,
        ?int $minPrice,
        int $maxPrice,
        int $minArea,
        ?int $maxArea,
        int $minRoomsCount
    ): string
    {
        $city = $this->locationRepository->find($cityId);

        $criteria = [$city, $propertyTypes, $minPrice, $maxPrice, $minArea, $maxArea, $minRoomsCount];

        $url = $this->buildPath(...$criteria);

        if (!empty($params = $this->buildQueryParameters(...$criteria))) {
            $url .= '?' . urldecode(http_build_query(array_merge(...$params)));
        }

        return $url;
    }

    /**
     * @param City     $city
     * @param array    $types
     * @param int|null $minPrice
     * @param int      $maxPrice
     * @param int      $minArea
     * @param int|null $maxArea
     * @param int      $minRoomsCount
     *
     * @return string
     */
    abstract protected function buildPath(
        City $city,
        array $types,
        ?int $minPrice,
        int $maxPrice,
        int $minArea,
        ?int $maxArea,
        int $minRoomsCount
    ): string;

    /**
     * @param City     $city
     * @param string[] $types
     * @param int|null $minPrice
     * @param int      $maxPrice
     * @param int      $minArea
     * @param int|null $maxArea
     * @param int      $minRoomsCount
     *
     * @return array
     */
    abstract protected function buildQueryParameters(
        City $city,
        array $types,
        ?int $minPrice,
        int $maxPrice,
        int $minArea,
        ?int $maxArea,
        int $minRoomsCount
    ): array;
}
