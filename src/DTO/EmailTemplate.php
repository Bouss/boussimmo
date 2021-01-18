<?php

namespace App\DTO;

class EmailTemplate
{
    private string $name;
    private string $providerName;
    private string $address;
    private string $from;
    private ?string $subjectKeyword;

    public function __construct(string $name, string $providerName, string $username, string $address, string $subjectKeyword = null)
    {
        $this->name = $name;
        $this->providerName = $providerName;
        $this->address = $address;
        $this->from = sprintf('%s <%s>', $username, $address);
        $this->subjectKeyword = $subjectKeyword;
    }

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

    public function getFrom(): string
    {
        return $this->from;
    }

    public function getSubjectKeyword(): ?string
    {
        return $this->subjectKeyword;
    }
}
