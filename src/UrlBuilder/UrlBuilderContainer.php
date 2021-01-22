<?php

namespace App\UrlBuilder;

use App\Enum\Provider;
use App\Exception\UrlBuilderNotFoundException;
use Psr\Container\ContainerInterface;
use Symfony\Contracts\Service\ServiceSubscriberInterface;

class UrlBuilderContainer implements ServiceSubscriberInterface
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
            Provider::BIENICI               => BienIciUrlBuilder::class,
            Provider::LEBONCOIN             => LeBonCoinUrlBuilder::class,
            Provider::LOGIC_IMMO            => LogicImmoUrlBuilder::class,
            Provider::LOGIC_IMMO_NEUF       => LogicImmoNeufUrlBuilder::class,
            Provider::OUESTFRANCE_IMMO      => OuestFranceImmoUrlBuilder::class,
            Provider::OUESTFRANCE_IMMO_NEUF => OuestFranceImmoNeufUrlBuilder::class,
            Provider::PAP                   => PapUrlBuilder::class,
            Provider::PAP_NEUF              => PapNeufUrlBuilder::class,
            Provider::SELOGER               => SeLogerUrlBuilder::class,
            Provider::SELOGER_NEUF          => SeLogerNeufUrlBuilder::class,
            Provider::SUPERIMMO             => SuperimmoUrlBuilder::class,
            Provider::SUPERIMMO_NEUF        => SuperimmoNeufUrlBuilder::class
        ];
    }

    /**
     * @throws UrlBuilderNotFoundException
     */
    public function get(string $provider): UrlBuilderInterface
    {
        if (!$this->locator->has($provider)) {
            throw new UrlBuilderNotFoundException('No URL builder found for the provider: ' . $provider);
        }

        return $this->locator->get($provider);
    }
}
