<?php

namespace App\Tests\Client;

use App\Client\GmailClient;
use App\DataProvider\EmailTemplateProvider;
use App\Exception\GmailException;
use Generator;
use Google_Client;
use Google_Service_Gmail;
use Google_Service_Gmail_ListMessagesResponse;
use Google_Service_Gmail_Resource_UsersMessages;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

class GmailClientTest extends TestCase
{
    use ProphecyTrait;

    private ObjectProphecy|Google_Service_Gmail $gmailService;
    private ObjectProphecy|EmailTemplateProvider $emailTemplateProvider;
    private GmailClient $gmailClient;

    public function setUp(): void
    {
        $this->gmailService = $this->prophesize(Google_Service_Gmail::class);
        $this->emailTemplateProvider = $this->prophesize(EmailTemplateProvider::class);

        $this->gmailClient = new GmailClient($this->gmailService->reveal(), $this->emailTemplateProvider->reveal());
    }

    /**
     * @dataProvider queryDataset
     *
     * @throws GmailException
     */
    public function test_build_messages_query_creates_correct_query_params(array $input, array $expected): void
    {
        $googleClient = $this->prophesize(Google_Client::class);
        $usersMessages = $this->prophesize(Google_Service_Gmail_Resource_UsersMessages::class);
        $response = $this->prophesize(Google_Service_Gmail_ListMessagesResponse::class);

        // Given
        $this->gmailService->getClient()->willReturn($googleClient->reveal());

        // private function buildMessagesQuery()
        if (isset($input['criteria']['provider'])) {
            $this->emailTemplateProvider->getAddressesByMainProvider(Argument::any())->willReturn(['foo@mail.com']);
        } else {
            $this->emailTemplateProvider->getAllAddresses()->willReturn(['foo@mail.com', 'bar@mail.com', 'qux@mail.com']);
        }

        $this->gmailService->users_messages = $usersMessages->reveal();
        $usersMessages->listUsersMessages(Argument::cetera())->willReturn($response->reveal());
        $response->getMessages()->willReturn([]);
        $response->getNextPageToken()->willReturn(null);

        // When
        $this->gmailClient->getMessages($input['criteria'], '123456789');

        // Then
        $usersMessages->listUsersMessages('me', $expected['query_params'])->shouldBeCalled();
    }

    public function queryDataset(): Generator
    {
        yield 'only "newer_than" filled' => [
            'input' => [
                'criteria' => [
                    'newer_than' => 42
                ]
            ],
            'expected' => [
                'query_params' => [
                    'q' => 'from:(foo@mail.com | bar@mail.com | qux@mail.com) newer_than:42d'
                ]
            ]
        ];
        yield '"newer_than", "gmail_label" and "provider" filled' => [
            'input' => [
                'criteria' => [
                    'gmail_label' => 'work',
                    'provider' => 'foo',
                    'newer_than' => 42
                ]
            ],
            'expected' => [
                'query_params' => [
                    'q' => 'from:(foo@mail.com) newer_than:42d',
                    'labelIds' => ['work']
                ]
            ]
        ];
    }
}
