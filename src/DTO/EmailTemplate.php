<?php

namespace App\DTO;

class EmailTemplate
{
    private string $id;
    private string $providerId;
    private string $emailAddress;
    private string $from;
    private ?string $subjectKeyword;

    /**
     * @param string      $id
     * @param string      $providerId
     * @param string      $username
     * @param string      $emailAddress
     * @param string|null $subjectKeyword
     */
    public function __construct(string $id, string $providerId, string $username, string $emailAddress, string $subjectKeyword = null)
    {
        $this->id = $id;
        $this->providerId = $providerId;
        $this->emailAddress = $emailAddress;
        $this->from = sprintf('%s <%s>', $username, $emailAddress);
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
    public function getEmailAddress(): string
    {
        return $this->emailAddress;
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
