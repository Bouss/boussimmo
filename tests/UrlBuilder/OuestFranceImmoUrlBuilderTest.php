<?php

namespace App\Tests\UrlBuilder;

use App\Entity\PropertyType;
use App\Service\LocationService;
use App\UrlBuilder\OuestFranceImmoUrlBuilder;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;

class OuestFranceImmoUrlBuilderTest extends TestCase
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

        $this->urlBuilder = new OuestFranceImmoUrlBuilder($this->locationService->reveal());

        // All tests are made with "nantes" city
        $this->locationService->getLocation('ouestfrance-immo', 'nantes')->willReturn('nantes-44-44000');
    }

    public function testBuildUrlForApartment(): void
    {
        $url = 'https://www.ouestfrance-immo.com/acheter/nantes-44-44000/?types=appartement&classifs=2-pieces,3-pieces&prix=150000_200000&surface=50_80&tri=DATE_DECROISSANT';
        $url = str_replace(',', '%2C', $url);

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

    public function testBuildUrlForOneRoomApartment(): void
    {
        $url = 'https://www.ouestfrance-immo.com/acheter/nantes-44-44000/?types=appartement&classifs=studio,t1&prix=0_150000&surface=20_0&tri=DATE_DECROISSANT';
        $url = str_replace(',', '%2C', $url);

        $criteria = [
            'nantes', // $city
            PropertyType::APARTMENT, // $propertyType
            null, // $minPrice
            150000, // $maxPrice
            20, // $minArea
            null, // $maxArea
            1, // $minRoomsCount
            null // $maxRoomsCount
        ];

        $this->assertEquals($url, $this->urlBuilder->buildUrl(...$criteria));
    }

    public function testBuildUrlForHouse(): void
    {
        $url = 'https://www.ouestfrance-immo.com/acheter/nantes-44-44000/?types=maison&chambres=3_0&prix=0_300000&surface=150_0&tri=DATE_DECROISSANT';
        $url = str_replace(',', '%2C', $url);

        $criteria = [
            'nantes', // $city
            PropertyType::HOUSE, // $propertyType
            null, // $minPrice
            300000, // $maxPrice
            150, // $minArea
            null, // $maxArea
            3, // $minRoomsCount
            5 // $maxRoomsCount
        ];

        $this->assertEquals($url, $this->urlBuilder->buildUrl(...$criteria));
    }
}
