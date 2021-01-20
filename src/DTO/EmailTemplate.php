<?php

namespace App\DTO;

use Stringable;

class EmailTemplate implements Stringable
{
    public function __construct(
        private string $name,
        private string $providerName,
        private string $username,
        private string $address,
        private ?string $subjectKeyword = null
    ) {}

    public function getName(): string
    {
        return $this->name;
    }

    public function getProviderName(): string
    {
        return $this->providerName;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function getSubjectKeyword(): ?string
    {
        return $this->subjectKeyword;
    }

    public function getFrom(): string
    {
        return sprintf('%s <%s>', $this->username, $this->address);
    }

    /**
     * {@inheritDoc}
     */
    public function __toString(): string
    {
        return $this->name;
    }
}
