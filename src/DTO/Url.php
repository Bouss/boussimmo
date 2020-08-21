<?php

namespace App\DTO;

class Url
{
    private string $website;
    private string $logo;
    private string $value;

    public function __construct(string $website, string $logo, string $value)
    {
        $this->website = $website;
        $this->logo = $logo;
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getWebsite(): string
    {
        return $this->website;
    }

    /**
     * @return string
     */
    public function getLogo(): string
    {
        return $this->logo;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
