<?php

namespace App\Service;

use App\Client\GmailClient;
use App\DataProvider\EmailTemplateProvider;
use App\DTO\Property;
use App\Entity\User;
use App\Exception\GmailException;
use App\Exception\GoogleException;
use App\Exception\GoogleRefreshTokenException;
use App\Exception\ParseException;
use App\Exception\ParserNotFoundException;
use App\Parser\ParserLocator;
use Psr\Log\LoggerInterface;
use function array_merge;

class PropertyService
{
    private const ORDER_ASC = 1;

    public function __construct(
        private GmailClient $gmailClient,
        private GmailMessageService $gmailMessageService,
        private GoogleOAuthService $googleOAuthService,
        private ParserLocator $parserLocator,
        private EmailTemplateProvider $emailTemplateProvider,
        private LoggerInterface $logger
    ) {}

    /**
     * @return Property[]
     *
     * @throws GmailException|GoogleException|GoogleRefreshTokenException|ParserNotFoundException
     */
    public function find(User $user, array $criteria, array $sort): array
    {
        $properties = [];

        // Refresh the access token if expired. Otherwise, get the current access token
        $accessToken = $this->googleOAuthService->refreshAccessTokenIfExpired($user);

        // Fetch the Gmail messages IDs matching the user criteria
        $messages = $this->gmailClient->getMessages($criteria, $accessToken);

        foreach ($messages as $message) {
            try {
                $message = $this->gmailClient->getMessage($message['id']);
            } catch (GmailException $e) {
                $this->logger->error('Error while retrieving a message: ' . $e->getMessage(), ['message_id' => $message['id']]);

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
                $properties[] = $this->parserLocator->get($emailTemplate->getName())->parse($html, $criteria, [
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

        // Sort properties
        $this->sort($properties, $sort[0], $sort[1]);

        return $properties;
    }

    /**
     * @param Property[] $properties
     */
    private function removeDuplicates(array &$properties): void
    {
        foreach ($properties as $i => $newestProperty) {
            foreach ($properties as $oldestProperty) {
                if (
                    $oldestProperty !== $newestProperty &&
                    $oldestProperty->getAd()->getProvider() === $newestProperty->getAd()->getProvider() &&
                    $oldestProperty->equals($newestProperty)
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
        foreach ($properties as $i => $newestProperty) {
            foreach ($properties as $oldestProperty) {
                if ($oldestProperty !== $newestProperty && $oldestProperty->equals($newestProperty)) {
                    $oldestProperty->addAd($newestProperty->getAd());
                    unset($properties[$i]);
                }
            }
        }
    }

    /**
     * @param Property[] $properties
     */
    private function sort(array &$properties, string $field, int $order = self::ORDER_ASC): void
    {
        $getter = 'get' . ucfirst($field);

        if (!method_exists(Property::class, $getter)) {
            return;
        }

        usort($properties, static function (Property $p1, Property $p2) use ($getter, $order) {
            $comparison = $p1->{$getter}() <=> $p2->{$getter}();

            return self::ORDER_ASC === $order ? $comparison : -$comparison;
        });
    }
}
