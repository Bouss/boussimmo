<?php

namespace App\Service;

use App\Entity\User;
use App\Exception\GoogleException;
use DateTime;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;
use Google_Client;
use GuzzleHttp\Exception\RequestException;
use Psr\Log\LoggerInterface;

class GoogleOAuthService
{
    public function __construct(
        private Google_Client $googleClient,
        private EntityManagerInterface $em,
        private LoggerInterface $logger
    ) {}

    /**
     * @throws GoogleException
     */
    public function refreshAccessTokenIfExpired(User $user): void
    {
        if (!$user->hasAccessTokenExpired()) {
            return;
        }

        try {
            $data = $this->googleClient->refreshToken($user->getRefreshToken());
        } catch (RequestException $e) {
            throw new GoogleException('Could not refresh the token: ' . $e->getMessage());
        }

        $user
            ->setAccessToken($data['access_token'])
            // ->setAccessTokenExpiresAt(new DateTime(sprintf('+%d seconds', $data['expires_in'])));
            // Driven by the unit tests: time() function is mockable, new DateTime instances are not
            ->setAccessTokenExpiresAt(DateTime::createFromFormat('U', time() + $data['expires_in'])
                ->setTimezone(new DateTimeZone('UTC'))
            );
        $this->em->flush();
    }

    public function revoke(User $user): void
    {
        try {
            $this->googleClient->revokeToken($user->getAccessToken());
            $this->googleClient->revokeToken($user->getRefreshToken());
        } catch (RequestException $e) {
            $this->logger->warning('Could not revoke at least one token: ' . $e->getMessage());
        }

        $user->setRevoked(true);
        $this->em->flush();
    }
}
