<?php

namespace App\Command;

use App\Client\EmailClient;
use App\Definition\SiteEnum;
use App\EmailParserContainer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ScrapEmailsCommand extends Command
{
    protected static $defaultName = 'app:scrap-emails';

    /**
     * @var EmailClient
     */
    private $emailClient;

    /**
     * @var EmailParserContainer
     */
    private $parserContainer;

    /**
     * @param EmailClient          $emailClient
     * @param EmailParserContainer $parserContainer
     */
    public function __construct(EmailClient $emailClient, EmailParserContainer $parserContainer)
    {
        $this->emailClient = $emailClient;
        $this->parserContainer = $parserContainer;

        parent::__construct();
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $ads = [];
        $mails = $this->emailClient->getMails(SiteEnum::LOGIC_IMMO, '-14 days');

        $parser = $this->parserContainer->get(SiteEnum::LOGIC_IMMO);

        foreach ($mails as $mail) {
            $ads[] = $parser->parse($mail->textHtml);
        }

        dump($ads); die;
    }
}
