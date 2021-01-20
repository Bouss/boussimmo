<?php

namespace App\DTO;

use Stringable;

class City implements Stringable
{
    public function __construct(
        private string $name,
        private string $departmentCode,
        private string $department,
        private string $region,
        private int $inseeCode,
        private int $logicImmoCode,
        private int $papCode,
        private int $seLogerNeufCode
    ) {}

    public function getName(): string
    {
        return $this->name;
    }

    public function getDepartmentCode(): string
    {
        return $this->departmentCode;
    }

    public function getDepartment(): string
    {
        return $this->department;
    }

    public function getRegion(): string
    {
        return $this->region;
    }

    public function getInseeCode(): int
    {
        return $this->inseeCode;
    }

    public function getLogicImmoCode(): int
    {
        return $this->logicImmoCode;
    }

    public function getPapCode(): int
    {
        return $this->papCode;
    }

    public function getSeLogerNeufCode(): int
    {
        return $this->seLogerNeufCode;
    }

    public function getZipCode(): string
    {
        return $this->departmentCode . '000';
    }

    /**
     * {@inheritDoc}
     */
    public function __toString(): string
    {
        return $this->name;
    }
}
