<?php

namespace App\Repository;

use App\DTO\Provider;
use Symfony\Component\Serializer\SerializerInterface;

class ProviderRepository
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
}
