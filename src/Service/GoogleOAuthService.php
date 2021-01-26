<?php

namespace App\Service;

use App\Entity\User;
use App\Exception\GoogleException;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Google_Client;
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
    public function refreshAccessTokenIfExpired(User $user): string
    {
        $this->googleClient->setAccessToken([
            'access_token' => $user->getAccessToken(),
            'expires_in' => $user->getAccessTokenExpiresAt()->getTimestamp() - time(),
            'created' => $user->getAccessTokenCreatedAt()->getTimestamp()
        ]);

        if (!$this->googleClient->isAccessTokenExpired()) {
            return $user->getAccessToken();
        }

        try {
            $creds = $this->googleClient->fetchAccessTokenWithRefreshToken($user->getRefreshToken());
        } catch (Exception $e) {
            throw new GoogleException('Could not refresh the token: ' . $e->getMessage());
        }

        $user
            ->setAccessToken($creds['access_token'])
            ->setAccessTokenCreatedAt(new DateTime('@' . $creds['created']))
            // ->setAccessTokenExpiresAt(new DateTime(sprintf('+%d seconds', $data['expires_in'])));
            // Driven by the unit tests: time() function is mockable, new DateTime objects are not
            ->setAccessTokenExpiresAt(DateTime::createFromFormat('U', time() + $creds['expires_in'])
            );

        $this->em->flush();

        return $creds['access_token'];
    }

    public function revoke(User $user): void
    {
        try {
            $this->googleClient->revokeToken($user->getAccessToken());
            $this->googleClient->revokeToken($user->getRefreshToken());
        } catch (Exception $e) {
            $this->logger->warning('Could not revoke at least one token: ' . $e->getMessage());
        }

        $user->setRevokedAt(new DateTime());
        $this->em->flush();
    }
}
