<?php

namespace App\Client;

use App\Enum\PropertyAdFilter;
use App\Exception\GoogleTokenRevokedException;
use App\Repository\EmailTemplateRepository;
use Exception;
use Google_Service_Exception;
use Google_Service_Gmail;
use Google_Service_Gmail_Label;
use Google_Service_Gmail_Message;
use Psr\Log\LoggerInterface;

class GmailClient
{
    private Google_Service_Gmail $gmailService;
    private EmailTemplateRepository $emailTemplateRepository;
    private LoggerInterface $logger;

    /**
     * @param Google_Service_Gmail    $gmailService
     * @param EmailTemplateRepository $emailTemplateRepository
     * @param LoggerInterface         $logger
     */
    public function __construct(Google_Service_Gmail $gmailService, EmailTemplateRepository $emailTemplateRepository, LoggerInterface $logger)
    {
        $this->gmailService = $gmailService;
        $this->emailTemplateRepository = $emailTemplateRepository;
        $this->logger = $logger;
    }

    /**
     * @param string $accessToken
     * @param array  $criteria
     *
     * @return int[]
     */
    public function getMessageIds(string $accessToken, array $criteria): array
    {
        $this->gmailService->getClient()->setAccessToken($accessToken);

        $messages = [];
        $labelId = $criteria[PropertyAdFilter::GMAIL_LABEL] ?? null;
        $provider = $criteria[PropertyAdFilter::PROVIDER] ?? null;
        $newerThan = $criteria[PropertyAdFilter::NEWER_THAN];

        // Prepare the Gmail messages query
        $params = ['q' => $this->buildMessagesQuery($provider, $newerThan)];
        if (!empty($labelId)) {
            $params['labelIds'] = [$labelId];
        }

        $pageToken = null;
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
     *
     * @throws GoogleTokenRevokedException
     */
    public function getLabels(string $accessToken, string $userId = 'me'): array
    {
        $this->gmailService->getClient()->setAccessToken($accessToken);

        try {
            return $this->gmailService->users_labels->listUsersLabels($userId)->getLabels();
        } catch (Google_Service_Exception $e) {
            throw new GoogleTokenRevokedException('Could not get the labels: ' . $e->getMessage());
        }
    }

    /**
     * @param string|null $provider
     * @param int|null    $newerThan
     *
     * @return string
     */
    private function buildMessagesQuery(string $provider = null, int $newerThan = null): string
    {
        $addresses = null !== $provider ?
            $this->emailTemplateRepository->getAddressesByMainProvider($provider) :
            $this->emailTemplateRepository->getAllAddresses();

        $fromFilter = sprintf('from:(%s)', implode(' | ', $addresses));
        $dateFilter = sprintf('newer_than:%dd', $newerThan);

        return implode(' ', [$fromFilter, $dateFilter]);
    }
}
