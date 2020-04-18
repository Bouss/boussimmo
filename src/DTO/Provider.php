<?php

namespace App\DTO;

class Provider
{
    /**
     * @var string
     */
    public $id;

    /**
     * @var string
     */
    public $logo;

    /**
     * @var string|null
     */
    public $parentProvider;

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
}
