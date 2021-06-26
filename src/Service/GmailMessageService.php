<?php

namespace App\Service;

use App\Util\StringUtil;
use DateTime;
use Google_Service_Gmail_Message;
use function Symfony\Component\String\u;

class GmailMessageService
{
    public function getHeaders(Google_Service_Gmail_Message $message): array
    {
        $headers = [];

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

    public function getHtml(Google_Service_Gmail_Message $message): string
    {
        $html = '';
        $payload = $message->getPayload();

        if ('text/html' === $payload->getMimeType() && null !== $body = $payload->getBody()->getData()) {
            $html .= StringUtil::base64UrlDecode($body);
        }

        foreach ($message->getPayload()->getParts() as $part) {
            if ('text/html' === $part->getMimeType() && null !== $body = $part->getBody()->getData()) {
                $html .= StringUtil::base64UrlDecode($body);
            }
        }

        return u($html)->collapseWhitespace();
    }
}
