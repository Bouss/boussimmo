<?php

namespace App\Service;

use Symfony\Component\Yaml\Yaml;

class LocationService
{
    private const LOCATIONS_FILE_PATH = '/config/locations.yaml';

    /**
     * @var array
     */
    private $locations;

    /**
     * @param string $projectDir
     */
    public function __construct(string $projectDir)
    {
        $this->locations = Yaml::parseFile($projectDir . self::LOCATIONS_FILE_PATH, Yaml::PARSE_CONSTANT);
    }

    /**
     * @param string $site
     * @param string $city
     *
     * @return string
     */
    public function getLocation(string $site, string $city): string
    {
        return $this->locations[$site][$city];
    }
}
