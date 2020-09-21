<?php

namespace App\DTO;

use DateTime;
use function Symfony\Component\String\u;

class PropertyAd
{
    public const NEW_BUILD_WORDS = ['neuf', 'livraison', 'programme', 'neuve', 'nouveau', 'nouvelle', 'remise'];

    private string $provider;
    private DateTime $publishedAt;
    private string $url;
    private ?float $price = null;
    private ?float $area = null;
    private ?int $roomsCount = null;
    private ?string $location = null;
    private ?string $name = null;
    private ?string $title = null;
    private ?string $description = null;
    private ?string $photo = null;
    private bool $newBuild = false;
    /** @var PropertyAd[] */
    private array $duplicates = [];

    /**
     * @return string
     */
    public function getProvider(): string
    {
        return $this->provider;
    }

    /**
     * @param string $provider
     *
     * @return PropertyAd
     */
    public function setProvider(string $provider): PropertyAd
    {
        $this->provider = $provider;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getPublishedAt(): DateTime
    {
        return $this->publishedAt;
    }

    /**
     * @param DateTime $publishedAt
     *
     * @return PropertyAd
     */
    public function setPublishedAt(DateTime $publishedAt): PropertyAd
    {
        $this->publishedAt = $publishedAt;

        return $this;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @param string $url
     *
     * @return PropertyAd
     */
    public function setUrl(string $url): PropertyAd
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getPrice(): ?float
    {
        return $this->price;
    }

    /**
     * @param float|null $price
     *
     * @return PropertyAd
     */
    public function setPrice(?float $price): PropertyAd
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getArea(): ?float
    {
        return $this->area;
    }

    /**
     * @param float|null $area
     *
     * @return PropertyAd
     */
    public function setArea(?float $area): PropertyAd
    {
        $this->area = $area;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getRoomsCount(): ?int
    {
        return $this->roomsCount;
    }

    /**
     * @param int|null $roomsCount
     *
     * @return PropertyAd
     */
    public function setRoomsCount(?int $roomsCount): PropertyAd
    {
        $this->roomsCount = $roomsCount;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getLocation(): ?string
    {
        return $this->location;
    }

    /**
     * @param string|null $location
     *
     * @return PropertyAd
     */
    public function setLocation(?string $location): PropertyAd
    {
        $this->location = $location;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     *
     * @return PropertyAd
     */
    public function setName(?string $name): PropertyAd
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string|null $title
     *
     * @return PropertyAd
     */
    public function setTitle(?string $title): PropertyAd
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     *
     * @return PropertyAd
     */
    public function setDescription(?string $description): PropertyAd
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    /**
     * @param string|null $photo
     *
     * @return PropertyAd
     */
    public function setPhoto(?string $photo): PropertyAd
    {
        $this->photo = $photo;

        return $this;
    }

    /**
     * @return bool
     */
    public function isNewBuild(): bool
    {
        return $this->newBuild;
    }

    /**
     * @param bool $newBuild
     *
     * @return PropertyAd
     */
    public function setNewBuild(bool $newBuild): PropertyAd
    {
        $this->newBuild = $newBuild;

        return $this;
    }

    /**
     * @return PropertyAd[]
     */
    public function getDuplicates(): array
    {
        return $this->duplicates;
    }

    /**
     * @param PropertyAd[] $duplicates
     *
     * @return PropertyAd
     */
    public function setDuplicates(array $duplicates): PropertyAd
    {
        $this->duplicates = $duplicates;

        return $this;
    }

    /**
     * @param PropertyAd $propertyAd
     */
    public function addDuplicate(PropertyAd $propertyAd): void
    {
        $this->duplicates[] = $propertyAd;
    }

    /**
     * @param PropertyAd $propertyAd
     * @param bool       $strict
     *
     * @return bool
     */
    public function equals(PropertyAd $propertyAd, bool $strict = false): bool
    {
        $strictRespected = $strict ? $this->provider === $propertyAd->getProvider() : true;

        // If at least one price is missing
        if (null === $this->price || null === $propertyAd->getPrice()) {
            // If the properties are new-build, they are the same ones if their name are equal
            if ($this->newBuild && $propertyAd->isNewBuild()) {
                return (null !== $this->name && $this->name === $propertyAd->getName()) && $strictRespected;
            }

            // At least one property is not a new-build, we can't determinate if they're the same ones
            return false;
        }

        $sameArea = null !== $this->area && abs($this->area - $propertyAd->getArea()) <= 1;

        // Properties are equals if their price are equal as long as the price doesn't finish with a so common "000"
        // or when their area are pretty much equal
        return
            (null !== $this->price && $this->price === $propertyAd->getPrice()) &&
            ((null !== $this->area && null !== $propertyAd->getArea()) ? $sameArea : true) &&
            ('000' !== substr($this->price, -3) || $sameArea) &&
            $strictRespected;
    }

    public function guessNewBuild(): void
    {
        $this->newBuild = u($this->title . $this->description)->containsAny(self::NEW_BUILD_WORDS);
    }
}
