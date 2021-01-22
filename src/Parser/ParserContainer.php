<?php

namespace App\Parser;

use App\Enum\EmailTemplate;
use App\Exception\ParserNotFoundException;
use Psr\Container\ContainerInterface;
use Symfony\Contracts\Service\ServiceSubscriberInterface;

class ParserContainer implements ServiceSubscriberInterface
{
    public function __construct(
        private ContainerInterface $locator
    ) {}

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedServices(): array
    {
        return [
            EmailTemplate::BIENICI               => BienIciParser::class,
            EmailTemplate::LEBONCOIN             => LeBonCoinParser::class,
            EmailTemplate::LOGIC_IMMO            => LogicImmoParser::class,
            EmailTemplate::LOGIC_IMMO_PARTNER    => LogicImmoPartnerParser::class,
            EmailTemplate::LOGIC_IMMO_NEUF       => LogicImmoNeufParser::class,
            EmailTemplate::OUESTFRANCE_IMMO      => OuestFranceImmoParser::class,
            EmailTemplate::OUESTFRANCE_IMMO_2    => OuestFranceImmo2Parser::class,
            EmailTemplate::OUESTFRANCE_IMMO_NEUF => OuestFranceImmoNeufParser::class,
            EmailTemplate::PAP                   => PapParser::class,
            EmailTemplate::PAP_NEUF              => PapNeufParser::class,
            EmailTemplate::SELOGER               => SeLogerParser::class,
            EmailTemplate::SELOGER_PARTNER       => SeLogerPartnerParser::class,
            EmailTemplate::SELOGER_NEUF          => SeLogerNeufParser::class,
            EmailTemplate::SUPERIMMO             => SuperimmoParser::class,
            EmailTemplate::SUPERIMMO_NEUF        => SuperimmoNeufParser::class
        ];
    }

    /**
     * @throws ParserNotFoundException
     */
    public function get(string $provider): ParserInterface
    {
        if (!$this->locator->has($provider)) {
            throw new ParserNotFoundException('No parser found for the provider: ' . $provider);
        }

        return $this->locator->get($provider);
    }
}
