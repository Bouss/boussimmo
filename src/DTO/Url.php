<?php

namespace App\DTO;

class Url
{
    public function __construct(
        private string $website,
        private string $logo,
        private string $value
    ) {}

    public function getWebsite(): string
    {
        return $this->website;
    }

    public function getLogo(): string
    {
        return $this->logo;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
