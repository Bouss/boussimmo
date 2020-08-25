<?php

namespace App\Service;

use App\Entity\User;
use App\Exception\GoogleApiException;
use DateTime;
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
            ->setAccessTokenExpiresAt(new DateTime(sprintf('+%d seconds', $data['expires_in'])));
        $this->em->flush();
    }

    /**
     * @param User $user
     *
     * @throws GoogleApiException
     */
    public function revoke(User $user): void
    {
        $revoked = $this->googleClient->revokeToken($user->getRefreshToken());

        if (!$revoked) {
            throw new GoogleApiException('User access revoking failed');
        }

        $user->setRevoked(true);
        $this->em->flush();
    }
}
