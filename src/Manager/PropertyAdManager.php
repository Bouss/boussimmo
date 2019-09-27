<?php

namespace App\Manager;

use App\Client\EmailClient;
use App\Client\GmailClient;
use App\Definition\MailProviderEnum;
use App\EmailParserContainer;
use App\Entity\PropertyAd;
use App\Exception\MailboxConnectionException;
use App\Exception\ParseException;
use App\Exception\ParserNotFoundException;
use App\Service\GmailService;
use App\Service\ProviderService;
use DateTime;
use Exception;
use Psr\Log\LoggerInterface;

class PropertyAdManager
{
    /**
     * @var GmailClient
     */
    private $gmailClient;

    /**
     * @var EmailClient
     */
    private $emailClient;

    /**
     * @var EmailParserContainer
     */
    private $parserContainer;

    /**
     * @var GmailService
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
     * @param GmailClient          $gmailClient
     * @param EmailClient          $emailClient
     * @param EmailParserContainer $parserContainer
     * @param GmailService         $gmailService
     * @param ProviderService      $providerService
     * @param LoggerInterface      $logger
     */
    public function __construct(
        GmailClient $gmailClient,
        EmailClient $emailClient,
        EmailParserContainer $parserContainer,
        GmailService $gmailService,
        ProviderService $providerService,
        LoggerInterface $logger
    ) {
        $this->gmailClient = $gmailClient;
        $this->emailClient = $emailClient;
        $this->parserContainer = $parserContainer;
        $this->gmailService = $gmailService;
        $this->providerService = $providerService;
        $this->logger = $logger;
    }

    /**
     * @param string      $userToken
     * @param string      $label
     * @param string|null $provider
     * @param string|null $since
     *
     * @return PropertyAd[]
     *
     * @throws ParserNotFoundException
     */
    public function find(string $userToken, string $label = 'Label_22', string $provider = null, string $since = null): array
    {
        $ads = [];
        $messages = $this->gmailClient->getMessages($userToken, $label);

        foreach ($messages as $message) {
            try {
                $message = $this->gmailClient->getMessage($message['id']);
            } catch (Exception $e) {
                $this->logger->error('Error while retrieving a message: ' . $e->getMessage(), ['id' => $message['id']]);
                continue;
            }

            $from = $this->gmailService->getFrom($message);
            $date = $this->gmailService->getDate($message);
            $html = $this->gmailService->getHtml($message);

            $provider = $this->providerService->getProviderByFrom($from);

            try {
                $parsedAds = $this->parserContainer->get($provider)->parse($html);
            } catch (ParseException $e) {
                $this->logger->error($e->getMessage());
                continue;
            }

            $this->setPublishedAt($parsedAds, $date);
            $ads[] = $parsedAds;
        }

        if (!empty($ads)) {
            $ads = array_merge(...$ads);
        }

        $this->sort($ads);

        return $ads;
    }

    /**
     * @param string|null $provider
     * @param string|null $since
     *
     * @return PropertyAd[]
     *
     * @throws Exception
     * @throws MailboxConnectionException
     * @throws ParserNotFoundException
     */
    public function oldFind(string $provider = null, string $since = null): array
    {
        $ads = [];

        foreach (MailProviderEnum::getAvailableValues() as $p) {
            if (null !== $provider && $provider !== $p) {
                continue;
            }

            $parser = $this->parserContainer->get($p);
            $mails = $this->emailClient->getMails($p);

            foreach ($mails as $mail) {
                try {
                    $parsedAds = $parser->parse($mail->textHtml);
                    $this->setPublishedAt($parsedAds, new DateTime($mail->date));
                    $ads[] = $parsedAds;
                } catch (ParseException $e) {
                    $this->logger->error($e->getMessage());
                }
            }

            if (null !== $provider && $provider === $p) {
                break;
            }
        }

        if (!empty($ads)) {
            $ads = array_merge(...$ads);
        }

        $this->sort($ads);

        return $ads;
    }

    /**
     * @param array    $propertyAds
     * @param DateTime $date
     */
    private function setPublishedAt(array $propertyAds, DateTime $date): void
    {
        array_walk($propertyAds, static function (PropertyAd $ad) use ($date) {
            $ad->setPublishedAt($date);
        });
    }


    /**
     * @param PropertyAd[] $propertyAds
     */
    private function sort(array &$propertyAds): void
    {
        usort($propertyAds, static function (PropertyAd $ad1, PropertyAd $ad2) {
            return $ad2->getPublishedAt() <=> $ad1->getPublishedAt();
        });
    }
}
