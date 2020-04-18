<?php

namespace App\Finder;

use App\DTO\Provider;
use Symfony\Component\Serializer\SerializerInterface;

class ProviderFinder
{
    /**
     * @var Provider[]
     */
    private $providers;

    /**
     * @param SerializerInterface $serializer
     * @param array               $providers
     */
    public function __construct(SerializerInterface $serializer, array $providers)
    {
        $this->providers = $serializer->denormalize($providers,'App\DTO\Provider[]');
    }

    /**
     * @param string $providerId
     *
     * @return string
     */
    public function getLogo(string $providerId): string
    {
        foreach ($this->providers as $provider) {
            if ($providerId === $provider->id) {
                return $provider->logo;
            }
        }

        return '';
    }
}
