<?php

namespace App\DataProvider;

use App\DTO\City;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\SerializerInterface;

class LocationProvider
{
    /** @var City[] */
    private array $cities;

    /**
     * @throws ExceptionInterface
     */
    public function __construct(array $cities, SerializerInterface $serializer)
    {
        $this->cities = $serializer->denormalize($cities, City::class . '[]');
    }

    public function find(string $name): ?City
    {
        return $this->cities[$name] ?? null;
    }
}
