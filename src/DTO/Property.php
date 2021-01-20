<?php

namespace App\DTO;

use DateTime;

class Property
{
    private ?float $price = null;
    private ?float $area = null;
    private ?int $roomsCount = null;
    private ?string $location = null;
    private ?string $buildingName = null;
    private bool $newBuild = false;
    /** @var PropertyAd[]  */
    private array $ads = [];

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(?float $price): Property
    {
        $this->price = $price;

        return $this;
    }

    public function getArea(): ?float
    {
        return $this->area;
    }

    public function setArea(?float $area): Property
    {
        $this->area = $area;

        return $this;
    }

    public function getRoomsCount(): ?int
    {
        return $this->roomsCount;
    }

    public function setRoomsCount(?int $roomsCount): Property
    {
        $this->roomsCount = $roomsCount;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(?string $location): Property
    {
        $this->location = $location;

        return $this;
    }

    public function getBuildingName(): ?string
    {
        return $this->buildingName;
    }

    public function setBuildingName(?string $buildingName): Property
    {
        $this->buildingName = $buildingName;

        return $this;
    }

    public function isNewBuild(): bool
    {
        return $this->newBuild;
    }

    public function setNewBuild(bool $newBuild): Property
    {
        $this->newBuild = $newBuild;

        return $this;
    }

    /**
     * @return PropertyAd[]
     */
    public function getAds(): array
    {
        return $this->ads;
    }

    /**
     * @param PropertyAd[] $ads
     */
    public function setAds(array $ads): Property
    {
        $this->ads = $ads;

        return $this;
    }

    public function getAd(): PropertyAd
    {
        return $this->ads[0];
    }

    public function setAd(PropertyAd $ad): Property
    {
        $this->ads = [$ad];

        return $this;
    }

    public function addAd(PropertyAd $ad): Property
    {
        $this->ads[] = $ad;

        return $this;
    }

    public function getPublishedAt(): DateTime
    {
        return $this->getAd()->getPublishedAt();
    }

    public function equals(Property $property): bool
    {
        // If the properties are new-build, they are the same ones if the name of their apartment building are equal
        if ($this->newBuild && $property->isNewBuild()) {
            return (null !== $this->buildingName && $this->buildingName === $property->getBuildingName());
        }

        $sameArea = null !== $this->area && abs($this->area - $property->getArea()) <= 1;

        // Properties are equal if their price are equal, as long as the price doesn't finish with a so common "000"
        // or if their area are pretty much equal
        return
            (null !== $this->price && $this->price === $property->getPrice()) &&
            ((null !== $this->area && null !== $property->getArea()) ? $sameArea : true) &&
            ('000' !== substr($this->price, -3) || $sameArea);
    }
}
