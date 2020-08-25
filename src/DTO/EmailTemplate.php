<?php

namespace App\DTO;

class EmailTemplate
{
    private string $id;
    private string $providerId;
    private string $address;
    private string $from;
    private ?string $subjectKeyword;

    /**
     * @param string      $id
     * @param string      $providerId
     * @param string      $name
     * @param string      $address
     * @param string|null $subjectKeyword
     */
    public function __construct(string $id, string $providerId, string $name, string $address, string $subjectKeyword = null)
    {
        $this->id = $id;
        $this->providerId = $providerId;
        $this->address = $address;
        $this->from = sprintf('%s <%s>', $name, $address);
        $this->subjectKeyword = $subjectKeyword;
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
    public function getProviderId(): string
    {
        return $this->providerId;
    }

    /**
     * @return string
     */
    public function getAddress(): string
    {
        return $this->address;
    }

    /**
     * @return string
     */
    public function getFrom(): string
    {
        return $this->from;
    }

    /**
     * @return string|null
     */
    public function getSubjectKeyword(): ?string
    {
        return $this->subjectKeyword;
    }
}
