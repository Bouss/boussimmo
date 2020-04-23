<?php

namespace App\Finder;

use App\DTO\EmailTemplate;
use RuntimeException;
use Symfony\Component\Serializer\SerializerInterface;

class EmailTemplateFinder
{
    /**
     * @var EmailTemplate[]
     */
    private $emailTemplates;

    /**
     * @param SerializerInterface $serializer
     * @param array               $emailTemplates
     */
    public function __construct(SerializerInterface $serializer, array $emailTemplates)
    {
        $this->emailTemplates = $serializer->denormalize($emailTemplates,'App\DTO\EmailTemplate[]');
    }

    /**
     * @param string $from
     * @param string $subject
     *
     * @return EmailTemplate
     *
     * @throws RuntimeException
     */
    public function find(string $from, string $subject): EmailTemplate
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

        throw new RuntimeException('No email template found');
    }

    /**
     * @param string|null $providerId
     *
     * @return string[]
     */
    public function getProviderEmails(string $providerId = null): array
    {
        $templates = $this->emailTemplates;

        if (null !== $providerId) {
            $templates = array_filter($templates, fn(EmailTemplate $template) => $providerId === $template->getProviderId());
        }

        $emails = array_map(fn(EmailTemplate $template) => $template->getEmail(), $templates);

        return array_unique($emails);
    }
}
