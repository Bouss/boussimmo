<?php

namespace App\Client;

use App\DataProvider\EmailTemplateProvider;
use App\Enum\PropertyFilter;
use App\Exception\GmailException;
use Exception;
use Google_Service_Gmail;
use Google_Service_Gmail_Label;
use Google_Service_Gmail_Message;

class GmailClient
{
    private Google_Service_Gmail $gmailService;
    private EmailTemplateProvider $emailTemplateProvider;

    public function __construct(Google_Service_Gmail $gmailService, EmailTemplateProvider $emailTemplateProvider)
    {
        $this->gmailService = $gmailService;
        $this->emailTemplateProvider = $emailTemplateProvider;
    }

    /**
     * @return int[]
     *
     * @throws GmailException
     */
    public function getMessageIds(string $accessToken, array $criteria): array
    {
        $this->gmailService->getClient()->setAccessToken($accessToken);

        $messages = [];
        $labelId = $criteria[PropertyFilter::GMAIL_LABEL] ?? null;
        $provider = $criteria[PropertyFilter::PROVIDER] ?? null;
        $newerThan = $criteria[PropertyFilter::NEWER_THAN];

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
                throw new GmailException($e->getMessage());
            }

            $messages[] = $response->getMessages();
        } while (null !== $pageToken = $response->getNextPageToken());

        return array_column(array_merge(...$messages), 'id');
    }

    /**
     * @throws GmailException
     */
    public function getMessage(string $messageId, string $userId = 'me'): Google_Service_Gmail_Message
    {
        try {
            return $this->gmailService->users_messages->get($userId, $messageId);
        } catch (Exception $e) {
            throw new GmailException($e->getMessage());
        }
    }

    /**
     * @return Google_Service_Gmail_Label[]
     *
     * @throws GmailException
     */
    public function getLabels(string $accessToken, string $userId = 'me'): array
    {
        $this->gmailService->getClient()->setAccessToken($accessToken);

        try {
            return $this->gmailService->users_labels->listUsersLabels($userId)->getLabels();
        } catch (Exception $e) {
            throw new GmailException($e->getMessage());
        }
    }

    private function buildMessagesQuery(string $provider = null, int $newerThan = null): string
    {
        $addresses = null !== $provider ?
            $this->emailTemplateProvider->getAddressesByMainProvider($provider) :
            $this->emailTemplateProvider->getAllAddresses();

        $fromFilter = sprintf('from:(%s)', implode(' | ', $addresses));
        $dateFilter = sprintf('newer_than:%dd', $newerThan);

        return implode(' ', [$fromFilter, $dateFilter]);
    }
}
