<?php

namespace App\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Security\Authenticator\SocialAuthenticator;
use KnpU\OAuth2ClientBundle\Client\Provider\GoogleClient;
use League\OAuth2\Client\Provider\GoogleUser;
use League\OAuth2\Client\Token\AccessToken;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class GoogleAuthenticator extends SocialAuthenticator
{
    /**
     * @var ClientRegistry
     */
    private $clientRegistry;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @param ClientRegistry         $clientRegistry
     * @param EntityManagerInterface $em
     * @param RouterInterface        $router
     */
    public function __construct(ClientRegistry $clientRegistry, EntityManagerInterface $em, RouterInterface $router)
    {
        $this->clientRegistry = $clientRegistry;
        $this->em = $em;
        $this->router = $router;
    }

    /**
     * {@inheritDoc}
     */
    public function supports(Request $request): bool
    {
        return $request->attributes->get('_route') === 'connect_google_check';
    }

    /**
     * {@inheritDoc}
     */
    public function getCredentials(Request $request)
    {
        return $this->fetchAccessToken($this->getGoogleClient());
    }

    /**
     * {@inheritDoc}
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        /** @var AccessToken $accessToken */
        $accessToken = $credentials;
        /** @var GoogleUser $googleUser */
        $googleUser = $this->getGoogleClient()->fetchUserFromToken($accessToken);

        $user = $this->em->getRepository(User::class)->findOneBy(['googleId' => $googleUser->getId()]);

        if (null !== $user) {
            $user->setAccessToken($accessToken);
        } else {
            $user = $this->em->getRepository(User::class)->findOneBy(['email' => $googleUser->getEmail()]);

            if (null === $user) {
                $user = (new User())->setEmail($googleUser->getEmail());
                $this->em->persist($user);
            }

            $user->setGoogleId($googleUser->getId());
            $user->setAccessToken($accessToken);
            $user->setProfileImage($googleUser->getAvatar());
        }

        $this->em->flush();

        return $user;
    }

    /**
     * {@inheritDoc}
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey): ?Response
    {
        return new RedirectResponse($this->router->generate('property_ad_index'));
    }

    /**
     * {@inheritDoc}
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $message = strtr($exception->getMessageKey(), $exception->getMessageData());

        return new Response($message, Response::HTTP_FORBIDDEN);
    }

    /**
     * {@inheritDoc}
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        return new RedirectResponse('/connect/', Response::HTTP_TEMPORARY_REDIRECT);
    }

    /**
     * @return GoogleClient
     */
    private function getGoogleClient(): GoogleClient
    {
        return $this->clientRegistry->getClient('google');
    }
}
