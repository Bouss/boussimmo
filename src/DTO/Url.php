<?php

namespace App\DTO;

class Url
{
    private string $website;
    private string $logo;
    private string $url;

    public function __construct(string $website, string $logo, string $url)
    {
        $this->website = $website;
        $this->logo = $logo;
        $this->url = $url;
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
    public function getUrl(): string
    {
        return $this->url;
    }
}
