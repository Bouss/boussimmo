<?php

namespace App\Client;

use App\Enum\PropertyAdFilter;
use App\Finder\EmailTemplateFinder;
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
     * @var EmailTemplateFinder
     */
    private $emailTemplateFinder;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param Google_Service_Gmail $gmailService
     * @param EmailTemplateFinder  $emailTemplateFinder
     * @param LoggerInterface      $logger
     */
    public function __construct(Google_Service_Gmail $gmailService, EmailTemplateFinder $emailTemplateFinder, LoggerInterface $logger)
    {
        $this->gmailService = $gmailService;
        $this->emailTemplateFinder = $emailTemplateFinder;
        $this->logger = $logger;
    }

    /**
     * @param string $accessToken
     * @param array  $filters
     *
     * @return int[]
     */
    public function getMessageIds(string $accessToken, array $filters): array
    {
        $this->gmailService->getClient()->setAccessToken($accessToken);

        $labelId = $filters[PropertyAdFilter::GMAIL_LABEL] ?? null;
        $provider = $filters[PropertyAdFilter::PROVIDER] ?? null;
        $newerThan = $filters[PropertyAdFilter::NEWER_THAN];
        $ids = [];
        $messages = [];
        $pageToken = null;

        // Prepare the Gmail messages query
        $params['q'] = $this->buildMessagesQuery($provider, $newerThan);
        if (!empty($labelId)) {
            $params['labelIds'] = [$labelId];
        }

        do {
            $params['pageToken'] = $pageToken;

            try {
                $response = $this->gmailService->users_messages->listUsersMessages('me', $params);
            } catch (Exception $e) {
                $this->logger->error('Error while retrieving messages: ' . $e->getMessage(), $params);
                break;
            }

            $messages[] = $response->getMessages();
        } while (null !== $pageToken = $response->getNextPageToken());

        return array_column(array_merge(...$messages), 'id');
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
        $fromFilter = sprintf('from:(%s)', implode(' | ', $this->emailTemplateFinder->getProviderEmails($provider)));
        $dateFilter = sprintf('newer_than:%dd', $newerThan);

        return implode(' ', [$fromFilter, $dateFilter]);
    }
}
