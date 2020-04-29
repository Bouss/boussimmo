<?php

namespace App\Repository;

use App\DTO\EmailTemplate;
use Symfony\Component\Serializer\SerializerInterface;

class EmailTemplateRepository
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
        $this->emailTemplates = $serializer->denormalize($emailTemplates, EmailTemplate::class . '[]');
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
     * @param string|null $providerId
     *
     * @return string[]
     */
    public function getEmailAddresses(string $providerId = null): array
    {
        $templates = $this->emailTemplates;

        if (null !== $providerId) {
            $templates = array_filter($templates, fn(EmailTemplate $template) => $providerId === $template->getProviderId());
        }

        $emails = array_map(fn(EmailTemplate $template) => $template->getEmailAddress(), $templates);

        return array_unique($emails);
    }
}
