<?php

namespace App;

use App\Definition\SiteEnum;
use App\Exception\ParserNotFoundException;
use App\Parser\AbstractParser;
use App\Parser\EmailParser\BienIciParser;
use App\Parser\EmailParser\FnaimParser;
use App\Parser\EmailParser\LeBonCoinParser;
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
            SiteEnum::BIENICI => BienIciParser::class,
            SiteEnum::LEBONCOIN => LeBonCoinParser::class,
            SiteEnum::FNAIM => FnaimParser::class,
            SiteEnum::LOGIC_IMMO => LogicImmoParser::class,
            SiteEnum::OUESTFRANCE_IMMO => OuestFranceImmoParser::class,
            SiteEnum::SELOGER => SeLogerParser::class,
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
