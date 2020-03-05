<?php

namespace App\Service;

use App\Entity\User;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Google_Client;

class GoogleService
{
    /**
     * @var Google_Client
     */
    private $googleClient;

    /**
     * @var EntityManagerInterface
     */
    private $em;

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
    public function setUserAccessTokenIfExpired(User $user): void
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
}
