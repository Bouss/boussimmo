<?php

namespace App;

use App\Definition\MailProviderEnum;
use App\Exception\ParserNotFoundException;
use App\Parser\AbstractParser;
use App\Parser\EmailParser\BienIciParser;
use App\Parser\EmailParser\FnaimParser;
use App\Parser\EmailParser\LeBonCoinParser;
use App\Parser\EmailParser\LogicImmoNeufParser;
use App\Parser\EmailParser\LogicImmoParser;
use App\Parser\EmailParser\OuestFranceImmoParser;
use App\Parser\EmailParser\SeLogerParser;
use Psr\Container\ContainerInterface;
use Symfony\Contracts\Service\ServiceSubscriberInterface;

class EmailParserContainer implements ServiceSubscriberInterface
{
    /**
     * @var ContainerInterface
     */
    private $locator;

    /**
     * @param ContainerInterface $locator
     */
    public function __construct(ContainerInterface $locator)
    {
        $this->locator = $locator;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedServices(): array
    {
        return [
            MailProviderEnum::BIENICI => BienIciParser::class,
            MailProviderEnum::LEBONCOIN => LeBonCoinParser::class,
            MailProviderEnum::FNAIM => FnaimParser::class,
            MailProviderEnum::LOGIC_IMMO => LogicImmoParser::class,
            MailProviderEnum::LOGIC_IMMO_NEUF => LogicImmoNeufParser::class,
            MailProviderEnum::OUESTFRANCE_IMMO => OuestFranceImmoParser::class,
            MailProviderEnum::SELOGER => SeLogerParser::class,
        ];
    }

    /**
     * @param string $id
     *
     * @return AbstractParser
     *
     * @throws ParserNotFoundException
     */
    public function get(string $id): AbstractParser
    {
        if (!$this->locator->has($id)) {
            throw new ParserNotFoundException(sprintf('No parser found with the id: "%s"', $id));
        }

        return $this->locator->get($id);
    }
}
