<?php

namespace App\Tests\EventListener;

use App\Entity\User;
use App\EventListener\LogoutListener;
use App\Service\GoogleOAuthService;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Event\LogoutEvent;

class LogoutListenerTest extends TestCase
{
    use ProphecyTrait;

    /** @var ObjectProphecy|GoogleOAuthService */
    private $oAuthService;

    private LogoutListener $logoutListener;

    public function setUp(): void
    {
        $this->oAuthService = $this->prophesize(GoogleOAuthService::class);

        $this->logoutListener = new LogoutListener($this->oAuthService->reveal());
    }

    public function testOnLogoutRevokesTheUserTokens(): void
    {
        $event = $this->prophesize(LogoutEvent::class);
        $token = $this->prophesize(TokenInterface::class);
        $user = $this->prophesize(User::class);

        // Given
        $token->getUser()->willReturn($user->reveal());
        $event->getToken()->willReturn($token->reveal());

        // When
        $this->logoutListener->onLogout($event->reveal());

        // Then
        $this->oAuthService->revoke($user->reveal())->shouldBeCalled();
    }
}
