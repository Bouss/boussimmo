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
    private $providersWithSubject;

    /**
     * @var array
     */
    private $providersWithoutSubject;

    /**
     * @param string $projectDir
     */
    public function __construct(string $projectDir)
    {
        $this->providers = Yaml::parseFile($projectDir . self::PROVIDERS_FILE_PATH, Yaml::PARSE_CONSTANT);
        $this->initProviders();
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
     * @param string $subject
     *
     * @return string
     */
    public function getProviderByFromAndSubject(string $from, string $subject): string
    {
        foreach ($this->providersWithSubject as $provider => $fields) {
            if ($fields['from'] === $from && stripos($subject, $fields['subject'])) {
                return $provider;
            }
        }

        foreach ($this->providersWithoutSubject as $provider => $fields) {
            if ($fields['from'] === $from) {
                return $provider;
            }
        }

        return '';
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

    private function initProviders(): void
    {
        $this->providersWithSubject = array_filter($this->providers, static function (array $provider) {
            return isset($provider['subject']);
        });

        $this->providersWithoutSubject = array_diff_key($this->providers, $this->providersWithSubject);
    }
}
