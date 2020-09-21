<?php

namespace App\Service;

use App\Entity\User;
use App\Exception\GoogleApiException;
use DateTime;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;
use Google_Client;

class GoogleOAuthService
{
    private Google_Client $googleClient;
    private EntityManagerInterface $em;

    /**
     * @param Google_Client          $googleClient
     * @param EntityManagerInterface $em
     */
    public function __construct(Google_Client $googleClient, EntityManagerInterface $em)
    {
        $this->googleClient = $googleClient;
        $this->em = $em;
    }

    /**
     * @param User $user
     */
    public function refreshAccessTokenIfExpired(User $user): void
    {
        if (!$user->hasAccessTokenExpired()) {
            return;
        }

        $data = $this->googleClient->refreshToken($user->getRefreshToken());
        $user
            ->setAccessToken($data['access_token'])
            // ->setAccessTokenExpiresAt(new DateTime(sprintf('+%d seconds', $data['expires_in'])));
            // Driven by the unit tests: time() function is mockable, new DateTime instances are not
            ->setAccessTokenExpiresAt(DateTime::createFromFormat('U', time() + $data['expires_in'])
                ->setTimezone(new DateTimeZone('UTC'))
            );
        $this->em->flush();
    }

    /**
     * @param User $user
     *
     * @throws GoogleApiException
     */
    public function revoke(User $user): void
    {
        $accessTokenRevoked = $this->googleClient->revokeToken($user->getAccessToken());
        $refreshTokenRevoked = $this->googleClient->revokeToken($user->getRefreshToken());

        if (!($accessTokenRevoked && $refreshTokenRevoked)) {
            throw new GoogleApiException('User access revoking failed');
        }

        $user->setRevoked(true);
        $this->em->flush();
    }
}
