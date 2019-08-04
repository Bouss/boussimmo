<?php

namespace App\Tests\UrlBuilder;

use App\Entity\PropertyType;
use App\Service\LocationService;
use App\UrlBuilder\LogicImmoUrlBuilder;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;

class LogicImmoUrlBuilderTest extends TestCase
{
    /**
     * @var OuestFranceImmoUrlBuilder
     */
    private $urlBuilder;

    /**
     * @var LocationService|ObjectProphecy
     */
    private $locationService;

    public function setUp(): void
    {
        $this->locationService = $this->prophesize(LocationService::class);

        $this->urlBuilder = new LogicImmoUrlBuilder($this->locationService->reveal());

        // All tests are made with "nantes" city
        $this->locationService->getLocation('logic-immo', 'nantes')->willReturn('nantes,240');
    }

    public function testBuildUrlForApartmentWithAllCriteriaFilled(): void
    {
        $url = 'https://www.logic-immo.com/vente-immobilier-nantes-tous-codes-postaux,240_99/options/groupprptypesids=1/pricemin=150000/pricemax=200000/areamin=50/areamax=80/nbrooms=2,3/order=update_date_desc';

        $criteria = [
            'nantes', // $city
            PropertyType::APARTMENT, // $propertyType
            150000, // $minPrice
            200000, // $maxPrice
            50, // $minArea
            80, // $maxArea
            2, // $minRoomsCount
            3, // $maxRoomsCount
        ];

        $this->assertEquals($url, $this->urlBuilder->buildUrl(...$criteria));
    }

    public function testBuildUrlForHouseWithMinimumCriteriaFilled(): void
    {
        $url = 'https://www.logic-immo.com/vente-immobilier-nantes-tous-codes-postaux,240_99/options/groupprptypesids=2/pricemax=100000/areamin=20/nbrooms=1/order=update_date_desc';

        $criteria = [
            'nantes', // $city
            PropertyType::HOUSE, // $propertyType
            null, // $minPrice
            100000, // $maxPrice
            20, // $minArea
            null, // $maxArea
            1, // $minRoomsCount
            null // $maxRoomsCount
        ];

        $this->assertEquals($url, $this->urlBuilder->buildUrl(...$criteria));
    }
}
