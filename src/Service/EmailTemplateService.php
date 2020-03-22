<?php

namespace App\Service;

use App\DTO\EmailTemplate;
use RuntimeException;
use Symfony\Component\Serializer\SerializerInterface;

class EmailTemplateService
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
     * @param string|null $provider
     *
     * @return string[]
     */
    public function getProviderEmails(string $provider = null): array
    {
        if (null !== $provider) {
            $templates = array_filter($this->emailTemplates, static function(EmailTemplate $template) use ($provider) {
                return $provider === $template->provider;
            });
        } else {
            $templates = $this->emailTemplates;
        }

        $emails = array_map(static function(EmailTemplate $template) {
            return $template->email;
        }, $templates);

        return array_unique($emails);
    }

    /**
     * @param string $from
     * @param string $subject
     *
     * @return string
     *
     * @throws RuntimeException
     */
    public function getEmailTemplate(string $from, string $subject): string
    {
        foreach ($this->emailTemplates as $template) {
            if ($from === $template->from) {
                // First, try to match an email template containing a particular subject keyword
                if (null !== $template->subject) {
                    if (false !== stripos($subject, $template->subject)) {
                        return $template->id;
                    }

                    continue;
                }

                return $template->id;
            }
        }

        throw new RuntimeException('No email template found');
    }
}
