<?php

namespace App\Service;

use App\Util\StringUtil;
use DateTime;
use Google\Service\Gmail\Message;
use function Symfony\Component\String\u;

class GmailMessageService
{
    public function getCreatedAt(Message $message): DateTime
    {
        return new DateTime(sprintf('@%d', $message->internalDate / 1000));
    }

    public function getHeaders(Message $message): array
    {
        $headers = [];

        foreach ($message->getPayload()->getHeaders() as $header) {
            if ('From' === $header->name) {
                $headers['from'] = $header->value;
            }

            if ('Subject' === $header->name) {
                $headers['subject'] = $header->value;
            }

            if (2 === count($headers)) {
                break;
            }
        }

        return $headers;
    }

    public function getHtml(Message $message): string
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
