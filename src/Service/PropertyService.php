<?php

namespace App\Service;

use App\Client\GmailClient;
use App\DataProvider\EmailTemplateProvider;
use App\DTO\Property;
use App\Entity\User;
use App\Exception\GmailException;
use App\Exception\GoogleException;
use App\Exception\ParseException;
use App\Exception\ParserNotFoundException;
use App\Parser\ParserContainer;
use Psr\Log\LoggerInterface;
use function array_merge;

class PropertyService
{
    public function __construct(
        private GmailClient $gmailClient,
        private GmailMessageService $gmailMessageService,
        private GoogleOAuthService $googleOAuthService,
        private ParserContainer $parserContainer,
        private EmailTemplateProvider $emailTemplateProvider,
        private LoggerInterface $logger
    ) {}

    /**
     * @return Property[]
     *
     * @throws GmailException|GoogleException|ParserNotFoundException
     */
    public function find(User $user, array $criteria): array
    {
        $properties = [];

        // Refresh the access token if expired
        $this->googleOAuthService->refreshAccessTokenIfExpired($user);

        // Fetch the Gmail messages IDs matching the user criteria
        $messageIds = $this->gmailClient->getMessageIds($criteria, $user->getAccessToken());

        foreach ($messageIds as $id) {
            try {
                $message = $this->gmailClient->getMessage($id);
            } catch (GmailException $e) {
                $this->logger->error('Error while retrieving a message: ' . $e->getMessage(), ['id' => $id]);

                continue;
            }

            $headers = $this->gmailMessageService->getHeaders($message);
            $html = $this->gmailMessageService->getHtml($message);

            // Find the email template matching the email headers
            $emailTemplate = $this->emailTemplateProvider->find($headers['from'], $headers['subject']);

            if (null === $emailTemplate) {
                $this->logger->error('No email template found', $headers);

                continue;
            }

            // Parse the HTML content to extract properties
            try {
                $properties[] = $this->parserContainer->get($emailTemplate->getName())->parse($html, $criteria, [
                    'email_template' => $emailTemplate->getName(),
                    'date' => $headers['date']
                ]);
            } catch (ParseException $e) {
                $this->logger->error($e->getMessage(), array_merge($headers, ['email_template' => $emailTemplate->getName()]));

                continue;
            }
        }

        // Flatten the array
        $properties = array_merge(...$properties);

        // Remove duplicates from same provider
        $this->removeDuplicates($properties);

        // Group property ads by property
        $this->groupPropertyAds($properties);

        return $properties;
    }

    /**
     * @param Property[] $properties
     */
    private function removeDuplicates(array &$properties): void
    {
        foreach ($properties as &$comparedProperty) {
            foreach ($properties as $i => $property) {
                if (
                    $comparedProperty !== $property &&
                    $comparedProperty->getAd()->getProvider() === $property->getAd()->getProvider() &&
                    $comparedProperty->equals($property)
                ) {
                    unset($properties[$i]);
                }
            }
        }
    }

    /**
     * @param Property[] $properties
     */
    private function groupPropertyAds(array &$properties): void
    {
        foreach ($properties as &$comparedProperty) {
            foreach ($properties as $i => $property) {
                if ($comparedProperty !== $property && $comparedProperty->equals($property)) {
                    $comparedProperty->addAd($property->getAd());
                    unset($properties[$i]);
                }
            }
        }
    }
}
