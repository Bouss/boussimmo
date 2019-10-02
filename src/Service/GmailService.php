<?php

namespace App\Service;

use App\Util\StringUtil;
use DateTime;
use Exception;
use Google_Service_Gmail_Message;
use Google_Service_Gmail_MessagePart;
use Google_Service_Gmail_MessagePartHeader;

class GmailService
{
    /**
     * @param Google_Service_Gmail_Message $message
     *
     * @return string
     */
    public function getFrom(Google_Service_Gmail_Message $message): string
    {
        /** @var  Google_Service_Gmail_MessagePartHeader $header */
        foreach ($message->getPayload()->getHeaders() as $header) {
            if ('From' === $header->name) {
                return $header->value;
            }
        }

        return '';
    }

    /**
     * @param Google_Service_Gmail_Message $message
     *
     * @return DateTime
     *
     * @throws Exception
     */
    public function getDate(Google_Service_Gmail_Message $message): DateTime
    {
        /** @var  Google_Service_Gmail_MessagePartHeader $header */
        foreach ($message->getPayload()->getHeaders() as $header) {
            if ('Date' === $header->name) {
                return new DateTime($header->value);
            }
        }

        return new DateTime();
    }

    /**
     * @param Google_Service_Gmail_Message $message
     *
     * @return string
     */
    public function getHtml(Google_Service_Gmail_Message $message): string
    {
        $html = '';

        $body = $message->getPayload()->getBody()->data;

        if (null !== $body) {
            $html = StringUtil::base64UrlDecode($body);
        }

        /** @var Google_Service_Gmail_MessagePart $part */
        foreach ($message->getPayload()->getParts() as $part) {
            $html .= StringUtil::base64UrlDecode($part->getBody()->data);
        }

        return $html;
    }
}
