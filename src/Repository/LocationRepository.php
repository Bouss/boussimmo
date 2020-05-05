<?php

namespace App\Repository;

use App\DTO\City;
use Symfony\Component\Serializer\SerializerInterface;

class LocationRepository
{
    /**
     * @var City[]
     */
    private $cities;

    /**
     * @param SerializerInterface $serializer
     * @param array               $cities
     */
    public function __construct(SerializerInterface $serializer, array $cities)
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
