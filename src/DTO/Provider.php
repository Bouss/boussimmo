<?php

namespace App\DTO;

class Provider
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $logo;

    /**
     * @var string|null
     */
    private $parentProvider;

    /**
     * @param string      $id
     * @param string      $logo
     * @param string|null $parentProvider
     */
    public function __construct(string $id, string $logo, string $parentProvider = null)
    {
        $this->id = $id;
        $this->logo = $logo;
        $this->parentProvider = $parentProvider;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getLogo(): string
    {
        return $this->logo;
    }

    /**
     * @return string|null
     */
    public function getParentProvider(): ?string
    {
        return $this->parentProvider;
    }
}
