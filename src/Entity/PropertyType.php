<?php

namespace App\Entity;

class PropertyType
{
    public const APARTMENT = 1;
    public const HOUSE = 2;
    public const AVAILABLE_TYPES = [
        self::APARTMENT,
        self::HOUSE
    ];

    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $type;

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
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return PropertyType
     */
    public function setType(string $type): PropertyType
    {
        $this->type = $type;

        return $this;
    }
}
