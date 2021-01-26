<?php

namespace App\Tests\Client;

use App\Client\GmailClient;
use App\DataProvider\EmailTemplateProvider;
use Generator;
use Google_Client;
use Google_Service_Gmail;
use Google_Service_Gmail_ListMessagesResponse;
use Google_Service_Gmail_Resource_UsersMessages;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Log\LoggerInterface;

class GmailClientTest extends TestCase
{
    use ProphecyTrait;

    /** @var ObjectProphecy|Google_Service_Gmail */
    private $gmailService;
    /** @var ObjectProphecy|EmailTemplateProvider */
    private $emailTemplateProvider;
    /** @var ObjectProphecy|LoggerInterface */
    private $logger;

    private GmailClient $gmailClient;

    public function setUp(): void
    {
        $this->gmailService = $this->prophesize(Google_Service_Gmail::class);
        $this->emailTemplateProvider = $this->prophesize(EmailTemplateProvider::class);
        $this->logger = $this->prophesize(LoggerInterface::class);

        $this->gmailClient = new GmailClient(
            $this->gmailService->reveal(),
            $this->emailTemplateProvider->reveal(),
            $this->logger->reveal()
        );
    }

    /**
     * @dataProvider queryDataset
     *
     * @param array $criteria
     * @param array $expectedParams
     */
    public function testBuildMessagesQueryCreatesCorrectQueryParams(array $criteria, array $expectedParams): void
    {
        $googleClient = $this->prophesize(Google_Client::class);
        $usersMessages = $this->prophesize(Google_Service_Gmail_Resource_UsersMessages::class);
        $response = $this->prophesize(Google_Service_Gmail_ListMessagesResponse::class);

        // Given
        $this->gmailService->getClient()->willReturn($googleClient->reveal());

        // private function buildMessagesQuery()
        if (isset($criteria['provider'])) {
            $this->emailTemplateProvider->getAddressesByMainProvider(Argument::any())->willReturn(['foo@mail.com']);
        } else {
            $this->emailTemplateProvider->getAllAddresses()->willReturn(['foo@mail.com', 'bar@mail.com', 'qux@mail.com']);
        }

        $this->gmailService->users_messages = $usersMessages->reveal();
        $usersMessages->listUsersMessages(Argument::cetera())->willReturn($response->reveal());
        $response->getMessages()->willReturn([]);
        $response->getNextPageToken()->willReturn(null);

        // When
        $this->gmailClient->getMessages($criteria, '123456789');

        // Then
        $usersMessages->listUsersMessages('me', $expectedParams)->shouldBeCalled();
    }

    public function queryDataset(): Generator
    {
        yield [
            ['newer_than' => 42],
            ['q' => 'from:(foo@mail.com | bar@mail.com | qux@mail.com) newer_than:42d']
        ];
        yield [
            ['gmail_label' => 'work', 'provider' => 'foo', 'newer_than' => 42],
            ['q' => 'from:(foo@mail.com) newer_than:42d', 'labelIds' => ['work']]
        ];
    }
}
