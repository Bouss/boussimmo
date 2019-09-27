<?php

namespace App\Client;

use App\Service\ProviderService;
use Exception;
use Google_Service_Gmail;
use Google_Service_Gmail_Message;
use Psr\Log\LoggerInterface;

class GmailClient
{
    private const DEFAULT_NEWER_THAN = 7; // Days

    /**
     * @var Google_Service_Gmail
     */
    private $gmailService;

    /**
     * @var ProviderService
     */
    private $providerService;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param Google_Service_Gmail $gmailService
     * @param ProviderService      $providerService
     * @param LoggerInterface      $logger
     */
    public function __construct(Google_Service_Gmail $gmailService, ProviderService $providerService, LoggerInterface $logger)
    {
        $this->gmailService = $gmailService;
        $this->providerService = $providerService;
        $this->logger = $logger;
    }

    /**
     * @param string      $userToken
     * @param string|null $labelId
     * @param int         $newerThan
     *
     * @return Google_Service_Gmail_Message[]
     */
    public function getMessages(string $userToken, string $labelId = null, int $newerThan = self::DEFAULT_NEWER_THAN): array
    {
        $this->gmailService->getClient()->setAccessToken($userToken);

        $messages = [];
        $pageToken = null;
        $optParams['labelIds'] = [$labelId];
        $optParams['q'] = $this->buildMessagesQuery($newerThan);

        do {
            try {
                $optParams['pageToken'] = $pageToken ?: null;

                $messagesResponse = $this->gmailService->users_messages->listUsersMessages('me', $optParams);

                if ($messagesResponse->getMessages()) {
                    $messages[] = $messagesResponse->getMessages();
                    $pageToken = $messagesResponse->getNextPageToken();
                }
            } catch (Exception $e) {
                $this->logger->error('Error while retrieving messages: ' . $e->getMessage(), $optParams);
            }
        } while (null !== $pageToken);

        if (!empty($messages)) {
            $messages = array_merge(...$messages);
        }

        return $messages;
    }

    /**
     * @param string $messageId
     * @param string $userId
     *
     * @return Google_Service_Gmail_Message
     *
     * @throws Exception
     */
    public function getMessage(string $messageId, string $userId = 'me'): Google_Service_Gmail_Message
    {
        return $this->gmailService->users_messages->get($userId, $messageId);
    }

    /**
     * @param int $newerThan days
     *
     * @return string
     */
    private function buildMessagesQuery(int $newerThan): string
    {
        $fromFilter = sprintf('from:(%s)', implode(' | ', $this->providerService->getAllEmails()));
        $dateFilter = sprintf('newer_than:%dd', $newerThan);

        return implode(' ', [$fromFilter, $dateFilter]);
    }
}
