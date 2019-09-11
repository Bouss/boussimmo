<?php

namespace App\Client;

use App\Definition\MailProviderEnum;
use App\Definition\SiteEnum;
use App\Exception\MailboxConnectionException;
use DateTime;
use Exception;
use PhpImap\IncomingMail;
use SecIT\ImapBundle\Service\Imap;

class EmailClient
{
    private const FROM_BIENICI = 'no_reply@bienici.com';
    private const FROM_FNAIM = 'no-reply@fnaim.fr';
    private const FROM_LEBONCOIN = 'no.reply@leboncoin.fr';
    private const FROM_LOGIC_IMMO = 'Logic-immo.com';
    private const FROM_LOGIC_IMMO_NEUF = 'Logic-ImmoNeuf.com';
    private const FROM_OUESTFRANCE_IMMO = 'alerte@news.ouestfrance-immo.com';
    private const FROM_SELOGER = 'seloger@al.alerteimmo.com';

    private const FROM_ADDRESS_BY_PROVIDER = [
        MailProviderEnum::BIENICI => self::FROM_BIENICI,
        MailProviderEnum::FNAIM => self::FROM_FNAIM,
        MailProviderEnum::LEBONCOIN => self::FROM_LEBONCOIN,
        MailProviderEnum::LOGIC_IMMO => self::FROM_LOGIC_IMMO,
        MailProviderEnum::LOGIC_IMMO_NEUF => self::FROM_LOGIC_IMMO_NEUF,
        MailProviderEnum::OUESTFRANCE_IMMO => self::FROM_OUESTFRANCE_IMMO,
        MailProviderEnum::SELOGER => self::FROM_SELOGER
    ];

    private const DEFAULT_CONNECTION = 'gmail';
    private const SINCE = '-7 days';
    private const IMAP_DATE_FORMAT = 'd-M-Y';

    /**
     * @var Imap
     */
    private $imap;

    /**
     * @param Imap $imap
     */
    public function __construct(Imap $imap)
    {
        $this->imap = $imap;
    }

    /**
     * @param string $provider
     * @param string $since
     *
     * @return IncomingMail[]
     *
     * @throws Exception
     * @throws MailboxConnectionException
     */
    public function getMails(string $provider, string $since = self::SINCE): array
    {
        $mails = [];
        $from = self::FROM_ADDRESS_BY_PROVIDER[$provider];
        $since = (new DateTime($since))->format(self::IMAP_DATE_FORMAT);

        try {
            $connection = $this->imap->get(self::DEFAULT_CONNECTION);
        } catch (Exception $e) {
            throw new MailboxConnectionException(sprintf('Impossible to connect to %s: %s', self::DEFAULT_CONNECTION, $e->getMessage()));
        }

        $mailIds = $connection->searchMailbox("FROM $from SINCE $since");

        foreach ($mailIds as $mailId) {
            $mails[] = $connection->getMail($mailId);
        }

        return $mails;
    }
}
