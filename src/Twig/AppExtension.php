<?php

namespace App\Twig;

use App\Entity\PropertyAd;
use App\Service\ProviderService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    /**
     * @var ProviderService
     */
    private $providerService;

    /**
     * @param ProviderService $providerService
     */
    public function __construct(ProviderService $providerService)
    {
        $this->providerService = $providerService;
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
     * @param string $provider
     *
     * @return string
     */
    public function getProviderLogo(string $provider): string
    {
        return $this->providerService->getLogo($provider);
    }

    /**
     * @param PropertyAd[] $propertyAds
     * @param string       $field
     * @param int          $order
     *
     * @return PropertyAd[]
     */
    public function sortBy(array $propertyAds, string $field, int $order = 1): array
    {
        $getter = 'get' . ucfirst($field);

        if (!method_exists(PropertyAd::class, $getter)) {
            return $propertyAds;
        }

        usort($propertyAds, static function (PropertyAd $a1, PropertyAd $a2) use ($getter, $order) {
            $comparison = $a1->{$getter}() <=> $a2->{$getter}();

            return 1 === $order ? $comparison : -$comparison;
        });

        return $propertyAds;
    }
}
