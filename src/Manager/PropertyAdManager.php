<?php

namespace App\Manager;

use App\Client\EmailClient;
use App\Definition\MailProviderEnum;
use App\EmailParserContainer;
use App\Entity\PropertyAd;
use App\Exception\MailboxConnectionException;
use App\Exception\ParseException;
use App\Exception\ParserNotFoundException;
use DateTime;
use Exception;
use Psr\Log\LoggerInterface;

class PropertyAdManager
{
    /**
     * @var EmailClient
     */
    private $emailClient;

    /**
     * @var EmailParserContainer
     */
    private $parserContainer;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param EmailClient          $emailClient
     * @param EmailParserContainer $parserContainer
     * @param LoggerInterface      $logger
     */
    public function __construct(EmailClient $emailClient, EmailParserContainer $parserContainer, LoggerInterface $logger)
    {
        $this->emailClient = $emailClient;
        $this->parserContainer = $parserContainer;
        $this->logger = $logger;
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
    public function find(string $provider = null, string $since = null): array
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
