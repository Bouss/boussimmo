<?php

namespace App\EventListener;

use App\Entity\User;
use App\Exception\GoogleApiException;
use App\Service\GoogleService;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Event\LogoutEvent;

class LogoutListener
{
    private GoogleService $googleService;

    /**
     * @param GoogleService $googleService
     */
    public function __construct(GoogleService $googleService)
    {
        $this->googleService = $googleService;
    }

    /**
     * @param LogoutEvent $event
     *
     * @throws GoogleApiException
     */
    public function onLogout(LogoutEvent $event): void
    {
        /** @var TokenInterface $token */
        $token = $event->getToken();
        /** @var User $user */
        $user = $token->getUser();

        $this->googleService->revoke($user);
    }
}
