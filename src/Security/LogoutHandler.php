<?php

namespace App\Security;

use App\Entity\User;
use App\Exception\GoogleApiException;
use App\Service\GoogleService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Logout\LogoutHandlerInterface;

class LogoutHandler implements LogoutHandlerInterface
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
     * {@inheritDoc}
     *
     * @throws GoogleApiException
     */
    public function logout(Request $request, Response $response, TokenInterface $token): void
    {
        /** @var User $user */
        $user = $token->getUser();

        $this->googleService->revoke($user);
    }
}
