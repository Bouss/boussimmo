<?php

namespace App\Tests\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Security\GoogleAuthenticator;
use DateTime;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Client\OAuth2ClientInterface;
use League\OAuth2\Client\Provider\GoogleUser;
use League\OAuth2\Client\Token\AccessToken;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class GoogleAuthenticatorTest extends TestCase
{
    use ProphecyTrait;

    /** @var ObjectProphecy|ClientRegistry */
    private $clientRegistry;

    /** @var ObjectProphecy|EntityManagerInterface */
    private $em;

    /** @var ObjectProphecy|RouterInterface */
    private $router;

    /** @var ObjectProphecy|UserRepository */
    private $userRepository;

    private GoogleAuthenticator $googleAuthenticator;

    public function setUp(): void
    {
        $this->clientRegistry = $this->prophesize(ClientRegistry::class);
        $this->em = $this->prophesize(EntityManagerInterface::class);
        $this->router = $this->prophesize(RouterInterface::class);
        $this->userRepository = $this->prophesize(UserRepository::class);

        $this->googleAuthenticator = new GoogleAuthenticator(
            $this->clientRegistry->reveal(),
            $this->em->reveal(),
            $this->router->reveal(),
            $this->userRepository->reveal()
        );
    }

    public function testGetUserCreatesAUser(): void
    {
        $accessToken = $this->prophesize(AccessToken::class);
        $googleUser = $this->prophesize(GoogleUser::class);
        $oAuth2Client = $this->prophesize(OAuth2ClientInterface::class);

        // Given
        $accessToken->__toString()->willReturn('12346789');
        $accessToken->getRefreshToken()->willReturn('987654321');
        $accessToken->getExpires()->willReturn(715795200);
        $googleUser->getId()->willReturn('42');
        $googleUser->getEmail()->willReturn('dave.loper@mail.com');
        $googleUser->getAvatar()->willReturn('avatar.jpg');

        $this->clientRegistry->getClient('google')->willReturn($oAuth2Client->reveal());
        $oAuth2Client->fetchUserFromToken($accessToken->reveal())->willReturn($googleUser->reveal());
        $this->userRepository->findOneBy(['googleId' => 42])->willReturn(null);

        // When
        $user = $this->googleAuthenticator->getUser(
            $accessToken->reveal(),
            $this->prophesize(UserProviderInterface::class)->reveal()
        );

        // Then
        $this->em->persist(Argument::that(static function ($user) {
            return
                $user instanceof User &&
                $user->getGoogleId() === '42' &&
                $user->getEmail() === 'dave.loper@mail.com' &&
                $user->getAvatar() === 'avatar.jpg' &&
                $user->getRefreshToken() === '987654321' &&
                $user->isRevoked() === false &&
                $user->getRoles() === ['ROLE_USER'] &&
                $user->getPropertySearchSettings() === [];
        }))
            ->shouldBeCalled();

        $this->em->flush()->shouldBeCalled();

        self::assertEquals('12346789', $user->getAccessToken());
        self::assertEquals(new DateTime('1992-09-06 18:00:00', new DateTimeZone('Europe/Paris')), $user->getAccessTokenExpiresAt());
    }

    public function testGetUserUpdatesAUserButDoesNotEraseTheCurrentRefreshTokenWhenNoNewOneIsProvided(): void
    {
        $userMock = $this->prophesize(User::class);
        $accessToken = $this->prophesize(AccessToken::class);
        $googleUser = $this->prophesize(GoogleUser::class);
        $oAuth2Client = $this->prophesize(OAuth2ClientInterface::class);

        // Given
        $userMock->getRefreshToken()->willReturn('987654321');
        $accessToken->__toString()->willReturn('12346789');
        $accessToken->getRefreshToken()->willReturn(null);
        $accessToken->getExpires()->willReturn(715802400);
        $googleUser->getId()->willReturn('42');
        $googleUser->getEmail()->willReturn('dave.loper@mail.com');
        $googleUser->getAvatar()->willReturn('avatar.jpg');

        $this->clientRegistry->getClient('google')->willReturn($oAuth2Client->reveal());
        $oAuth2Client->fetchUserFromToken($accessToken->reveal())->willReturn($googleUser->reveal());
        $this->userRepository->findOneBy(['googleId' => 42])->willReturn($userMock->reveal());

        $userMock->setRevoked(false)->willReturn($userMock->reveal());
        $userMock->setAccessToken('12346789')->willReturn($userMock->reveal());
        $userMock->setAccessTokenExpiresAt(Argument::type(DateTime::class))->willReturn($userMock->reveal());

        // When
        $user = $this->googleAuthenticator->getUser(
            $accessToken->reveal(),
            $this->prophesize(UserProviderInterface::class)->reveal()
        );

        // Then
        $userMock->setRefreshToken(Argument::any())->shouldNotBeCalled();
        self::assertEquals('987654321', $user->getRefreshToken());
    }
}
