<?php

namespace App\DTO;

class Site
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $code;

    /**
     * @var string
     */
    private $type;

    /**
     * @var string[]
     */
    private $cities;

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
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @param string $code
     *
     * @return Site
     */
    public function setCode(string $code): Site
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return Site
     */
    public function setType(string $type): Site
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getCities(): array
    {
        return $this->cities;
    }

    /**
     * @param string[] $cities
     *
     * @return Site
     */
    public function setCities(array $cities): Site
    {
        $this->cities = $cities;

        return $this;
    }
}
