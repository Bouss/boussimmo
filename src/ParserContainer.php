<?php

namespace App;

use App\Enum\EmailTemplate;
use App\Exception\ParserNotFoundException;
use App\Parser\BienIciParser;
use App\Parser\LeBonCoinParser;
use App\Parser\LogicImmo2Parser;
use App\Parser\LogicImmoNeufParser;
use App\Parser\LogicImmoParser;
use App\Parser\OuestFranceImmoNeuf2Parser;
use App\Parser\OuestFranceImmoNeufParser;
use App\Parser\OuestFranceImmoParser;
use App\Parser\PapParser;
use App\Parser\ParserInterface;
use App\Parser\SeLoger2Parser;
use App\Parser\SeLogerNeufParser;
use App\Parser\SeLogerParser;
use App\Parser\SuperimmoNeufParser;
use App\Parser\SuperimmoParser;
use Psr\Container\ContainerInterface;
use Symfony\Contracts\Service\ServiceSubscriberInterface;

class ParserContainer implements ServiceSubscriberInterface
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
            EmailTemplate::BIENICI                 => BienIciParser::class,
            EmailTemplate::LEBONCOIN               => LeBonCoinParser::class,
            EmailTemplate::LOGIC_IMMO              => LogicImmoParser::class,
            EmailTemplate::LOGIC_IMMO_2            => LogicImmo2Parser::class,
            EmailTemplate::LOGIC_IMMO_NEUF         => LogicImmoNeufParser::class,
            EmailTemplate::OUESTFRANCE_IMMO        => OuestFranceImmoParser::class,
            EmailTemplate::OUESTFRANCE_IMMO_NEUF   => OuestFranceImmoNeufParser::class,
            EmailTemplate::OUESTFRANCE_IMMO_NEUF_2 => OuestFranceImmoNeuf2Parser::class,
            EmailTemplate::PAP                     => PapParser::class,
            EmailTemplate::SELOGER                 => SeLogerParser::class,
            EmailTemplate::SELOGER_2               => SeLoger2Parser::class,
            EmailTemplate::SELOGER_NEUF            => SeLogerNeufParser::class,
            EmailTemplate::SUPERIMMO               => SuperimmoParser::class,
            EmailTemplate::SUPERIMMO_NEUF          => SuperimmoNeufParser::class
        ];
    }

    /**
     * @param string $id
     *
     * @return ParserInterface
     *
     * @throws ParserNotFoundException
     */
    public function get(string $id): ParserInterface
    {
        if (!$this->locator->has($id)) {
            throw new ParserNotFoundException(sprintf('No parser found with the id: "%s"', $id));
        }

        return $this->locator->get($id);
    }
}
