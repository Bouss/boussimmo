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
    private int $selogerNeufCode;

    /**
     * @param string $name
     * @param string $departmentCode
     * @param string $department
     * @param string $region
     * @param int    $inseeCode
     * @param int    $logicImmoCode
     * @param int    $papCode
     * @param int    $selogerNeufCode
     */
    public function __construct(
        string $name,
        string $departmentCode,
        string $department,
        string $region,
        int $inseeCode,
        int $logicImmoCode,
        int $papCode,
        int $selogerNeufCode
    ) {
        $this->name = $name;
        $this->zipCode = $departmentCode . '000';
        $this->departmentCode = $departmentCode;
        $this->department = $department;
        $this->region = $region;
        $this->inseeCode = $inseeCode;
        $this->logicImmoCode = $logicImmoCode;
        $this->papCode = $papCode;
        $this->selogerNeufCode = $selogerNeufCode;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getZipCode(): string
    {
        return $this->zipCode;
    }

    /**
     * @return string
     */
    public function getDepartmentCode(): string
    {
        return $this->departmentCode;
    }

    /**
     * @return string
     */
    public function getDepartment(): string
    {
        return $this->department;
    }

    /**
     * @return string
     */
    public function getRegion(): string
    {
        return $this->region;
    }

    /**
     * @return int
     */
    public function getInseeCode(): int
    {
        return $this->inseeCode;
    }

    /**
     * @return int
     */
    public function getLogicImmoCode(): int
    {
        return $this->logicImmoCode;
    }

    /**
     * @return int
     */
    public function getPapCode(): int
    {
        return $this->papCode;
    }

    /**
     * @return int
     */
    public function getSelogerNeufCode(): int
    {
        return $this->selogerNeufCode;
    }
}
