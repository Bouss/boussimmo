<?php

namespace App\Twig;

use App\DTO\PropertyAd;
use App\Repository\ProviderRepository;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    private const ORDER_ASC = 1;

    private ProviderRepository $providerRepository;

    /**
     * @param ProviderRepository $ProviderRepository
     */
    public function __construct(ProviderRepository $ProviderRepository)
    {
        $this->providerRepository = $ProviderRepository;
    }

    /**
     * {@inheritDoc}
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('provider_logo', [$this, 'getProviderLogo']),
            new TwigFilter('sort_by', [$this, 'sortBy'])
        ];
    }

    /**
     * @param string $providerId
     *
     * @return string|null
     */
    public function getProviderLogo(string $providerId): ?string
    {
        $provider = $this->providerRepository->find($providerId);

        return null !== $provider ? $provider->getLogo() : null;
    }

    /**
     * @param PropertyAd[] $propertyAds
     * @param string       $field
     * @param int          $order
     *
     * @return PropertyAd[]
     */
    public function sortBy(array $propertyAds, string $field, int $order = self::ORDER_ASC): array
    {
        $getter = 'get' . ucfirst($field);

        if (!method_exists(PropertyAd::class, $getter)) {
            return $propertyAds;
        }

        usort($propertyAds, static function (PropertyAd $a1, PropertyAd $a2) use ($getter, $order) {
            $comparison = $a1->{$getter}() <=> $a2->{$getter}();

            return self::ORDER_ASC === $order ? $comparison : -$comparison;
        });

        return $propertyAds;
    }
}
