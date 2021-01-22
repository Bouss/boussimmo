<?php

namespace App\Tests\Service;

use App\Entity\User;
use App\Exception\GoogleException;
use App\Service\GoogleOAuthService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Google_Client;
use GuzzleHttp\Exception\ClientException;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\PhpUnit\ClockMock;

class GoogleOAuthServiceTest extends TestCase
{
    use ProphecyTrait;

    /** @var ObjectProphecy|Google_Client  */
    private $googleClient;
    /** @var ObjectProphecy|EntityManagerInterface */
    private $em;
    /** @var ObjectProphecy|LoggerInterface */
    private $logger;

    private GoogleOAuthService $googleOAuthService;

    public function setUp(): void
    {
        $this->googleClient = $this->prophesize(Google_Client::class);
        $this->em = $this->prophesize(EntityManagerInterface::class);
        $this->logger = $this->prophesize(LoggerInterface::class);

        $this->googleOAuthService = new GoogleOAuthService(
            $this->googleClient->reveal(),
            $this->em->reveal(),
            $this->logger->reveal()
        );
    }

    public function testRefreshAccessTokenIfExpiredRefreshesTheAccessTokenWhenExpired(): void
    {
        ClockMock::withClockMock(true);

        $user = $this->prophesize(User::class);

        // Given
        $user->getAccessToken()->willReturn('123456789');
        $user->getAccessTokenCreatedAt()->willReturn(new DateTime('2021-01-01 9:00:00'));
        $user->getAccessTokenExpiresAt()->willReturn(new DateTime('2021-01-01 10:00:00'));
        $user->getRefreshToken()->willReturn('192837465');

        $this->googleClient->isAccessTokenExpired()->willReturn(true);
        $this->googleClient->fetchAccessTokenWithRefreshToken(Argument::any())->willReturn([
            'access_token' => '987654321',
            'expires_in' => 3600,
            'created' => 1609498800 // 2021-01-01 11:00:00
        ]);

        $user->setAccessToken(Argument::any())->willReturn($user->reveal());
        $user->setAccessTokenCreatedAt(Argument::any())->willReturn($user->reveal());
        $user->setAccessTokenExpiresAt(Argument::any())->willReturn($user->reveal());

        // When
        $accessToken = $this->googleOAuthService->refreshAccessTokenIfExpired($user->reveal());

        // Then
        self::assertEquals('987654321', $accessToken);
        $this->googleClient->setAccessToken([
            'access_token' => '123456789',
            'expires_in' => 1609495200  /* 2021-01-01 10:00:00 */ - time(),
            'created' => 1609491600 // 2021-01-01 9:00:00
        ])
            ->shouldBeCalled();
        $this->googleClient->fetchAccessTokenWithRefreshToken('192837465')->shouldBeCalled();
        $user->setAccessToken('987654321')->shouldBeCalled();
        $user->setAccessTokenCreatedAt(new DateTime('2021-01-01 11:00:00'))->shouldBeCalled();
        $user->setAccessTokenExpiresAt(DateTime::createFromFormat('U', time() + 3600))->shouldBeCalled();
        $this->em->flush()->shouldBeCalled();

        ClockMock::withClockMock(false);
    }

    public function testRefreshAccessTokenIfExpiredDoesNothingWhenNotExpired(): void
    {
        $user = $this->prophesize(User::class);

        // Given
        $user->getAccessToken()->willReturn('123456789');
        $user->getAccessTokenCreatedAt()->willReturn(new DateTime('2021-01-01 9:00:00'));
        $user->getAccessTokenExpiresAt()->willReturn(new DateTime('2021-01-01 10:00:00'));
        $this->googleClient->isAccessTokenExpired()->willReturn(false);

        // When
        $accessToken = $this->googleOAuthService->refreshAccessTokenIfExpired($user->reveal());

        // Then
        self::assertEquals('123456789', $accessToken);
        $this->googleClient->setAccessToken([
            'access_token' => '123456789',
            'expires_in' => 1609495200  /* 2021-01-01 10:00:00 */ - time(),
            'created' => 1609491600 // 2021-01-01 9:00:00
        ])
            ->shouldBeCalled();
        $this->googleClient->fetchAccessTokenWithRefreshToken(Argument::any())->shouldNotBeCalled();
    }

    public function testRevokeRevokesTheUserToken(): void
    {
        $user = $this->prophesize(User::class);

        // Given
        $user->getAccessToken()->willReturn('123456789');
        $user->getRefreshToken()->willReturn('987654321');
        $this->googleClient->revokeToken(Argument::any())->willReturn(true);
        $user->setRevokedAt(Argument::any())->willReturn($user->reveal());

        // When
        $this->googleOAuthService->revoke($user->reveal());

        // Then
        $this->googleClient->revokeToken(Argument::that(static function($token) {
            return $token === '123456789' || $token === '987654321';
        }))
            ->shouldBeCalledTimes(2);
        $user->setRevokedAt(Argument::type(DateTime::class))->shouldBeCalled();
        $this->em->flush()->shouldBeCalled();
    }

    public function testRevokeThrowAnExceptionWhenGoogleApiCallFails(): void
    {
        $user = $this->prophesize(User::class);

        // Given
        $user->getAccessToken()->willReturn('123456789');
        $user->getRefreshToken()->willReturn('987654321');
        $this->googleClient->revokeToken('123456789')->willReturn(true);
        $this->googleClient->revokeToken('987654321')->willThrow(ClientException::class);

        // Then
        $this->logger->warning(Argument::any())->shouldBeCalled();
        $user->setRevokedAt(Argument::type(DateTime::class))->shouldBeCalled();
        $this->em->flush()->shouldBeCalled();

        // When
        $this->googleOAuthService->revoke($user->reveal());
    }
}
