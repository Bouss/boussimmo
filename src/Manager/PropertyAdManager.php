<?php

namespace App\Manager;

use App\Client\GmailClient;
use App\ParserContainer;
use App\DTO\PropertyAd;
use App\Exception\ParseException;
use App\Exception\ParserNotFoundException;
use App\Service\GmailService;
use App\Service\EmailTemplateManager;
use Exception;
use Psr\Log\LoggerInterface;
use RuntimeException;

class PropertyAdManager
{
    /**
     * @var GmailClient
     */
    private $gmailClient;

    /**
     * @var ParserContainer
     */
    private $parserContainer;

    /**
     * @var GmailService
     */
    private $gmailService;

    /**
     * @var EmailTemplateManager
     */
    private $providerService;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param GmailClient          $gmailClient
     * @param ParserContainer      $parserContainer
     * @param GmailService         $gmailService
     * @param EmailTemplateManager $providerService
     * @param LoggerInterface      $logger
     */
    public function __construct(
        GmailClient $gmailClient,
        ParserContainer $parserContainer,
        GmailService $gmailService,
        EmailTemplateManager $providerService,
        LoggerInterface $logger
    ) {
        $this->gmailClient = $gmailClient;
        $this->parserContainer = $parserContainer;
        $this->gmailService = $gmailService;
        $this->providerService = $providerService;
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

            try {
                $emailTemplate = $this->providerService->getEmailTemplate($headers['from'], $headers['subject'])->id;
                $propertyAds[] = $this->parserContainer->get($emailTemplate)->parse($html, $filters, [
                    'email_template' => $emailTemplate,
                    'date' => $headers['date']
                ]);
            } catch (RuntimeException | ParseException $e) {
                $this->logger->error($e->getMessage(), $headers);
                continue;
            }
        }

        if (!empty($propertyAds)) {
            $propertyAds = array_merge(...$propertyAds);
        }
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
