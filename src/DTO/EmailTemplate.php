<?php

namespace App\DTO;

class EmailTemplate
{
    /**
     * @var string
     */
    public $id;

    /**
     * @var string
     */
    public $provider;

    /**
     * @var string
     */
    public $email;

    /**
     * @var string
     */
    public $from;

    /**
     * @var string|null
     */
    public $subjectKeyword;

    /**
     * @param string      $id
     * @param string      $provider
     * @param string      $name
     * @param string      $email
     * @param string|null $subject
     */
    public function __construct(string $id, string $provider, string $name, string $email, string $subject = null)
    {
        $this->id = $id;
        $this->provider = $provider;
        $this->email = $email;
        $this->from = sprintf('%s <%s>', $name, $email);
        $this->subjectKeyword = $subject;
    }
}
