<?php

namespace App\Client;

use App\DataProvider\EmailTemplateProvider;
use App\Enum\PropertyFilter;
use App\Exception\GmailApiException;
use App\Exception\GoogleInsufficientPermissionException;
use Exception;
use Google\Service\Gmail\Label;
use Google\Service\Gmail\Message;
use Google_Service_Gmail;

class GmailApiClient
{
    public function __construct(
        private Google_Service_Gmail $gmailService,
        private EmailTemplateProvider $emailTemplateProvider
    ) {}

    /**
     * @return Message[]
     *
     * @throws GmailApiException|GoogleInsufficientPermissionException
     */
    public function getMessages(array $criteria, string $accessToken, string $userId = 'me'): array
    {
        $this->gmailService->getClient()->setAccessToken($accessToken);

        $messages = [];
        $labelId = $criteria[PropertyFilter::GMAIL_LABEL] ?? null;
        $provider = $criteria[PropertyFilter::PROVIDER] ?? null;
        $newerThan = $criteria[PropertyFilter::NEWER_THAN];

        // Prepare the Gmail query
        $params = ['q' => $this->buildMessagesQuery($provider, $newerThan)];
        if (!empty($labelId)) {
            $params['labelIds'] = [$labelId];
        }

        do {
            try {
                $response = $this->gmailService->users_messages->listUsersMessages($userId, $params);
            } catch (Exception $e) {
                $error = json_decode($e->getMessage(), true);

                if (403 === $error['error']['code']) {
                    throw new GoogleInsufficientPermissionException($error['error']['message']);
                }

                throw new GmailApiException($error['error']['message']);
            }

            $messages[] = $response->getMessages();
        } while (null !== $params['pageToken'] = $response->getNextPageToken());

        // Flatten the array
        return array_merge(...$messages);
    }

    /**
     * @throws GmailApiException|GoogleInsufficientPermissionException
     */
    public function getMessage(string $messageId, string $userId = 'me'): Message
    {
        try {
            return $this->gmailService->users_messages->get($userId, $messageId);
        } catch (Exception $e) {
            $error = json_decode($e->getMessage(), true);

            if (403 === $error['error']['code']) {
                throw new GoogleInsufficientPermissionException($error['error']['message']);
            }

            throw new GmailApiException($error['error']['message']);
        }
    }

    /**
     * @return Label[]
     *
     * @throws GmailApiException|GoogleInsufficientPermissionException
     */
    public function getLabels(string $accessToken, string $userId = 'me'): array
    {
        $this->gmailService->getClient()->setAccessToken($accessToken);

        try {
            return $this->gmailService->users_labels->listUsersLabels($userId)->getLabels();
        } catch (Exception $e) {
            $error = json_decode($e->getMessage(), true);

            if (403 === $error['error']['code']) {
                throw new GoogleInsufficientPermissionException($error['error']['message']);
            }

            throw new GmailApiException($error['error']['message']);
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
