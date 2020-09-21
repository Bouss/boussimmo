<?php

namespace App\Tests\Service;

use App\Entity\User;
use App\Exception\GoogleApiException;
use App\Service\GoogleOAuthService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Google_Client;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Bridge\PhpUnit\ClockMock;

class GoogleOAuthServiceTest extends TestCase
{
    use ProphecyTrait;

    /** @var ObjectProphecy|Google_Client  */
    private $googleClient;
    /** @var ObjectProphecy|EntityManagerInterface */
    private $em;

    private GoogleOAuthService $googleOAuthService;

    public function setUp(): void
    {
        $this->googleClient = $this->prophesize(Google_Client::class);
        $this->em = $this->prophesize(EntityManagerInterface::class);

        $this->googleOAuthService = new GoogleOAuthService($this->googleClient->reveal(), $this->em->reveal());
    }

    public function testRefreshAccessTokenIfExpiredRefreshesTheAccessTokenWhenExpired(): void
    {
        ClockMock::withClockMock(true);

        $user = $this->prophesize(User::class);

        // Given
        $user->hasAccessTokenExpired()->willReturn(true);
        $user->getRefreshToken()->willReturn('123456789');

        $this->googleClient->refreshToken(Argument::any())->willReturn([
            'access_token' => '987654321',
            'expires_in' => 3600
        ]);

        $user->setAccessToken(Argument::any())->willReturn($user->reveal());
        $user->setAccessTokenExpiresAt(Argument::any())->willReturn($user->reveal());

        // When
        $this->googleOAuthService->refreshAccessTokenIfExpired($user->reveal());

        // Then
        $this->googleClient->refreshToken('123456789')->shouldBeCalled();
        $user->setAccessToken('987654321')->shouldBeCalled();
        $user->setAccessTokenExpiresAt(DateTime::createFromFormat('U', time() + 3600))->shouldBeCalled();
        $this->em->flush()->shouldBeCalled();

        ClockMock::withClockMock(false);
    }

    public function testRefreshAccessTokenIfExpiredDoesNothingWhenNotExpired(): void
    {
        $user = $this->prophesize(User::class);

        // Given
        $user->hasAccessTokenExpired()->willReturn(false);

        // When
        $this->googleOAuthService->refreshAccessTokenIfExpired($user->reveal());

        // Then
        $this->googleClient->refreshToken(Argument::any())->shouldNotBeCalled();
    }

    public function testRevokeRevokesTheUserToken(): void
    {
        $user = $this->prophesize(User::class);

        // Given
        $user->getAccessToken()->willReturn('123456789');
        $user->getRefreshToken()->willReturn('987654321');
        $this->googleClient->revokeToken(Argument::any())->willReturn(true);
        $user->setRevoked(Argument::any())->willReturn($user->reveal());

        // When
        $this->googleOAuthService->revoke($user->reveal());

        // Then
        $this->googleClient->revokeToken(Argument::that(static function($token) {
            return $token === '123456789' || $token === '987654321';
        }))
            ->shouldBeCalledTimes(2);
        $user->setRevoked(true)->shouldBeCalled();
        $this->em->flush()->shouldBeCalled();
    }

    public function testRevokeThrowAnExceptionWhenGoogleApiCallFails(): void
    {
        $user = $this->prophesize(User::class);

        // Given
        $user->getAccessToken()->willReturn('123456789');
        $user->getRefreshToken()->willReturn('987654321');
        $this->googleClient->revokeToken('123456789')->willReturn(true);
        $this->googleClient->revokeToken('987654321')->willReturn(false);

        // Then
        $this->expectException(GoogleApiException::class);

        // When
        $this->googleOAuthService->revoke($user->reveal());
    }
}
