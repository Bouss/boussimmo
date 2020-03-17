<?php

namespace App;

use App\Enum\EmailTemplate;
use App\Exception\ParserNotFoundException;
use App\Parser\AbstractParser;
use App\Parser\EmailParser\BienIciParser;
use App\Parser\EmailParser\FnaimParser;
use App\Parser\EmailParser\LeBonCoinParser;
use App\Parser\EmailParser\LogicImmo2Parser;
use App\Parser\EmailParser\LogicImmoNeufParser;
use App\Parser\EmailParser\LogicImmoParser;
use App\Parser\EmailParser\OuestFranceImmoNeufParser;
use App\Parser\EmailParser\OuestFranceImmoParser;
use App\Parser\EmailParser\PapParser;
use App\Parser\EmailParser\SeLoger2Parser;
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
            EmailTemplate::BIENICI               => BienIciParser::class,
            EmailTemplate::LEBONCOIN             => LeBonCoinParser::class,
            EmailTemplate::FNAIM                 => FnaimParser::class,
            EmailTemplate::LOGIC_IMMO            => LogicImmoParser::class,
            EmailTemplate::LOGIC_IMMO_2          => LogicImmo2Parser::class,
            EmailTemplate::LOGIC_IMMO_NEUF       => LogicImmoNeufParser::class,
            EmailTemplate::OUESTFRANCE_IMMO      => OuestFranceImmoParser::class,
            EmailTemplate::OUESTFRANCE_IMMO_NEUF => OuestFranceImmoNeufParser::class,
            EmailTemplate::PAP                   => PapParser::class,
            EmailTemplate::SELOGER               => SeLogerParser::class,
            EmailTemplate::SELOGER_2             => SeLoger2Parser::class
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
