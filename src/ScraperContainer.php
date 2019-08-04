<?php

namespace App;

use App\Definition\SiteEnum;
use App\Exception\ScraperLocatorException;
use App\Scraper\AbstractScraper;
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
            SiteEnum::LOGIC_IMMO => LogicImmoScraper::class,
            SiteEnum::OUESTFRANCE_IMMO => OuestFranceImmoScraper::class,
            SiteEnum::SELOGER => SeLogerScraper::class,
        ];
    }

    /**
     * @param string $id
     *
     * @return AbstractScraper
     *
     * @throws ScraperLocatorException
     */
    public function get(string $id): AbstractScraper
    {
        if (!$this->locator->has($id)) {
            throw new ScraperLocatorException(sprintf('No scraper found with the id: "%s"', $id));
        }

        return $this->locator->get($id);
    }
}
