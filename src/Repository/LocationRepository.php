<?php

namespace App\Repository;

use App\DTO\City;
use Symfony\Component\Serializer\SerializerInterface;

class LocationRepository
{
    /** @var City[] */
    private array $cities;

    /**
     * @param array               $cities
     * @param SerializerInterface $serializer
     */
    public function __construct(array $cities, SerializerInterface $serializer)
    {
        $this->cities = $serializer->denormalize($cities, City::class . '[]');
    }

    /**
     * @param string $id
     *
     * @return City|null
     */
    public function find(string $id): ?City
    {
        return $this->cities[$id] ?? null;
    }
}
