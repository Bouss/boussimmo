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
     * @return array ['from', 'date', 'subject']
     *
     * @throws Exception
     */
    public function getHeaders(Google_Service_Gmail_Message $message): array
    {
        $headers = [];

        /** @var  Google_Service_Gmail_MessagePartHeader $header */
        foreach ($message->getPayload()->getHeaders() as $header) {
            if ('From' === $header->name) {
                $headers['from'] = $header->value;
            }

            if ('Date' === $header->name) {
                $headers['date'] = new DateTime($header->value);
            }

            if ('Subject' === $header->name) {
                $headers['subject'] = $header->value;
            }

            if (3 === count($headers)) {
                break;
            }
        }

        return $headers;
    }

    /**
     * @param Google_Service_Gmail_Message $message
     *
     * @return string
     */
    public function getHtml(Google_Service_Gmail_Message $message): string
    {
        $html = '';

        if (null !== $body = $message->getPayload()->getBody()->data) {
            $html = StringUtil::base64UrlDecode($body);
        }

        /** @var Google_Service_Gmail_MessagePart $part */
        foreach ($message->getPayload()->getParts() as $part) {
            if (null !== $body = $part->getBody()->data) {
                $html .= StringUtil::base64UrlDecode($body);
            }
        }

        return $html;
    }
}
