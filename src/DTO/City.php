<?php

namespace App\DTO;

class City
{
    private string $name;
    private string $zipCode;
    private string $departmentCode;
    private string $department;
    private string $region;
    private int $inseeCode;
    private int $logicImmoCode;
    private int $papCode;
    private int $seLogerNeufCode;

    public function __construct(
        string $name,
        string $departmentCode,
        string $department,
        string $region,
        int $inseeCode,
        int $logicImmoCode,
        int $papCode,
        int $seLogerNeufCode
    ) {
        $this->name = $name;
        $this->zipCode = $departmentCode . '000';
        $this->departmentCode = $departmentCode;
        $this->department = $department;
        $this->region = $region;
        $this->inseeCode = $inseeCode;
        $this->logicImmoCode = $logicImmoCode;
        $this->papCode = $papCode;
        $this->seLogerNeufCode = $seLogerNeufCode;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getZipCode(): string
    {
        return $this->zipCode;
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
}
