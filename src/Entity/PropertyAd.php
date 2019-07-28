<?php

namespace App\Entity;

use DateTime;

class PropertyAd
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string|null
     */
    private $externalId;

    /**
     * @var string|null
     */
    private $checksum;

    /**
     * @var string
     */
    private $site;

    /**
     * @var string
     */
    private $url;

    /**
     * @var float
     */
    private $price;

    /**
     * @var float
     */
    private $area;

    /**
     * @var int
     */
    private $roomsCount;

    /**
     * @var string|null
     */
    private $location;

    /**
     * @var DateTime|null
     */
    private $publishedAt;

    /**
     * @var string|null
     */
    private $title;

    /**
     * @var string|null
     */
    private $description;

    /**
     * @var string|null
     */
    private $photo;

    /**
     * @var string|null
     */
    private $realEstateAgent;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getExternalId(): ?string
    {
        return $this->externalId;
    }

    /**
     * @param string|null $externalId
     *
     * @return PropertyAd
     */
    public function setExternalId(?string $externalId): PropertyAd
    {
        $this->externalId = $externalId;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getChecksum(): ?string
    {
        return $this->checksum;
    }

    /**
     * @param string|null $checksum
     *
     * @return PropertyAd
     */
    public function setChecksum(?string $checksum): PropertyAd
    {
        $this->checksum = $checksum;

        return $this;
    }

    /**
     * @return string
     */
    public function getSite(): string
    {
        return $this->site;
    }

    /**
     * @param string $site
     *
     * @return PropertyAd
     */
    public function setSite(string $site): PropertyAd
    {
        $this->site = $site;

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
     * @return float
     */
    public function getPrice(): float
    {
        return $this->price;
    }

    /**
     * @param float $price
     *
     * @return PropertyAd
     */
    public function setPrice(float $price): PropertyAd
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @return float
     */
    public function getArea(): float
    {
        return $this->area;
    }

    /**
     * @param float $area
     *
     * @return PropertyAd
     */
    public function setArea(float $area): PropertyAd
    {
        $this->area = $area;

        return $this;
    }

    /**
     * @return int
     */
    public function getRoomsCount(): int
    {
        return $this->roomsCount;
    }

    /**
     * @param int $roomsCount
     *
     * @return PropertyAd
     */
    public function setRoomsCount(int $roomsCount): PropertyAd
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
     * @return DateTime|null
     */
    public function getPublishedAt(): ?DateTime
    {
        return $this->publishedAt;
    }

    /**
     * @param DateTime|null $publishedAt
     *
     * @return PropertyAd
     */
    public function setPublishedAt(?DateTime $publishedAt): PropertyAd
    {
        $this->publishedAt = $publishedAt;

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
     * @return string|null
     */
    public function getRealEstateAgent(): ?string
    {
        return $this->realEstateAgent;
    }

    /**
     * @param string|null $realEstateAgent
     *
     * @return PropertyAd
     */
    public function setRealEstateAgent(?string $realEstateAgent): PropertyAd
    {
        $this->realEstateAgent = $realEstateAgent;

        return $this;
    }
}
