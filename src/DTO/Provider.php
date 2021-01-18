<?php

namespace App\DTO;

class Provider
{
    private string $name;
    private string $logo;
    private ?string $parent;
    private bool $newBuildOnly;

    public function __construct(string $name, string $logo, string $parent = null)
    {
        $this->name = $name;
        $this->logo = $logo;
        $this->parent = $parent;
        $this->newBuildOnly = null !== $parent;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getLogo(): string
    {
        return $this->logo;
    }

    public function getParent(): ?string
    {
        return $this->parent;
    }

    public function isNewBuildOnly(): bool
    {
        return $this->newBuildOnly;
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
