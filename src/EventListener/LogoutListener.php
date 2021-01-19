<?php

namespace App\EventListener;

use App\Entity\User;
use App\Service\GoogleOAuthService;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Event\LogoutEvent;

class LogoutListener
{
    public function __construct(
        private GoogleOAuthService $oAuthService
    ) {}

    public function onLogout(LogoutEvent $event): void
    {
        /** @var TokenInterface $token */
        $token = $event->getToken();
        /** @var User $user */
        $user = $token->getUser();

        $this->oAuthService->revoke($user);
    }
}
