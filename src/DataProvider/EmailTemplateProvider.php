<?php

namespace App\DataProvider;

use App\DTO\EmailTemplate;
use App\DTO\Provider;
use Symfony\Component\Serializer\SerializerInterface;
use function array_filter;
use function array_map;
use function array_unique;
use function in_array;
use function stripos;

class EmailTemplateProvider
{
    /** @var EmailTemplate[] */
    private array $emailTemplates;
    private ProviderProvider $providerProvider;

    public function __construct(array $emailTemplates, SerializerInterface $serializer, ProviderProvider $providerProvider)
    {
        $this->emailTemplates = $serializer->denormalize($emailTemplates, EmailTemplate::class . '[]');
        $this->providerProvider = $providerProvider;
    }

    public function find(string $from, string $subject): ?EmailTemplate
    {
        foreach ($this->emailTemplates as $template) {
            if ($from === $template->getFrom()) {
                // First, try to match an email template containing a particular subject keyword
                if (null !== $template->getSubjectKeyword()) {
                    if (false !== stripos($subject, $template->getSubjectKeyword())) {
                        return $template;
                    }

                    continue;
                }

                return $template;
            }
        }

        return null;
    }

    /**
     * @return string[]
     */
    public function getAllAddresses(): array
    {
        $addresses = array_map(static fn(EmailTemplate $template) => $template->getAddress(), $this->emailTemplates);

        return array_unique($addresses);
    }

    /**
     * @return string[]
     */
    public function getAddressesByMainProvider(string $mainProviderName): array
    {
        $providers = $this->providerProvider->getProvidersByMainProvider($mainProviderName);
        $providerNames = array_map(static fn(Provider $provider) => $provider->getName(), $providers);

        $templates = array_filter($this->emailTemplates, static fn(EmailTemplate $template) =>
            in_array($template->getProviderName(), $providerNames, true)
        );

        $addresses = array_map(static fn(EmailTemplate $template) => $template->getAddress(), $templates);

        return array_unique($addresses);
    }
}
