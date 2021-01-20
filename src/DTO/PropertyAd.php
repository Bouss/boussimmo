<?php

namespace App\DTO;

use DateTime;

class PropertyAd
{
    private string $provider;
    private ?string $title = null;
    private ?string $description = null;
    private string $photo;
    private string $url;
    private DateTime $publishedAt;
    private Property $property;

    public function getProvider(): string
    {
        return $this->provider;
    }

    public function setProvider(string $provider): PropertyAd
    {
        $this->provider = $provider;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): PropertyAd
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): PropertyAd
    {
        $this->description = $description;

        return $this;
    }

    public function getPhoto(): string
    {
        return $this->photo;
    }

    public function setPhoto(string $photo): PropertyAd
    {
        $this->photo = $photo;

        return $this;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setUrl(string $url): PropertyAd
    {
        $this->url = $url;

        return $this;
    }

    public function getPublishedAt(): DateTime
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(DateTime $publishedAt): PropertyAd
    {
        $this->publishedAt = $publishedAt;

        return $this;
    }

    public function getProperty(): Property
    {
        return $this->property;
    }

    public function setProperty(Property $property): PropertyAd
    {
        $this->property = $property;

        return $this;
    }
}
