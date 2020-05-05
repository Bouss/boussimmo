<?php

namespace App\DTO;

use App\Util\StringUtil;
use DateTime;

class PropertyAd
{
    public const NEW_BUILD_WORDS = ['neuf', 'livraison', 'programme', 'neuve', 'nouveau', 'nouvelle', 'remise'];

    private int $id;
    private string $provider;
    private DateTime $publishedAt;
    private string $url;
    private ?float $price;
    private ?float $area;
    private ?int $roomsCount;
    private ?string $location;
    private ?string $title;
    private ?string $description;
    private ?string $photo;
    private bool $newBuild = false;
    /** @var PropertyAd[] */
    private array $duplicates = [];

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

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
        if (null === $this->price) {
            return false;
        }

        return
            ($strict ? $this->provider === $propertyAd->getProvider() : true) &&
            (
                ($this->price === $propertyAd->getPrice() && '000' !== substr($this->price, -3)) ||
                ($this->price === $propertyAd->getPrice() && abs($this->area - $propertyAd->getArea()) <= 1)
            );
    }

    public function guessNewBuild(): void
    {
        $this->newBuild = StringUtil::contains($this->title . $this->description, self::NEW_BUILD_WORDS);
    }
}
