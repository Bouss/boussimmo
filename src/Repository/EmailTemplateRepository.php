<?php

namespace App\Repository;

use App\DTO\EmailTemplate;
use App\DTO\Provider;
use Symfony\Component\Serializer\SerializerInterface;
use function array_filter;
use function array_map;
use function array_unique;
use function in_array;
use function stripos;

class EmailTemplateRepository
{
    /** @var EmailTemplate[] */
    private array $emailTemplates;
    private ProviderRepository $providerRepository;

    /**
     * @param array               $emailTemplates
     * @param SerializerInterface $serializer
     * @param ProviderRepository  $providerRepository
     */
    public function __construct(array $emailTemplates, SerializerInterface $serializer, ProviderRepository $providerRepository)
    {
        $this->emailTemplates = $serializer->denormalize($emailTemplates, EmailTemplate::class . '[]');
        $this->providerRepository = $providerRepository;
    }

    /**
     * @param string $from
     * @param string $subject
     *
     * @return EmailTemplate|null
     */
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
     * @param string|null $mainProviderId
     *
     * @return string[]
     */
    public function getEmailAddresses(string $mainProviderId = null): array
    {
        $templates = $this->emailTemplates;

        if (null !== $mainProviderId) {
            $providers = $this->providerRepository->getAllProviders($mainProviderId);
            $providerIds = array_map(fn(Provider $provider) => $provider->getId(), $providers);

            $templates = array_filter($templates, fn(EmailTemplate $template) =>
                in_array($template->getProviderId(), $providerIds, true)
            );
        }

        $emails = array_map(fn(EmailTemplate $template) => $template->getEmailAddress(), $templates);

        return array_unique($emails);
    }
}
