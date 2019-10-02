<?php

namespace App\Twig;

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
            new TwigFilter('providerLogo', [$this, 'getProviderLogo']),
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
}
