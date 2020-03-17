<?php

namespace App;

use App\Enum\Provider;
use App\Exception\ScraperNotFoundException;
use App\Scraper\AbstractScraper;
use App\Scraper\LeBoinCoinScraper;
use App\Scraper\LogicImmoScraper;
use App\Scraper\OuestFranceImmoScraper;
use App\Scraper\SeLogerScraper;
use Psr\Container\ContainerInterface;
use Symfony\Contracts\Service\ServiceSubscriberInterface;

class ScraperContainer implements ServiceSubscriberInterface
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
            Provider::LEBONCOIN        => LeBoinCoinScraper::class,
            Provider::LOGIC_IMMO       => LogicImmoScraper::class,
            Provider::OUESTFRANCE_IMMO => OuestFranceImmoScraper::class,
            Provider::SELOGER          => SeLogerScraper::class,
        ];
    }

    /**
     * @param string $id
     *
     * @return AbstractScraper
     *
     * @throws ScraperNotFoundException
     */
    public function get(string $id): AbstractScraper
    {
        if (!$this->locator->has($id)) {
            throw new ScraperNotFoundException(sprintf('No scraper found with the id: "%s"', $id));
        }

        return $this->locator->get($id);
    }
}
