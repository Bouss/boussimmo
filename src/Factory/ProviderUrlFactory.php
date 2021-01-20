<?php

namespace App\Factory;

use App\DataProvider\ProviderProvider;
use App\DTO\Provider;
use App\DTO\Url;
use App\Exception\UrlBuilderNotFoundException;
use App\UrlBuilder\UrlBuilderContainer;

class ProviderUrlFactory
{
    public function __construct(
        private ProviderProvider $providerProvider,
        private UrlBuilderContainer $urlBuilderContainer
    ) {}

    /**
     * @throws UrlBuilderNotFoundException
     */
    public function create(
        string $providerName,
        string $city,
        array $propertyTypes,
        ?int $minPrice,
        int $maxPrice,
        ?int $minArea,
        ?int $maxArea,
        int $minRoomsCount
    ): Url {
        /** @var Provider $provider */
        $provider = $this->providerProvider->find($providerName);

        $urlBuilder = $this->urlBuilderContainer->get($provider->getName());
        $url = $urlBuilder->build($city, $propertyTypes, $minPrice, $maxPrice, $minArea, $maxArea, $minRoomsCount);

        return new Url($provider->getName(), $provider->getLogo(), $url);
    }
}
