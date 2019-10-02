<?php

namespace App\Service;

use Symfony\Component\Yaml\Yaml;

class ProviderService
{
    private const PROVIDERS_FILE_PATH = '/config/providers.yaml';

    /**
     * @var array
     */
    private $providers;

    /**
     * @var array
     */
    private $providersByFrom;

    /**
     * @param string $projectDir
     */
    public function __construct(string $projectDir)
    {
        $this->providers = Yaml::parseFile($projectDir . self::PROVIDERS_FILE_PATH, Yaml::PARSE_CONSTANT);
        $this->initProvidersByFrom();
    }

    /**
     * @return string[]
     */
    public function getAllEmails(): array
    {
        return array_unique(array_column($this->providers, 'email'));
    }

    /**
     * @param string $from
     *
     * @return string
     */
    public function getProviderByFrom(string $from): string
    {
        return $this->providersByFrom[$from];
    }

    /**
     * @param string $provider
     *
     * @return string
     */
    public function getLogo(string $provider): string
    {
        return $this->providers[$provider]['logo'];
    }

    private function initProvidersByFrom(): void
    {
        foreach ($this->providers as $provider => $val) {
            $this->providersByFrom[$val['from']] = $provider;
        }
    }
}
