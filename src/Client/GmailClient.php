<?php

namespace App\Client;

use App\Service\EmailTemplateService;
use Exception;
use Google_Service_Gmail;
use Google_Service_Gmail_Message;
use Google_Service_Gmail_Label;
use Psr\Log\LoggerInterface;

class GmailClient
{
    /**
     * @var Google_Service_Gmail
     */
    private $gmailService;

    /**
     * @var EmailTemplateService
     */
    private $emailTemplateService;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param Google_Service_Gmail $gmailService
     * @param EmailTemplateService $emailTemplateService
     * @param LoggerInterface      $logger
     */
    public function __construct(Google_Service_Gmail $gmailService, EmailTemplateService $emailTemplateService, LoggerInterface $logger)
    {
        $this->gmailService = $gmailService;
        $this->emailTemplateService = $emailTemplateService;
        $this->logger = $logger;
    }

    /**
     * @param string      $accessToken
     * @param string|null $labelId
     * @param string|null $provider
     * @param int         $newerThan
     *
     * @return int[]
     */
    public function getMessageIds(string $accessToken, ?string $labelId, ?string $provider, int $newerThan): array
    {
        $this->gmailService->getClient()->setAccessToken($accessToken);

        $ids = [];
        $messages = [];
        $pageToken = null;
        $params['q'] = $this->buildMessagesQuery($provider, $newerThan);
        if (!empty($labelId)) {
            $params['labelIds'] = [$labelId];
        }

        do {
            try {
                $params['pageToken'] = $pageToken ?: null;

                $messagesResponse = $this->gmailService->users_messages->listUsersMessages('me', $params);

                if ($messagesResponse->getMessages()) {
                    $messages[] = $messagesResponse->getMessages();
                    $pageToken = $messagesResponse->getNextPageToken();
                }
            } catch (Exception $e) {
                $this->logger->error('Error while retrieving messages: ' . $e->getMessage(), $params);
            }
        } while (null !== $pageToken);

        if (!empty($messages)) {
            $ids = array_column(array_merge(...$messages), 'id');
        }

        return $ids;
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
     * @param string $accessToken
     * @param string $userId
     *
     * @return Google_Service_Gmail_Label[]
     */
    public function getLabels(string $accessToken, string $userId = 'me'): array
    {
        $this->gmailService->getClient()->setAccessToken($accessToken);

        return $this->gmailService->users_labels->listUsersLabels($userId)->getLabels();
    }

    /**
     * @param string|null $provider
     * @param int|null    $newerThan
     *
     * @return string
     */
    private function buildMessagesQuery(string $provider = null, int $newerThan = null): string
    {
        $fromFilter = sprintf('from:(%s)', implode(' | ', $this->emailTemplateService->getProviderEmails($provider)));
        $dateFilter = sprintf('newer_than:%dd', $newerThan);

        return implode(' ', [$fromFilter, $dateFilter]);
    }
}
