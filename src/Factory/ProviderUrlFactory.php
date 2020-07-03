<?php

namespace App\Factory;

use App\DTO\Provider;
use App\DTO\Url;
use App\Repository\ProviderRepository;
use App\UrlBuilderContainer;

class ProviderUrlFactory
{
    private ProviderRepository $providerRepository;
    private UrlBuilderContainer $urlBuilderContainer;

    public function __construct(ProviderRepository $providerRepository, UrlBuilderContainer $urlBuilderContainer)
    {
        $this->providerRepository = $providerRepository;
        $this->urlBuilderContainer = $urlBuilderContainer;
    }

    public function create(
        string $providerId,
        string $city,
        array $propertyTypes,
        ?int $minPrice,
        int $maxPrice,
        ?int $minArea,
        ?int $maxArea,
        int $minRoomsCount
    ): Url
    {
        /** @var Provider $provider */
        $provider = $this->providerRepository->find($providerId);

        $urlBuilder = $this->urlBuilderContainer->get($provider->getId());
        $url = $urlBuilder->build($city, $propertyTypes, $minPrice, $maxPrice, $minArea, $maxArea, $minRoomsCount);

        return new Url($provider->getId(), $provider->getLogo(), $url);
    }
}
