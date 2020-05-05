<?php

namespace App;

use App\Enum\Provider;
use App\Exception\ParserNotFoundException;
use App\UrlBuilder\BienIciUrlBuilder;
use App\UrlBuilder\LeBonCoinUrlBuilder;
use App\UrlBuilder\LogicImmoNeufUrlBuilder;
use App\UrlBuilder\LogicImmoUrlBuilder;
use App\UrlBuilder\OuestFranceImmoNeufUrlBuilder;
use App\UrlBuilder\OuestFranceImmoUrlBuilder;
use App\UrlBuilder\PapNeufUrlBuilder;
use App\UrlBuilder\PapUrlBuilder;
use App\UrlBuilder\SeLogerNeufUrlBuilder;
use App\UrlBuilder\SeLogerUrlBuilder;
use App\UrlBuilder\SuperimmoNeufUrlBuilder;
use App\UrlBuilder\SuperimmoUrlBuilder;
use App\UrlBuilder\UrlBuilderInterface;
use Psr\Container\ContainerInterface;
use Symfony\Contracts\Service\ServiceSubscriberInterface;

class UrlBuilderContainer implements ServiceSubscriberInterface
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
     * @param string $id
     *
     * @return UrlBuilderInterface
     *
     * @throws ParserNotFoundException
     */
    public function get(string $id): UrlBuilderInterface
    {
        if (!$this->locator->has($id)) {
            throw new ParserNotFoundException(sprintf('No parser found with the id: "%s"', $id));
        }

        return $this->locator->get($id);
    }
}
