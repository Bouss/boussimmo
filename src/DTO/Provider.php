<?php

namespace App\DTO;

use Stringable;

class Provider implements Stringable
{
    public function __construct(
        private string $name,
        private string $logo,
        private ?string $parent = null
    ) {}

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
        return null !== $this->parent;
    }

    /**
     * {@inheritDoc}
     */
    public function __toString(): string
    {
        return $this->name;
    }
}
