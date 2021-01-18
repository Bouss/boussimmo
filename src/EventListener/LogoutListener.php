<?php

namespace App\EventListener;

use App\Entity\User;
use App\Exception\GoogleException;
use App\Service\GoogleOAuthService;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Event\LogoutEvent;

class LogoutListener
{
    private GoogleOAuthService $oAuthService;

    /**
     * @param GoogleOAuthService $oAuthService
     */
    public function __construct(GoogleOAuthService $oAuthService)
    {
        $this->oAuthService = $oAuthService;
    }

    /**
     * @param LogoutEvent $event
     *
     * @throws GoogleException
     */
    public function onLogout(LogoutEvent $event): void
    {
        /** @var TokenInterface $token */
        $token = $event->getToken();
        /** @var User $user */
        $user = $token->getUser();

        $this->oAuthService->revoke($user);
    }
}
