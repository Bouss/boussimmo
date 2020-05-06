<?php

namespace App\Repository;

use App\DTO\Provider;
use Symfony\Component\Serializer\SerializerInterface;
use function array_filter;

class ProviderRepository
{
    /** @var Provider[] */
    private array $providers;

    /**
     * @param array               $providers
     * @param SerializerInterface $serializer
     */
    public function __construct(array $providers, SerializerInterface $serializer)
    {
        $this->providers = $serializer->denormalize($providers, Provider::class . '[]');
    }

    /**
     * @param string $id
     *
     * @return Provider|null
     */
    public function find(string $id): ?Provider
    {
        return $this->providers[$id] ?? null;
    }

    /**
     * @param string $id
     *
     * @return Provider[]
     */
    public function getAllProviders(string $id): array
    {
        return array_filter($this->providers, fn(Provider $provider) =>
            $id === $provider->getId() || $id === $provider->getParentProvider()
        );
    }
}
