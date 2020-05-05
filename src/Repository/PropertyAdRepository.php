<?php

namespace App\Repository;

use App\Client\GmailClient;
use App\DTO\PropertyAd;
use App\Exception\ParseException;
use App\Exception\ParserNotFoundException;
use App\ParserContainer;
use App\Service\GmailService;
use Exception;
use Psr\Log\LoggerInterface;

class PropertyAdRepository
{
    private GmailClient $gmailClient;
    private ParserContainer $parserContainer;
    private GmailService $gmailService;
    private EmailTemplateRepository $emailTemplateRepository;
    private LoggerInterface $logger;

    /**
     * @param GmailClient             $gmailClient
     * @param ParserContainer         $parserContainer
     * @param GmailService            $gmailService
     * @param EmailTemplateRepository $emailTemplateRepository
     * @param LoggerInterface         $logger
     */
    public function __construct(
        GmailClient $gmailClient,
        ParserContainer $parserContainer,
        GmailService $gmailService,
        EmailTemplateRepository $emailTemplateRepository,
        LoggerInterface $logger
    ) {
        $this->gmailClient = $gmailClient;
        $this->parserContainer = $parserContainer;
        $this->gmailService = $gmailService;
        $this->emailTemplateRepository = $emailTemplateRepository;
        $this->logger = $logger;
    }

    /**
     * @param string $accessToken
     * @param array  $filters
     *
     * @return PropertyAd[]
     *
     * @throws ParserNotFoundException
     */
    public function find(string $accessToken, array $filters): array
    {
        $propertyAds = [];
        $messageIds = $this->gmailClient->getMessageIds($accessToken, $filters);

        foreach ($messageIds as $id) {
            try {
                $message = $this->gmailClient->getMessage($id);
            } catch (Exception $e) {
                $this->logger->error('Error while retrieving a message: ' . $e->getMessage(), ['id' => $id]);
                continue;
            }

            $headers = $this->gmailService->getHeaders($message);
            $html = $this->gmailService->getHtml($message);

            // Find the email template matching the email headers
            $emailTemplate = $this->emailTemplateRepository->find($headers['from'], $headers['subject']);

            if (null === $emailTemplate) {
                $this->logger->error('No email template found', $headers);
                continue;
            }

            // Parse the HTML content to extract property ads
            try {
                $propertyAds[] = $this->parserContainer->get($emailTemplate->getId())->parse($html, $filters, [
                    'email_template' => $emailTemplate->getId(),
                    'date' => $headers['date']
                ]);
            } catch (ParseException $e) {
                $this->logger->error($e->getMessage(), array_merge($headers, ['email_template' => $emailTemplate->getId()]));
                continue;
            }
        }

        $propertyAds = array_merge(...$propertyAds);

        // Remove duplicates from same provider
        $this->removeDuplicates($propertyAds);

        // Group same property ads from different providers
        $this->groupPropertyAds($propertyAds);

        return $propertyAds;
    }

    /**
     * @param PropertyAd[] $propertyAds
     */
    private function removeDuplicates(array &$propertyAds): void
    {
        foreach ($propertyAds as &$comparedAd) {
            foreach ($propertyAds as $i => $ad) {
                if ($comparedAd !== $ad && $comparedAd->equals($ad, true)) {
                    unset($propertyAds[$i]);
                }
            }
        }
    }

    /**
     * @param PropertyAd[] $propertyAds
     */
    private function groupPropertyAds(array &$propertyAds): void
    {
        foreach ($propertyAds as &$comparedAd) {
            foreach ($propertyAds as $i => $ad) {
                if ($comparedAd !== $ad && $comparedAd->equals($ad)) {
                    $comparedAd->addDuplicate($ad);
                    unset($propertyAds[$i]);
                }
            }
        }
    }
}
