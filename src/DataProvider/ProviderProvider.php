<?php

namespace App\DataProvider;

use App\DTO\Provider;
use Symfony\Component\Serializer\SerializerInterface;
use function array_filter;

class ProviderProvider
{
    /** @var Provider[] */
    private array $providers;

    public function __construct(array $providers, SerializerInterface $serializer)
    {
        $this->providers = $serializer->denormalize($providers, Provider::class . '[]');
    }

    public function find(string $name): ?Provider
    {
        return $this->providers[$name] ?? null;
    }

    /**
     * @return Provider[]
     */
    public function getProvidersByMainProvider(string $name): array
    {
        return array_filter($this->providers, static fn(Provider $provider) =>
            in_array($name, [$provider->getName(), $provider->getParent()], true)
        );
    }
}
